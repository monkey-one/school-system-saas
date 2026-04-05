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

        $whatsApp->sendTemplate($parent->phone, 'absen_alfa', [
            'nama_siswa' => $student->full_name,
            'tanggal' => $attendance->attendanceSession->date ?? now()->format('d/m/Y'),
            'kelas' => $student->classroom->name ?? '-',
        ], 'student_attendance', $attendance->id);

        $attendance->update(['notified_parent_at' => now()]);

        Log::info("NotifyParentAbsentStudent: Notified parent for student #{$student->id}");
    }
}
