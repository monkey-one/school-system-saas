<?php

namespace App\Filament\SchoolAdmin\Resources\TeachingScheduleResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeachingScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachingSchedules extends ListRecords
{
    protected static string $resource = TeachingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jadwal'),
        ];
    }
}
