<?php

namespace App\Filament\SchoolAdmin\Resources\TeacherResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeacherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
