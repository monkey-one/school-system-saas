<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Models\AlumniProfile;
use App\Models\Student;
use App\Models\Tenant;

class AlumniController extends Controller
{
    public function index()
    {
        $tenant = Tenant::current();

        $stats = [
            'total_alumni' => AlumniProfile::count(),
            'verified' => AlumniProfile::where('is_verified', true)->count(),
            'pursuing_education' => AlumniProfile::whereNotNull('higher_education')->where('higher_education', '!=', '')->count(),
            'employed' => AlumniProfile::whereNotNull('current_occupation')->where('current_occupation', '!=', '')->count(),
        ];

        $graduationYears = AlumniProfile::query()
            ->selectRaw('YEAR(graduated_at) as year')
            ->groupByRaw('YEAR(graduated_at)')
            ->orderByDesc('year')
            ->pluck('year')
            ->filter()
            ->toArray();

        $selectedYear = request('year');

        $alumni = AlumniProfile::query()
            ->with('student')
            ->where('is_verified', true)
            ->when($selectedYear, fn ($q) => $q->whereYear('graduated_at', $selectedYear))
            ->orderByDesc('graduated_at')
            ->paginate(12);

        $testimonials = AlumniProfile::query()
            ->with('student')
            ->where('is_verified', true)
            ->whereNotNull('testimonial')
            ->where('testimonial', '!=', '')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('alumni.index', compact('tenant', 'stats', 'graduationYears', 'selectedYear', 'alumni', 'testimonials'));
    }
}
