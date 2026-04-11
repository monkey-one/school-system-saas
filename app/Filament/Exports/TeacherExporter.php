<?php

namespace App\Filament\Exports;

use App\Models\Teacher;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TeacherExporter extends Exporter
{
    protected static ?string $model = Teacher::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nip')->label('NIP'),
            ExportColumn::make('nuptk')->label('NUPTK'),
            ExportColumn::make('full_name')->label('Nama Lengkap'),
            ExportColumn::make('gender')->label('Jenis Kelamin'),
            ExportColumn::make('birth_place')->label('Tempat Lahir'),
            ExportColumn::make('birth_date')->label('Tanggal Lahir'),
            ExportColumn::make('religion')->label('Agama'),
            ExportColumn::make('employment_status')->label('Status Kepegawaian'),
            ExportColumn::make('grade_group')->label('Golongan'),
            ExportColumn::make('position')->label('Jabatan'),
            ExportColumn::make('education')->label('Pendidikan Terakhir'),
            ExportColumn::make('major')->label('Jurusan'),
            ExportColumn::make('phone')->label('Telepon'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('joined_at')->label('Tanggal Bergabung'),
            ExportColumn::make('is_homeroom_teacher')->label('Wali Kelas'),
            ExportColumn::make('homeroomClassroom.name')->label('Kelas Wali'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data guru selesai. ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
