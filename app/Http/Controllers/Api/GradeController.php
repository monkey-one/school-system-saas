<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentGrade;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Retrieves grades and assessment results for the authenticated student
class GradeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (! $student) {
            return response()->json(['message' => 'Anda bukan siswa.'], 403);
        }

        $query = StudentGrade::where('student_id', $student->id)
            ->where('tenant_id', Tenant::current()->id)
            ->with('assessment.classroomSubject.subject', 'assessment.assessmentType');

        if ($request->has('semester_id')) {
            $query->whereHas('assessment.classroomSubject', function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
        }

        $grades = $query->latest()->paginate(25);

        return response()->json($grades);
    }
}
