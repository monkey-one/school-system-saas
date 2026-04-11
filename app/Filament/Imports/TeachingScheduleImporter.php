<?php

namespace App\Filament\Imports;

use App\Models\TeachingSchedule;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TeachingScheduleImporter extends Importer
{
    protected static ?string $model = TeachingSchedule::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('teacher_id')
                ->label('ID Guru')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('classroom_subject_id')
                ->label('ID Kelas-Mapel')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('day_of_week')
                ->label('Hari (1-6)')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:1', 'max:6']),
            ImportColumn::make('start_time')
                ->label('Jam Mulai')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('end_time')
                ->label('Jam Selesai')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('room')
                ->label('Ruangan')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('semester_id')
                ->label('ID Semester')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): ?TeachingSchedule
    {
        return new TeachingSchedule();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data jadwal selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
