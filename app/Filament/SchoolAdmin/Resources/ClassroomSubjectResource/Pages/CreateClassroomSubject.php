<?php

namespace App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource\Pages;

use App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassroomSubject extends CreateRecord
{
    protected static string $resource = ClassroomSubjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
