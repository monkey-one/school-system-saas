<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Helpers\CurrencyHelper;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Services\StudentOverview;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

// Summary statistics for the school admin dashboard. Revenue only counts
// settled payments (manual or confirmed by the gateway) and attendance
// counts late arrivals as present.
class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeStudents = Student::where('status', StudentStatus::ACTIVE)->count();
        $teachers = Teacher::count();

        $revenue = Payment::whereBetween('payment_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->where(fn ($q) => $q->whereNull('gateway_status')->orWhereIn('gateway_status', StudentOverview::SETTLED_GATEWAY_STATUSES))
            ->sum('amount');

        $today = StudentAttendance::whereHas('attendanceSession', fn ($q) => $q->whereDate('date', Carbon::today()));
        $totalToday = (clone $today)->count();
        $presentToday = (clone $today)->whereIn('status', [AttendanceStatus::HADIR->value, AttendanceStatus::TERLAMBAT->value])->count();
        $rate = $totalToday > 0 ? round($presentToday / $totalToday * 100, 1) : 0;

        return [
            Stat::make(__('Total Active Students'), number_format($activeStudents))
                ->description(__('Registered active students'))
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-users'),
            Stat::make(__('Total Active Teachers'), number_format($teachers))
                ->description(__('Teaching staff'))
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->icon('heroicon-o-briefcase'),
            Stat::make(__('Monthly SPP Revenue'), CurrencyHelper::format($revenue))
                ->description(__('Month') . ' ' . Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),
            Stat::make(__('Attendance Today'), $rate . '%')
                ->description($presentToday . ' ' . __('of') . ' ' . $totalToday . ' ' . __('students'))
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }
}
