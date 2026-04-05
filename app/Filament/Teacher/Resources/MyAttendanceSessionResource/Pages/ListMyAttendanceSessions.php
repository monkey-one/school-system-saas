<?php

namespace App\Filament\Teacher\Resources\MyAttendanceSessionResource\Pages;

use App\Filament\Teacher\Resources\MyAttendanceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAttendanceSessions extends ListRecords
{
    protected static string $resource = MyAttendanceSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Sesi Absensi'),
        ];
    }
}
