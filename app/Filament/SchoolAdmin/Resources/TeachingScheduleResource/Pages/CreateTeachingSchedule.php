<?php

namespace App\Filament\SchoolAdmin\Resources\TeachingScheduleResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeachingScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeachingSchedule extends CreateRecord
{
    protected static string $resource = TeachingScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
