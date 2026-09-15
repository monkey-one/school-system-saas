<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Serves a leave request attachment (e.g. doctor's note) from the private
// disk to school staff, the student's homeroom teacher or the requester.
class LeaveRequestAttachmentController extends Controller
{
    public function show(Request $request, LeaveRequest $leaveRequest)
    {
        $user = $request->user();
        $homeroomUserId = $leaveRequest->student?->classroom?->homeroomTeacher?->user_id;

        abort_unless(
            in_array($user->type, [UserType::SCHOOL_ADMIN, UserType::OPERATOR], true)
                || $user->id === $homeroomUserId
                || $user->id === $leaveRequest->requested_by,
            403,
        );

        $path = $leaveRequest->attachment;
        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, 'lampiran-izin-' . $leaveRequest->id . '.' . pathinfo($path, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
