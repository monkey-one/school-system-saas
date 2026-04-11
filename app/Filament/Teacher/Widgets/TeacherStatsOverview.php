<?php

namespace App\Filament\Teacher\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\TeacherAttendance;
use App\Models\TeachingSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TeacherStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $teacher = auth()->user()->teacher ?? null;
        $teacherId = $teacher?->id;

        $todaySchedules = $teacherId
            ? TeachingSchedule::where('teacher_id', $teacherId)
                ->where('day_of_week', Carbon::now()->dayOfWeekIso)
                ->where('is_active', true)
                ->count()
            : 0;

        $totalStudents = $teacherId
            ? Student::whereHas('classroom', function ($q) use ($teacherId) {
                $q->where('homeroom_teacher_id', $teacherId);
            })->count()
            : 0;

        $myAttendanceThisMonth = $teacherId
            ? TeacherAttendance::where('teacher_id', $teacherId)
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->where('status', AttendanceStatus::HADIR)
                ->count()
            : 0;

        $totalSessions = $teacherId
            ? AttendanceSession::where('teacher_id', $teacherId)
                ->whereMonth('date', Carbon::now()->month)
                ->count()
            : 0;

        return [
            Stat::make(__('Today\'s Classes'), $todaySchedules)
                ->description(__('Scheduled for today'))
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-calendar-days'),

            Stat::make(__('My Students'), number_format($totalStudents))
                ->description(__('In homeroom class'))
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make(__('My Attendance'), $myAttendanceThisMonth . ' ' . __('days'))
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make(__('Sessions This Month'), number_format($totalSessions))
                ->description(__('Attendance sessions recorded'))
                ->descriptionIcon('heroicon-o-document-check')
                ->color('warning')
                ->icon('heroicon-o-document-check'),
        ];
    }
}
