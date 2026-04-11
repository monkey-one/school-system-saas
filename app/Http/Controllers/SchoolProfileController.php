<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Models\Announcement;
use App\Models\Facility;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Tenant;
use Illuminate\Http\Request;

// Serves the public-facing school profile website. Requires tenant context
// so the correct school's data is displayed.
class SchoolProfileController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();

        $stats = [
            'students' => Student::where('status', StudentStatus::ACTIVE)->count(),
            'teachers' => Teacher::count(),
            'alumni' => Student::where('status', StudentStatus::ALUMNI)->count(),
            'facilities' => Facility::count(),
        ];

        $teachers = Teacher::query()
            ->select('full_name', 'position', 'photo', 'education', 'major')
            ->limit(8)
            ->get();

        $facilities = Facility::query()
            ->select('name', 'type', 'capacity', 'description')
            ->limit(6)
            ->get();

        $announcements = Announcement::query()
            ->where('target_type', 'all')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('school-profile.index', compact('tenant', 'stats', 'teachers', 'facilities', 'announcements'));
    }
}
