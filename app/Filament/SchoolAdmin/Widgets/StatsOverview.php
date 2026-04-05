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
            Stat::make('Total Siswa Aktif', number_format($totalSiswaAktif))
                ->description('Siswa terdaftar aktif')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-users'),

            Stat::make('Total Guru Aktif', number_format($totalGuruAktif))
                ->description('Tenaga pengajar')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Pendapatan SPP Bulan Ini', 'Rp ' . number_format($pendapatanSppBulanIni, 0, ',', '.'))
                ->description('Bulan ' . Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Kehadiran Hari Ini', $kehadiranPercentage . '%')
                ->description($hadirToday . ' dari ' . $totalToday . ' siswa')
                ->descriptionIcon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }
}
