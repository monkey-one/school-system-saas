<?php

namespace App\Filament\Exports;

use App\Models\TeachingSchedule;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TeachingScheduleExporter extends Exporter
{
    protected static ?string $model = TeachingSchedule::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('teacher.full_name')->label('Guru'),
            ExportColumn::make('classroomSubject.classroom.name')->label('Kelas'),
            ExportColumn::make('classroomSubject.subject.name')->label('Mapel'),
            ExportColumn::make('day_of_week')
                ->label('Hari')
                ->formatStateUsing(fn (int $state) => match ($state) {
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu',
                    default => '-',
                }),
            ExportColumn::make('start_time')->label('Jam Mulai'),
            ExportColumn::make('end_time')->label('Jam Selesai'),
            ExportColumn::make('room')->label('Ruangan'),
            ExportColumn::make('semester.name')->label('Semester'),
            ExportColumn::make('is_active')->label('Aktif'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data jadwal selesai. ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
