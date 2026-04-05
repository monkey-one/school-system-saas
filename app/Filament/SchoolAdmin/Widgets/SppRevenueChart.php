<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SppRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan SPP 6 Bulan Terakhir';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect();
        $revenues = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $revenue = Payment::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');

            $revenues->push((float) $revenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan SPP',
                    'data' => $revenues->toArray(),
                    'backgroundColor' => '#F59E0B',
                    'borderColor' => '#D97706',
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
