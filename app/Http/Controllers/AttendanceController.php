<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\QRCodeService;
use Illuminate\Http\Request;

// QR attendance for students. A teacher displays a QR code that encodes a
// signed, short-lived JWT; the student scans it with their phone, signs in
// (if needed) and confirms. Attendance is always recorded for the SIGNED-IN
// student only, and only when the session belongs to that student's class.
class AttendanceController extends Controller
{
    public function __construct(
        protected QRCodeService $qrCodeService,
    ) {}

    // Show the session details and whether the student is already recorded.
    public function scan(Request $request)
    {
        $request->validate(['token' => 'required|string|max:2048']);

        $student = $request->user()->student;
        [$session, $error] = $this->resolveSession($request->string('token'), $student);

        return view('attendance.scan', [
            'session' => $session,
            'student' => $student,
            'token' => $request->string('token'),
            'attendance' => $session && $student
                ? StudentAttendance::where('attendance_session_id', $session->id)->where('student_id', $student->id)->first()
                : null,
            'justRecorded' => false,
            'error' => $error,
        ]);
    }

    // Record attendance once; a repeated confirmation keeps the first record.
    public function confirm(Request $request)
    {
        $request->validate(['token' => 'required|string|max:2048']);

        $student = $request->user()->student;
        [$session, $error] = $this->resolveSession($request->string('token'), $student);

        if ($error) {
            return view('attendance.scan', compact('session', 'student', 'error') + [
                'token' => $request->string('token'),
                'attendance' => null,
                'justRecorded' => false,
            ]);
        }

        $sessionStart = $session->date->copy()->setTimeFromTimeString($session->start_time ?? '00:00:00');
        $isLate = now()->greaterThan($sessionStart->addMinutes(15));

        $attendance = StudentAttendance::firstOrCreate(
            [
                'attendance_session_id' => $session->id,
                'student_id' => $student->id,
            ],
            [
                'tenant_id' => $session->tenant_id,
                'status' => $isLate ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR,
                'check_in_time' => now(),
                'method' => 'qr_code',
            ],
        );

        return view('attendance.scan', [
            'session' => $session,
            'student' => $student,
            'token' => $request->string('token'),
            'attendance' => $attendance,
            'justRecorded' => $attendance->wasRecentlyCreated,
            'error' => null,
        ]);
    }

    // Validates the QR token and returns [session, errorMessage]. The session
    // lookup runs under the student's tenant scope, so QR codes of other
    // schools are never found.
    private function resolveSession(string $token, ?Student $student): array
    {
        if (! $student) {
            return [null, __('Only student accounts can record attendance.')];
        }

        $payload = $this->qrCodeService->validateToken($token);

        if (! $payload || empty($payload['session_id'])) {
            return [null, __('The QR code is invalid or has expired. Please scan again.')];
        }

        $session = AttendanceSession::with('classroomSubject.subject', 'classroomSubject.classroom', 'teacher')
            ->find($payload['session_id']);

        if (! $session || $session->status !== 'open') {
            return [null, __('The attendance session was not found or is already closed.')];
        }

        if ((int) $session->classroomSubject?->classroom_id !== (int) $student->classroom_id) {
            return [$session, __('This attendance session is not for your class.')];
        }

        return [$session, null];
    }
}
