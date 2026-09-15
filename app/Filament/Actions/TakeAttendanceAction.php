<?php

namespace App\Filament\Actions;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Jobs\NotifyParentAbsentStudent;
use App\Models\AttendanceSession;
use App\Models\LeaveRequest;
use App\Models\Student;
use App\Models\StudentAttendance;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;

// Table action shared by the school admin and teacher panels: record or
// correct the attendance of every active student in the session's class in
// one form. Students newly marked "alfa" trigger the parent notification.
class TakeAttendanceAction
{
    public static function make(): Action
    {
        return Action::make('takeAttendance')
            ->label(__('Take Attendance'))
            ->icon('heroicon-o-clipboard-document-check')
            ->color('success')
            ->modalHeading(__('Take Attendance'))
            ->modalWidth('4xl')
            ->fillForm(function (AttendanceSession $record): array {
                $existing = $record->studentAttendances()->get(['student_id', 'status'])->keyBy('student_id');
                $students = self::students($record);

                // Approved leave requests covering the session date pre-fill sick/permission.
                $leaves = LeaveRequest::where('status', 'approved')
                    ->whereIn('student_id', $students->pluck('id'))
                    ->whereDate('start_date', '<=', $record->date)
                    ->whereDate('end_date', '>=', $record->date)
                    ->pluck('type', 'student_id');

                return [
                    'statuses' => $students
                        ->mapWithKeys(fn (Student $student) => [
                            $student->id => $existing->get($student->id)?->status->value
                                ?? match ($leaves->get($student->id)) {
                                    'sakit' => AttendanceStatus::SAKIT->value,
                                    'izin' => AttendanceStatus::IZIN->value,
                                    default => AttendanceStatus::HADIR->value,
                                },
                        ])
                        ->all(),
                ];
            })
            ->form(fn (AttendanceSession $record): array => [
                Forms\Components\Placeholder::make('empty')
                    ->hiddenLabel()
                    ->content(__('No active students in this class.'))
                    ->visible(self::students($record)->isEmpty()),
                Forms\Components\Grid::make(2)->schema(
                    self::students($record)
                        ->map(fn (Student $student) => Forms\Components\Select::make("statuses.{$student->id}")
                            ->label("{$student->full_name} ({$student->nis})")
                            ->options(collect(AttendanceStatus::cases())->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => $status->label()]))
                            ->native()
                            ->required())
                        ->all()
                ),
            ])
            ->action(function (AttendanceSession $record, array $data): void {
                $allowed = self::students($record)->pluck('id')->all();

                foreach ($data['statuses'] ?? [] as $studentId => $status) {
                    $status = AttendanceStatus::tryFrom((string) $status);

                    if (! $status || ! in_array((int) $studentId, $allowed, true)) {
                        continue;
                    }

                    $attendance = StudentAttendance::firstOrNew([
                        'attendance_session_id' => $record->id,
                        'student_id' => (int) $studentId,
                    ]);

                    $wasAbsent = $attendance->exists && $attendance->status === AttendanceStatus::ALFA;

                    $attendance->tenant_id = $record->tenant_id;
                    $attendance->status = $status;

                    if (! $attendance->exists) {
                        $attendance->check_in_time = now();
                        $attendance->method = 'manual';
                    }

                    $attendance->save();

                    if ($status === AttendanceStatus::ALFA && ! $wasAbsent) {
                        NotifyParentAbsentStudent::dispatch($attendance->id);
                    }
                }

                Notification::make()->success()->title(__('Attendance saved'))->send();
            });
    }

    // Active students of the session's class, cached per session for the
    // lifetime of the request (the form closure runs several times).
    private static function students(AttendanceSession $record): Collection
    {
        static $cache = [];

        return $cache[$record->getKey()] ??= Student::query()
            ->where('classroom_id', $record->classroomSubject?->classroom_id)
            ->where('status', StudentStatus::ACTIVE)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nis']);
    }
}
