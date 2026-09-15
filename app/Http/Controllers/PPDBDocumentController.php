<?php

namespace App\Http\Controllers;

use App\Models\PPDBRegistration;
use Illuminate\Support\Facades\Storage;

// Serves PPDB applicants' uploaded documents from the private disk. The route
// is limited to school admins/operators and the registration is loaded under
// the tenant scope, so staff can only open documents of their own school.
class PPDBDocumentController extends Controller
{
    public function show(PPDBRegistration $registration, string $document)
    {
        $path = $registration->documents[$document] ?? null;

        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        $name = $document . '-' . $registration->registration_number . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->response($path, $name, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
