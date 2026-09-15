<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\PaymentStatus;
use App\Models\Announcement;
use App\Models\BookLoan;
use App\Models\ClassroomSubject;
use App\Models\Payment;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentExtracurricular;
use App\Models\StudentGrade;
use App\Models\Teacher;
use App\Models\TeachingSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

// Read-only queries shared by the student and parent portals. Every method
// receives the student explicitly; authorization happens in the controllers.
// All models are tenant-scoped, so results never cross schools.
class StudentOverview
{
    // Gateway statuses that mean the money has actually arrived. Manual
    // payments (cash/transfer recorded by staff) have no gateway status.
    public const SETTLED_GATEWAY_STATUSES = ['settlement', 'capture', 'paid'];

    public function activeSemester(): ?Semester
    {
        return Semester::with('academicYear')->where('is_active', true)->first();
    }

    public function semesters(): Collection
    {
        return Semester::with('academicYear')->orderByDesc('starts_at')->get();
    }

    // Active teaching schedule of the student's class grouped by ISO weekday (1 = Monday).
    public function weeklySchedule(Student $student): Collection
    {
        if (! $student->classroom_id) {
            return collect();
        }

        $semester = $this->activeSemester();

        return TeachingSchedule::query()
            ->where('is_active', true)
            ->whereHas('classroomSubject', fn (Builder $q) => $q->where('classroom_id', $student->classroom_id))
            ->when($semester, fn (Builder $q) => $q->where('semester_id', $semester->id))
            ->with('classroomSubject.subject', 'teacher')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
    }

    public function todaySchedule(Student $student): Collection
    {
        return $this->weeklySchedule($student)->get(now()->dayOfWeekIso, collect());
    }

    // Counts per attendance status (by session date) plus total and presence rate.
    public function attendanceSummary(Student $student, ?Carbon $month = null): array
    {
        $counts = StudentAttendance::query()
            ->where('student_id', $student->id)
            ->when($month, fn (Builder $q) => $q->whereHas('attendanceSession', fn (Builder $s) => $s->whereBetween('date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = collect(AttendanceStatus::cases())
            ->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => (int) ($counts[$status->value] ?? 0)])
            ->all();

        $total = array_sum($summary);
        $present = $summary[AttendanceStatus::HADIR->value] + $summary[AttendanceStatus::TERLAMBAT->value];

        return $summary + [
            'total' => $total,
            'rate' => $total > 0 ? (int) round($present / $total * 100) : null,
        ];
    }

    public function attendanceRecords(Student $student, Carbon $month): Collection
    {
        return StudentAttendance::query()
            ->where('student_id', $student->id)
            ->whereHas('attendanceSession', fn (Builder $q) => $q->whereBetween('date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ]))
            ->with('attendanceSession.classroomSubject.subject')
            ->get()
            ->sortByDesc(fn (StudentAttendance $row) => $row->attendanceSession?->date?->timestamp)
            ->values();
    }

    // Grades of a semester grouped by subject name, each group sorted by date.
    public function gradesBySubject(Student $student, ?int $semesterId): Collection
    {
        return StudentGrade::query()
            ->where('student_id', $student->id)
            ->whereHas('assessment', fn (Builder $q) => $q->when($semesterId, fn (Builder $a) => $a->where('semester_id', $semesterId)))
            ->with('assessment.classroomSubject.subject', 'assessment.assessmentType')
            ->get()
            ->sortBy(fn (StudentGrade $grade) => $grade->assessment?->date?->timestamp)
            ->groupBy(fn (StudentGrade $grade) => $grade->assessment?->classroomSubject?->subject?->name ?? '-')
            ->sortKeys();
    }

    public function recentGrades(Student $student, int $limit = 5): Collection
    {
        return StudentGrade::query()
            ->where('student_id', $student->id)
            ->with('assessment.classroomSubject.subject', 'assessment.assessmentType')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function publishedReportCards(Student $student): Collection
    {
        return ReportCard::query()
            ->where('student_id', $student->id)
            ->where('status', 'published')
            ->with('semester.academicYear', 'classroom')
            ->withCount('reportCardSubjects')
            ->latest('published_at')
            ->get();
    }

    public function bills(Student $student): Collection
    {
        return SppBill::query()
            ->where('student_id', $student->id)
            ->with(['sppType', 'allocations.payment'])
            ->orderByDesc('due_date')
            ->get();
    }

    // Amount still owed on a bill, counting only allocations of settled payments.
    public function outstanding(SppBill $bill): float
    {
        if (in_array($bill->status, [PaymentStatus::PAID, PaymentStatus::WAIVED], true)) {
            return 0.0;
        }

        $paid = $bill->allocations
            ->filter(fn ($allocation) => $allocation->payment && $this->isSettled($allocation->payment))
            ->sum('amount');

        return max(0.0, (float) $bill->final_amount - (float) $paid);
    }

    public function billSummary(Collection $bills): array
    {
        $outstanding = $bills->sum(fn (SppBill $bill) => $this->outstanding($bill));
        $total = (float) $bills->sum('final_amount');

        return [
            'total' => $total,
            'outstanding' => $outstanding,
            'paid' => max(0.0, $total - $outstanding),
            'overdue_count' => $bills->filter(fn (SppBill $bill) => $this->outstanding($bill) > 0 && $bill->due_date?->isPast())->count(),
        ];
    }

    public function payments(Student $student): Collection
    {
        return Payment::query()
            ->where('student_id', $student->id)
            ->with('allocations.sppBill.sppType')
            ->latest('payment_date')
            ->latest('id')
            ->get();
    }

    public function isSettled(Payment $payment): bool
    {
        return $payment->gateway_status === null
            || in_array(strtolower((string) $payment->gateway_status), self::SETTLED_GATEWAY_STATUSES, true);
    }

    // Published, unexpired announcements for the audience ("students" or
    // "parents"), including those targeted at the student's class.
    public function announcements(string $audience, ?Student $student, ?int $limit = null): Collection
    {
        return Announcement::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(function (Builder $q) use ($audience, $student) {
                $q->whereIn('target_type', ['all', $audience]);

                if ($student?->classroom_id) {
                    $q->orWhere(fn (Builder $c) => $c->where('target_type', 'specific_class')
                        ->where(fn (Builder $ids) => $ids
                            ->whereJsonContains('target_ids', (string) $student->classroom_id)
                            ->orWhereJsonContains('target_ids', (int) $student->classroom_id)));
                }
            })
            ->with('author')
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->when($limit, fn (Builder $q) => $q->limit($limit))
            ->get();
    }

    public function loans(Student $student): Collection
    {
        return BookLoan::query()
            ->where('borrower_type', Student::class)
            ->where('borrower_id', $student->id)
            ->with('book')
            ->latest('loan_date')
            ->get();
    }

    public function extracurriculars(Student $student): Collection
    {
        return StudentExtracurricular::query()
            ->where('student_id', $student->id)
            ->with('extracurricular.teacher', 'academicYear')
            ->get();
    }

    // Teachers a student/parent may message: the homeroom teacher and the
    // teachers of the class's subjects. Returns [user_id, label] rows.
    public function messageRecipients(Student $student): Collection
    {
        $homeroomId = $student->classroom?->homeroom_teacher_id;

        $teacherIds = collect([$homeroomId])
            ->merge(ClassroomSubject::where('classroom_id', $student->classroom_id)->pluck('teacher_id'))
            ->filter()
            ->unique();

        return Teacher::query()
            ->whereIn('id', $teacherIds)
            ->whereNotNull('user_id')
            ->orderBy('full_name')
            ->get()
            ->map(fn (Teacher $teacher) => [
                'user_id' => $teacher->user_id,
                'label' => $teacher->full_name . ($teacher->id === $homeroomId ? ' — ' . __('Homeroom Teacher') : ''),
                'homeroom' => $teacher->id === $homeroomId,
            ])
            ->sortByDesc('homeroom')
            ->values();
    }
}
