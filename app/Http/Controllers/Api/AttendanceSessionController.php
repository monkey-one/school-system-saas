<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Manages CRUD operations for attendance sessions. Teachers can only manage
// their own sessions; admins and operators manage every session of the school.
class AttendanceSessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sessions = AttendanceSession::where('tenant_id', Tenant::current()->id)
            ->when($this->teacherId($request), fn ($q, $teacherId) => $q->where('teacher_id', $teacherId))
            ->with('classroomSubject.subject', 'teacher')
            ->latest('date')
            ->paginate(25);

        return response()->json($sessions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(creating: true));

        $validated['tenant_id'] = Tenant::current()->id;
        $validated['teacher_id'] = $this->teacherId($request) ?? $validated['teacher_id'];

        $session = AttendanceSession::create($validated);

        return response()->json([
            'message' => __('Attendance session created.'),
            'data' => $session->load('classroomSubject.subject', 'teacher'),
        ], 201);
    }

    public function show(Request $request, AttendanceSession $attendanceSession): JsonResponse
    {
        $this->authorizeSession($request, $attendanceSession);

        return response()->json([
            'data' => $attendanceSession->load('classroomSubject.subject', 'teacher', 'studentAttendances.student'),
        ]);
    }

    public function update(Request $request, AttendanceSession $attendanceSession): JsonResponse
    {
        $this->authorizeSession($request, $attendanceSession);

        $validated = $request->validate($this->rules(creating: false));
        unset($validated['teacher_id']);

        $attendanceSession->update($validated);

        return response()->json([
            'message' => __('Attendance session updated.'),
            'data' => $attendanceSession->fresh()->load('classroomSubject.subject', 'teacher'),
        ]);
    }

    public function destroy(Request $request, AttendanceSession $attendanceSession): JsonResponse
    {
        $this->authorizeSession($request, $attendanceSession);

        $attendanceSession->delete();

        return response()->json([
            'message' => __('Attendance session deleted.'),
        ]);
    }

    // The teacher profile ID when the caller is a teacher, otherwise null.
    private function teacherId(Request $request): ?int
    {
        $user = $request->user();

        return $user->type === UserType::TEACHER ? ($user->teacher?->id ?? 0) : null;
    }

    private function authorizeSession(Request $request, AttendanceSession $session): void
    {
        abort_unless($session->tenant_id === Tenant::current()->id, 404);

        $teacherId = $this->teacherId($request);
        abort_if($teacherId !== null && $session->teacher_id !== $teacherId, 403);
    }

    private function rules(bool $creating): array
    {
        $tenantId = Tenant::current()->id;
        $required = $creating ? 'required' : 'sometimes';

        return [
            'classroom_subject_id' => [$required, Rule::exists('classroom_subjects', 'id')->where('tenant_id', $tenantId)],
            'teacher_id' => ['sometimes', Rule::exists('teachers', 'id')->where('tenant_id', $tenantId)],
            'date' => [$required, 'date'],
            'start_time' => [$required, 'date_format:H:i'],
            'end_time' => [$required, 'date_format:H:i', 'after:start_time'],
            'topic' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'in:open,closed'],
        ];
    }
}
