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

// Displays platform-wide summary statistics for the super admin dashboard
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
                return $subscription->plan?->price_monthly ?? 0;
            });

        $totalGuru = Teacher::count();

        return [
            Stat::make(__('Total Active Schools'), number_format($totalSekolahAktif))
                ->description(__('Subscribed schools'))
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('success')
                ->icon('heroicon-o-building-office-2'),

            Stat::make(__('Total Students'), number_format($totalSiswa))
                ->description(__('All tenants'))
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->icon('heroicon-o-users'),

            Stat::make('MRR', 'Rp ' . number_format($mrr, 0, ',', '.'))
                ->description(__('Monthly Recurring Revenue'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make(__('Total Teachers'), number_format($totalGuru))
                ->description(__('All tenants'))
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary')
                ->icon('heroicon-o-briefcase'),
        ];
    }
}
