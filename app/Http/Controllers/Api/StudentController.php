<?php

namespace App\Http\Controllers\Api;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Manages CRUD operations for student records. Related IDs are validated
// against the current school so records can never be linked across tenants.
class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $students = Student::where('tenant_id', Tenant::current()->id)
            ->with('classroom', 'academicYear')
            ->latest()
            ->paginate(25);

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(creating: true));

        $validated['tenant_id'] = Tenant::current()->id;

        $student = Student::create($validated);

        return response()->json([
            'message' => __('Student created.'),
            'data' => $student->load('classroom'),
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        return response()->json([
            'data' => $student->load('classroom', 'academicYear'),
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        $student->update($request->validate($this->rules(creating: false)));

        return response()->json([
            'message' => __('Student updated.'),
            'data' => $student->fresh()->load('classroom'),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        $student->delete();

        return response()->json([
            'message' => __('Student deleted.'),
        ]);
    }

    private function rules(bool $creating): array
    {
        $tenantId = Tenant::current()->id;
        $required = $creating ? 'required' : 'sometimes';

        return [
            'nis' => [$required, 'string', 'max:50'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'classroom_id' => [$required, Rule::exists('classrooms', 'id')->where('tenant_id', $tenantId)],
            'academic_year_id' => [$required, Rule::exists('academic_years', 'id')->where('tenant_id', $tenantId)],
            'full_name' => [$required, 'string', 'max:255'],
            'gender' => [$required, Rule::enum(Gender::class)],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => [$required, 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', Rule::enum(StudentStatus::class)],
        ];
    }
}
