<?php

namespace App\Filament\Exports;

use App\Models\StudentGrade;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentGradeExporter extends Exporter
{
    protected static ?string $model = StudentGrade::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('student.nis')->label('NIS'),
            ExportColumn::make('student.full_name')->label('Nama Siswa'),
            ExportColumn::make('assessment.name')->label('Penilaian'),
            ExportColumn::make('score')->label('Nilai'),
            ExportColumn::make('is_remedial')->label('Remedial'),
            ExportColumn::make('remedial_score')->label('Nilai Remedial'),
            ExportColumn::make('notes')->label('Catatan'),
            ExportColumn::make('grader.name')->label('Penilai'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data nilai selesai. ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
