<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Enums\TenantStatus;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class PlatformAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 9;

    protected static string $view = 'filament.super-admin.pages.platform-analytics';

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Platform Analytics');
    }

    public function getTitle(): string
    {
        return __('Platform Analytics');
    }

    public function getViewData(): array
    {
        $now = Carbon::now();

        $tenantsByStatus = Tenant::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthlyGrowth[] = [
                'month' => $date->translatedFormat('M Y'),
                'tenants' => Tenant::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        $topTenants = Tenant::where('status', TenantStatus::ACTIVE)
            ->withCount(['students', 'teachers'])
            ->orderByDesc('students_count')
            ->limit(10)
            ->get();

        $subscriptionStats = Subscription::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'totalTenants' => Tenant::count(),
            'activeTenants' => $tenantsByStatus[TenantStatus::ACTIVE->value] ?? 0,
            'totalUsers' => User::count(),
            'totalStudents' => Student::count(),
            'totalTeachers' => Teacher::count(),
            'tenantsByStatus' => $tenantsByStatus,
            'monthlyGrowth' => $monthlyGrowth,
            'topTenants' => $topTenants,
            'subscriptionStats' => $subscriptionStats,
        ];
    }
}
