<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Manages CRUD operations for attendance sessions
class AttendanceSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sessions = AttendanceSession::where('tenant_id', Tenant::current()->id)
            ->with('classroomSubject.subject', 'teacher')
            ->latest('date')
            ->paginate(25);

        return response()->json($sessions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'classroom_subject_id' => 'required|exists:classroom_subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'topic' => 'nullable|string|max:500',
        ]);

        $validated['tenant_id'] = Tenant::current()->id;

        $session = AttendanceSession::create($validated);

        return response()->json([
            'message' => 'Sesi absensi berhasil dibuat.',
            'data' => $session->load('classroomSubject.subject', 'teacher'),
        ], 201);
    }

    public function show(AttendanceSession $attendanceSession): JsonResponse
    {
        abort_unless($attendanceSession->tenant_id === Tenant::current()->id, 404);

        return response()->json([
            'data' => $attendanceSession->load('classroomSubject.subject', 'teacher', 'studentAttendances.student'),
        ]);
    }

    public function update(Request $request, AttendanceSession $attendanceSession): JsonResponse
    {
        abort_unless($attendanceSession->tenant_id === Tenant::current()->id, 404);

        $validated = $request->validate([
            'classroom_subject_id' => 'sometimes|exists:classroom_subjects,id',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',
            'topic' => 'nullable|string|max:500',
            'status' => 'sometimes|in:open,closed',
        ]);

        $attendanceSession->update($validated);

        return response()->json([
            'message' => 'Sesi absensi berhasil diperbarui.',
            'data' => $attendanceSession->fresh()->load('classroomSubject.subject', 'teacher'),
        ]);
    }

    public function destroy(AttendanceSession $attendanceSession): JsonResponse
    {
        abort_unless($attendanceSession->tenant_id === Tenant::current()->id, 404);

        $attendanceSession->delete();

        return response()->json([
            'message' => 'Sesi absensi berhasil dihapus.',
        ]);
    }
}
