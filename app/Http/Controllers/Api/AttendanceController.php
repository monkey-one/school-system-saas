<?php

namespace App\Http\Controllers\Api;

use App\Enums\AttendanceStatus;
use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use App\Services\QRCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService,
    ) {}

    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $payload = $this->qrCodeService->validateToken($request->token);

        if (! $payload) {
            return response()->json(['message' => 'QR Code tidak valid atau sudah kedaluwarsa.'], 422);
        }

        $session = AttendanceSession::where('tenant_id', Tenant::current()->id)
            ->find($payload['session_id']);

        if (! $session || $session->status !== 'open') {
            return response()->json(['message' => 'Sesi absensi tidak ditemukan atau sudah ditutup.'], 404);
        }

        $student = $request->user()->student;

        if (! $student) {
            return response()->json(['message' => 'Anda bukan siswa.'], 403);
        }

        $existing = StudentAttendance::where('attendance_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah melakukan absensi untuk sesi ini.'], 409);
        }

        $now = now();
        $sessionStart = $session->date->copy()->setTimeFromTimeString($session->start_time);
        $isLate = $now->diffInMinutes($sessionStart, false) < -15;

        $attendance = StudentAttendance::create([
            'tenant_id' => $session->tenant_id,
            'attendance_session_id' => $session->id,
            'student_id' => $student->id,
            'status' => $isLate ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR,
            'check_in_time' => $now,
            'method' => 'qr_code',
        ]);

        return response()->json([
            'message' => $isLate ? 'Absensi berhasil (terlambat).' : 'Absensi berhasil.',
            'data' => $attendance->load('attendanceSession'),
        ], 201);
    }
}
