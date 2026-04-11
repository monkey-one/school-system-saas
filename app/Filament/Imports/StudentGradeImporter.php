<?php

namespace App\Filament\Imports;

use App\Models\StudentGrade;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StudentGradeImporter extends Importer
{
    protected static ?string $model = StudentGrade::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('assessment_id')
                ->label('ID Penilaian')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('student_id')
                ->label('ID Siswa')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('score')
                ->label('Nilai')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric', 'min:0', 'max:100']),
            ImportColumn::make('is_remedial')
                ->label('Remedial')
                ->boolean()
                ->rules(['nullable', 'boolean']),
            ImportColumn::make('remedial_score')
                ->label('Nilai Remedial')
                ->numeric()
                ->rules(['nullable', 'numeric', 'min:0', 'max:100']),
            ImportColumn::make('notes')
                ->label('Catatan')
                ->rules(['nullable', 'max:500']),
        ];
    }

    public function resolveRecord(): ?StudentGrade
    {
        return StudentGrade::firstOrNew([
            'assessment_id' => $this->data['assessment_id'],
            'student_id' => $this->data['student_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data nilai selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
