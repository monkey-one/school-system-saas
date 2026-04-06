<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

// Displays tenant growth trend over the last twelve months
class TenantGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Tenant 12 Bulan Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect();
        $counts = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $count = Tenant::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $counts->push($count);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tenant Baru',
                    'data' => $counts->toArray(),
                    'backgroundColor' => '#1E3A5F',
                    'borderColor' => '#1E3A5F',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
