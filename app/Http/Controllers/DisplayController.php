<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Post;
use App\Models\SchoolEvent;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use App\Models\Teacher;
use App\Models\Tenant;
use Illuminate\View\View;

// Full-screen lobby TV display: clock, rotating slides with announcements,
// agenda, achievements and news, today's attendance summary and a running
// text. Only public, aggregated information is shown. The page refreshes
// itself every 10 minutes.
class DisplayController extends Controller
{
    public function show(): View
    {
        $tenant = Tenant::current();
        abort_unless($tenant, 404);

        $today = StudentAttendance::whereHas('attendanceSession', fn ($q) => $q->whereDate('date', today()));
        $present = (clone $today)->whereIn('status', [AttendanceStatus::HADIR->value, AttendanceStatus::TERLAMBAT->value])->count();
        $total = (clone $today)->count();

        return view('display.board', [
            'tenant' => $tenant,
            'announcements' => Announcement::whereIn('target_type', ['all', 'students', 'parents'])
                ->whereNotNull('published_at')->where('published_at', '<=', now())
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                ->orderByDesc('is_pinned')->latest('published_at')->limit(5)->get(),
            'events' => SchoolEvent::published()->upcoming()->orderBy('starts_at')->limit(5)->get(),
            'achievements' => Achievement::published()->latest('achieved_at')->limit(4)->get(),
            'news' => Post::published()->latest('published_at')->limit(3)->get(),
            'attendanceRate' => $total > 0 ? (int) round($present / $total * 100) : null,
            'teachersPresent' => TeacherAttendance::whereDate('date', today())->whereIn('status', [AttendanceStatus::HADIR->value, AttendanceStatus::TERLAMBAT->value])->count(),
            'teachersTotal' => Teacher::count(),
        ]);
    }
}
