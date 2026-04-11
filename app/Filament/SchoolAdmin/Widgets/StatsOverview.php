<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\AttendanceStatus;
use App\Enums\StudentStatus;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use App\Helpers\CurrencyHelper;

// Displays summary statistics for the school admin dashboard
class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSiswaAktif = Student::where('status', StudentStatus::ACTIVE)->count();

        $totalGuruAktif = Teacher::count();

        $pendapatanSppBulanIni = Payment::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $todayAttendances = StudentAttendance::whereHas('attendanceSession', function ($query) {
            $query->whereDate('date', Carbon::today());
        });

        $totalToday = (clone $todayAttendances)->count();
        $hadirToday = (clone $todayAttendances)->where('status', AttendanceStatus::HADIR)->count();
        $kehadiranPercentage = $totalToday > 0
            ? round(($hadirToday / $totalToday) * 100, 1)
            : 0;

        return [
            Stat::make(__('Total Active Students'), number_format($totalSiswaAktif))
                ->description(__('Registered active students'))
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make(__('Total Active Teachers'), number_format($totalGuruAktif))
                ->description(__('Teaching staff'))
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->icon('heroicon-o-briefcase'),

            Stat::make(__('Monthly SPP Revenue'), CurrencyHelper::format($pendapatanSppBulanIni))
                ->description(__('Month') . ' ' . Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make(__('Attendance Today'), $kehadiranPercentage . '%')
                ->description($hadirToday . ' ' . __('of') . ' ' . $totalToday . ' ' . __('students'))
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }
}
