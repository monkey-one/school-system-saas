<?php

namespace App\Filament\Imports;

use App\Models\Subject;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SubjectImporter extends Importer
{
    protected static ?string $model = Subject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->label('Kode')
                ->requiredMapping()
                ->rules(['required', 'max:20']),
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->label('Tipe')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description')
                ->label('Deskripsi')
                ->rules(['nullable', 'max:1000']),
        ];
    }

    public function resolveRecord(): ?Subject
    {
        return Subject::firstOrNew(['code' => $this->data['code']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data mapel selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
