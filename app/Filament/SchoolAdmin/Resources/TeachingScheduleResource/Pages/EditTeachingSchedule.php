<?php

namespace App\Filament\SchoolAdmin\Resources\TeachingScheduleResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeachingScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeachingSchedule extends EditRecord
{
    protected static string $resource = TeachingScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
