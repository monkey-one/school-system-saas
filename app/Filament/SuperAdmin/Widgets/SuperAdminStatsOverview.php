<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Enums\TenantStatus;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSekolahAktif = Tenant::where('status', TenantStatus::ACTIVE)->count();

        $totalSiswa = Student::count();

        $mrr = Subscription::where('status', 'active')
            ->whereHas('plan')
            ->get()
            ->sum(function ($subscription) {
                return $subscription->plan?->price ?? 0;
            });

        $totalGuru = Teacher::count();

        return [
            Stat::make('Total Sekolah Aktif', number_format($totalSekolahAktif))
                ->description('Sekolah berlangganan')
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('success')
                ->icon('heroicon-o-building-office-2'),

            Stat::make('Total Siswa', number_format($totalSiswa))
                ->description('Seluruh tenant')
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('MRR', 'Rp ' . number_format($mrr, 0, ',', '.'))
                ->description('Monthly Recurring Revenue')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Guru', number_format($totalGuru))
                ->description('Seluruh tenant')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary')
                ->icon('heroicon-o-briefcase'),
        ];
    }
}
