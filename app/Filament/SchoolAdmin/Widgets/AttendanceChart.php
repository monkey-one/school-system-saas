<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\AttendanceStatus;
use App\Models\StudentAttendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

// Displays weekly student attendance statistics as a chart
class AttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'Kehadiran Minggu Ini';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $labels = [];
        $hadirData = [];
        $sakitData = [];
        $izinData = [];
        $alfaData = [];

        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $labels[] = $date->translatedFormat('D, d M');

            $dayAttendances = StudentAttendance::whereHas('attendanceSession', function ($query) use ($date) {
                $query->whereDate('date', $date);
            });

            $hadirData[] = (clone $dayAttendances)->where('status', AttendanceStatus::HADIR)->count();
            $sakitData[] = (clone $dayAttendances)->where('status', AttendanceStatus::SAKIT)->count();
            $izinData[] = (clone $dayAttendances)->where('status', AttendanceStatus::IZIN)->count();
            $alfaData[] = (clone $dayAttendances)->where('status', AttendanceStatus::ALFA)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $hadirData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Sakit',
                    'data' => $sakitData,
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Izin',
                    'data' => $izinData,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Alfa',
                    'data' => $alfaData,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
