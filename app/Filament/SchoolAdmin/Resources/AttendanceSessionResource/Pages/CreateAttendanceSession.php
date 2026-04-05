<?php

namespace App\Filament\SchoolAdmin\Resources\AttendanceSessionResource\Pages;

use App\Filament\SchoolAdmin\Resources\AttendanceSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceSession extends CreateRecord
{
    protected static string $resource = AttendanceSessionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
