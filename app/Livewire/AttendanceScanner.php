<?php

namespace App\Livewire;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\StudentAttendance;
use App\Services\QRCodeService;
use Livewire\Component;

class AttendanceScanner extends Component
{
    public ?string $token = null;
    public ?AttendanceSession $session = null;
    public ?string $error = null;
    public ?string $success = null;

    public function mount(?string $token = null): void
    {
        $this->token = $token;

        if (! $token) {
            $this->error = 'Token tidak ditemukan.';
            return;
        }

        $qrCodeService = app(QRCodeService::class);
        $payload = $qrCodeService->validateToken($token);

        if (! $payload) {
            $this->error = 'QR Code tidak valid atau sudah kedaluwarsa.';
            return;
        }

        $this->session = AttendanceSession::with('classroomSubject.subject', 'teacher')
            ->find($payload['session_id']);

        if (! $this->session || $this->session->status !== 'open') {
            $this->error = 'Sesi absensi tidak ditemukan atau sudah ditutup.';
            $this->session = null;
        }
    }

    public function confirm(int $studentId): void
    {
        if (! $this->session || ! $this->token) {
            $this->error = 'Sesi tidak valid.';
            return;
        }

        $qrCodeService = app(QRCodeService::class);
        $payload = $qrCodeService->validateToken($this->token);

        if (! $payload) {
            $this->error = 'QR Code sudah kedaluwarsa. Silakan scan ulang.';
            return;
        }

        $existing = StudentAttendance::where('attendance_session_id', $this->session->id)
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            $this->error = 'Anda sudah melakukan absensi untuk sesi ini.';
            return;
        }

        $now = now();
        $sessionStart = $this->session->date->copy()->setTimeFromTimeString($this->session->start_time);
        $isLate = $now->diffInMinutes($sessionStart, false) < -15;

        StudentAttendance::create([
            'tenant_id' => $this->session->tenant_id,
            'attendance_session_id' => $this->session->id,
            'student_id' => $studentId,
            'status' => $isLate ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR,
            'check_in_time' => $now,
            'method' => 'qr_code',
        ]);

        $this->success = $isLate
            ? 'Absensi berhasil dicatat (terlambat).'
            : 'Absensi berhasil dicatat.';
    }

    public function render()
    {
        return view('livewire.attendance-scanner');
    }
}
