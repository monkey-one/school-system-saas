<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nis')->label('NIS'),
            ExportColumn::make('nisn')->label('NISN'),
            ExportColumn::make('full_name')->label('Nama Lengkap'),
            ExportColumn::make('nickname')->label('Nama Panggilan'),
            ExportColumn::make('gender')->label('Jenis Kelamin'),
            ExportColumn::make('birth_place')->label('Tempat Lahir'),
            ExportColumn::make('birth_date')->label('Tanggal Lahir'),
            ExportColumn::make('religion')->label('Agama'),
            ExportColumn::make('classroom.name')->label('Kelas'),
            ExportColumn::make('academicYear.name')->label('Tahun Ajaran'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('address')->label('Alamat'),
            ExportColumn::make('city')->label('Kota'),
            ExportColumn::make('province')->label('Provinsi'),
            ExportColumn::make('phone')->label('Telepon'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('previous_school')->label('Sekolah Asal'),
            ExportColumn::make('entry_year')->label('Tahun Masuk'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data siswa selesai. ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
