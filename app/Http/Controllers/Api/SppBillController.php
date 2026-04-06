<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SppBill;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Retrieves tuition fee bills for the authenticated student
class SppBillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (! $student) {
            return response()->json(['message' => 'Anda bukan siswa.'], 403);
        }

        $bills = SppBill::where('student_id', $student->id)
            ->where('tenant_id', Tenant::current()->id)
            ->with('sppType')
            ->orderByDesc('due_date')
            ->paginate(25);

        return response()->json($bills);
    }
}
