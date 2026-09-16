<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\StudentDocument;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Student papers (birth certificate, family card, diploma) live on the private
// disk and are never served by URL guessing. Staff may open any document of
// their own school; a student and their parents may open only their own.
class StudentDocumentController extends Controller
{
    public function show(Request $request, StudentDocument $document)
    {
        $user = $request->user();

        $allowed = in_array($user->type, [UserType::SCHOOL_ADMIN, UserType::OPERATOR], true)
            || $user->student?->id === $document->student_id
            || StudentParent::where('email', $user->email)->where('student_id', $document->student_id)->exists();

        abort_unless($allowed, 403);
        abort_unless(is_string($document->file_path) && Storage::disk('local')->exists($document->file_path), 404);

        $name = $document->file_name ?: 'dokumen-' . $document->id;

        return Storage::disk('local')->response(
            $document->file_path,
            $name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION),
            ['Cache-Control' => 'private, no-store'],
        );
    }
}
