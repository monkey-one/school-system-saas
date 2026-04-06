<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Services\QRCodeService;
use Illuminate\Http\Request;

// Handles the public-facing QR attendance flow. Teachers generate a QR code
// that encodes a signed JWT. Students scan the code on their phone, which
// hits the scan endpoint to verify the token, then confirm their attendance.
class AttendanceController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService,
    ) {}

    // Display the scan page after validating the JWT token from the QR code.
    // Shows the session info so the student can confirm their identity.
    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $payload = $this->qrCodeService->validateToken($request->token);

        if (! $payload) {
            return view('attendance.scan', ['error' => 'QR Code tidak valid atau sudah kedaluwarsa.']);
        }

        $session = AttendanceSession::with('classroomSubject.subject', 'teacher')
            ->find($payload['session_id']);

        if (! $session || $session->status !== 'open') {
            return view('attendance.scan', ['error' => 'Sesi absensi tidak ditemukan atau sudah ditutup.']);
        }

        return view('attendance.scan', [
            'session' => $session,
            'token' => $request->token,
            'error' => null,
        ]);
    }

    // Record the student's attendance after a second validation of the token.
    // Checks for duplicates and marks late arrivals (more than 15 minutes
    // after the scheduled start time).
    public function confirm(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'student_id' => 'required|exists:students,id',
        ]);

        $payload = $this->qrCodeService->validateToken($request->token);

        if (! $payload) {
            return back()->with('error', 'QR Code tidak valid atau sudah kedaluwarsa.');
        }

        $session = AttendanceSession::find($payload['session_id']);

        if (! $session || $session->status !== 'open') {
            return back()->with('error', 'Sesi absensi tidak ditemukan atau sudah ditutup.');
        }

        $existing = StudentAttendance::where('attendance_session_id', $session->id)
            ->where('student_id', $request->student_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah melakukan absensi untuk sesi ini.');
        }

        $now = now();
        $sessionStart = $session->date->copy()->setTimeFromTimeString($session->start_time);
        $isLate = $now->diffInMinutes($sessionStart, false) < -15;

        StudentAttendance::create([
            'tenant_id' => $session->tenant_id,
            'attendance_session_id' => $session->id,
            'student_id' => $request->student_id,
            'status' => $isLate ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR,
            'check_in_time' => $now,
            'method' => 'qr_code',
        ]);

        return view('attendance.success', [
            'session' => $session,
            'isLate' => $isLate,
        ]);
    }
}
