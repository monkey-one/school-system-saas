<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\LeaveRequest;
use App\Models\StudentAttendance;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

// Review workflow for online leave requests. Approving a request records the
// student as "sakit"/"izin" in every attendance session of their class held
// within the requested dates; sessions created later pick the status up from
// TakeAttendanceAction.
class LeaveRequestService
{
    public function approve(LeaveRequest $request, User $reviewer, ?string $note = null): int
    {
        return DB::transaction(function () use ($request, $reviewer, $note) {
            $request->update([
                'status' => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'review_note' => $note,
            ]);

            $student = $request->student;
            $status = $request->type === 'sakit' ? AttendanceStatus::SAKIT : AttendanceStatus::IZIN;

            $sessions = AttendanceSession::query()
                ->whereHas('classroomSubject', fn ($q) => $q->where('classroom_id', $student->classroom_id))
                ->whereBetween('date', [$request->start_date->toDateString(), $request->end_date->toDateString()])
                ->get();

            foreach ($sessions as $session) {
                StudentAttendance::updateOrCreate(
                    ['attendance_session_id' => $session->id, 'student_id' => $student->id],
                    [
                        'tenant_id' => $session->tenant_id,
                        'status' => $status,
                        'check_in_time' => $session->date->copy()->setTimeFromTimeString($session->start_time ?? '07:00'),
                        'method' => 'leave_request',
                        'notes' => $request->reason,
                    ],
                );
            }

            $this->notifyRequester($request, __('Your leave request for :name was approved.', ['name' => $student->full_name]));

            return $sessions->count();
        });
    }

    public function reject(LeaveRequest $request, User $reviewer, ?string $note = null): void
    {
        $request->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_note' => $note,
        ]);

        $this->notifyRequester($request, __('Your leave request for :name was rejected.', ['name' => $request->student->full_name]));
    }

    // Let the homeroom teacher know a new request is waiting (database notification).
    public function notifyHomeroomTeacher(LeaveRequest $request): void
    {
        $teacherUser = $request->student->classroom?->homeroomTeacher?->user;

        if ($teacherUser) {
            Notification::make()
                ->title(__('New leave request'))
                ->body(__(':name requests :type leave (:from – :to).', [
                    'name' => $request->student->full_name,
                    'type' => LeaveRequest::typeLabels()[$request->type] ?? $request->type,
                    'from' => $request->start_date->translatedFormat('d M'),
                    'to' => $request->end_date->translatedFormat('d M Y'),
                ]))
                ->icon('heroicon-o-envelope-open')
                ->sendToDatabase($teacherUser);
        }
    }

    private function notifyRequester(LeaveRequest $request, string $message): void
    {
        if (! $request->requester) {
            return;
        }

        \App\Models\Message::create([
            'sender_id' => $request->reviewed_by,
            'recipient_id' => $request->requested_by,
            'student_id' => $request->student_id,
            'subject' => __('Leave request') . ' ' . $request->start_date->translatedFormat('d M Y'),
            'content' => $message . ($request->review_note ? "\n\n" . $request->review_note : ''),
        ]);
    }
}
