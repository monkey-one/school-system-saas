<?php

namespace App\Filament\SchoolAdmin\Resources\AttendanceSessionResource\Pages;

use App\Filament\SchoolAdmin\Resources\AttendanceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceSessions extends ListRecords
{
    protected static string $resource = AttendanceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Sesi Absensi'),
        ];
    }
}
