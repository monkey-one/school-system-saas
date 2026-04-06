<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Manages CRUD operations for student records
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
        $validated = $request->validate([
            'nis' => 'required|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'required|exists:classrooms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string',
        ]);

        $validated['tenant_id'] = Tenant::current()->id;

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan.',
            'data' => $student->load('classroom'),
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        return response()->json([
            'data' => $student->load('classroom', 'academicYear', 'user'),
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        $validated = $request->validate([
            'nis' => 'sometimes|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'sometimes|exists:classrooms,id',
            'full_name' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'sometimes|date',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string',
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => $student->fresh()->load('classroom'),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        abort_unless($student->tenant_id === Tenant::current()->id, 404);

        $student->delete();

        return response()->json([
            'message' => 'Siswa berhasil dihapus.',
        ]);
    }
}
