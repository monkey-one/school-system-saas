<?php

namespace App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacherAttendance extends CreateRecord
{
    protected static string $resource = TeacherAttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
