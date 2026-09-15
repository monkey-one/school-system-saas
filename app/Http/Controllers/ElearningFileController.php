<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Private e-learning files. Assignment materials are available to staff, the
// assignment's teacher and students/parents of the class; submission files
// to staff, the assignment's teacher, the student and the student's parents.
class ElearningFileController extends Controller
{
    public function assignment(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        $classroomId = $assignment->classroomSubject?->classroom_id;

        $allowed = $this->isStaff($user)
            || $assignment->teacher?->user_id === $user->id
            || ($assignment->is_published && (
                $user->student?->classroom_id === $classroomId
                || StudentParent::where('email', $user->email)->whereHas('student', fn ($q) => $q->where('classroom_id', $classroomId))->exists()
            ));

        abort_unless($allowed, 403);

        return $this->serve($assignment->attachment, 'materi-' . $assignment->id);
    }

    public function submission(Request $request, AssignmentSubmission $submission)
    {
        $user = $request->user();

        $allowed = $this->isStaff($user)
            || $submission->assignment?->teacher?->user_id === $user->id
            || $user->student?->id === $submission->student_id
            || StudentParent::where('email', $user->email)->where('student_id', $submission->student_id)->exists();

        abort_unless($allowed, 403);

        return $this->serve($submission->attachment, 'jawaban-' . $submission->id);
    }

    private function isStaff($user): bool
    {
        return in_array($user->type, [UserType::SCHOOL_ADMIN, UserType::OPERATOR], true);
    }

    private function serve(?string $path, string $name)
    {
        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, $name . '.' . pathinfo($path, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
