<?php

namespace App\Filament\Imports;

use App\Enums\Gender;
use App\Enums\Religion;
use App\Enums\StudentStatus;
use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StudentImporter extends Importer
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping()
                ->rules(['required', 'max:20']),
            ImportColumn::make('nisn')
                ->label('NISN')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('full_name')
                ->label('Nama Lengkap')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('nickname')
                ->label('Nama Panggilan')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('gender')
                ->label('Jenis Kelamin')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('birth_place')
                ->label('Tempat Lahir')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('birth_date')
                ->label('Tanggal Lahir')
                ->rules(['required', 'date']),
            ImportColumn::make('religion')
                ->label('Agama')
                ->rules(['nullable']),
            ImportColumn::make('address')
                ->label('Alamat')
                ->rules(['nullable']),
            ImportColumn::make('city')
                ->label('Kota')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('province')
                ->label('Provinsi')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('phone')
                ->label('Telepon')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('email')
                ->label('Email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('previous_school')
                ->label('Sekolah Asal')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('entry_year')
                ->label('Tahun Masuk')
                ->numeric()
                ->rules(['nullable', 'integer']),
        ];
    }

    public function resolveRecord(): ?Student
    {
        return Student::firstOrNew(['nis' => $this->data['nis']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor data siswa selesai. ' . number_format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
