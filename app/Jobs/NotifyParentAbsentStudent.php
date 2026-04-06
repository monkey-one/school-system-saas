<?php

namespace App\Jobs;

use App\Enums\AttendanceStatus;
use App\Models\StudentAttendance;
use App\Models\StudentParent;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Sends a WhatsApp notification to the parent of a student who was marked
// absent (ALFA) in a specific attendance session. Only fires when the
// student's parent has WhatsApp notifications enabled.
class NotifyParentAbsentStudent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $studentAttendanceId,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $attendance = StudentAttendance::with(['student', 'attendanceSession'])
            ->findOrFail($this->studentAttendanceId);

        if ($attendance->status !== AttendanceStatus::ALFA) {
            return;
        }

        $student = $attendance->student;

        $parent = StudentParent::where('student_id', $student->id)
            ->where('is_whatsapp_active', true)
            ->first();

        if (! $parent || empty($parent->phone)) {
            Log::warning("NotifyParentAbsentStudent: No active WhatsApp parent for student #{$student->id}");
            return;
        }

        // Variable names must match the double-brace placeholders in the
        // absen_alfa notification template: {{student_name}}, {{date}}, {{subject}}.
        $whatsApp->sendTemplate($parent->phone, 'absen_alfa', [
            'student_name' => $student->full_name,
            'date' => $attendance->attendanceSession->date ?? now()->format('d/m/Y'),
            'subject' => $student->classroom->name ?? '-',
        ], 'student_attendance', $attendance->id);

        $attendance->update(['notified_parent_at' => now()]);

        Log::info("NotifyParentAbsentStudent: Notified parent for student #{$student->id}");
    }
}
