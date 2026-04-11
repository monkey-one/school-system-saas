<?php

namespace App\Filament\Imports;

use App\Models\Teacher;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TeacherImporter extends Importer
{
    protected static ?string $model = Teacher::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nip')
                ->label('NIP')
                ->rules(['nullable', 'max:30']),
            ImportColumn::make('nuptk')
                ->label('NUPTK')
                ->rules(['nullable', 'max:30']),
            ImportColumn::make('full_name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('gender')
                ->label('Jenis Kelamin')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('birth_place')
                ->label('Tempat Lahir')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('birth_date')
                ->label('Tanggal Lahir')
                ->rules(['nullable', 'date']),
            ImportColumn::make('religion')
                ->label('Agama')
                ->rules(['nullable']),
            ImportColumn::make('employment_status')
                ->label('Status Kepegawaian')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('position')
                ->label('Jabatan')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('education')
                ->label('Pendidikan Terakhir')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('major')
                ->label('Jurusan')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('phone')
                ->label('Telepon')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Teacher
    {
        if (! empty($this->data['nip'])) {
            return Teacher::firstOrNew(['nip' => $this->data['nip']]);
        }

        return new Teacher();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data guru selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
