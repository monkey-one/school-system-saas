<?php

namespace App\Filament\SchoolAdmin\Resources\ClassroomResource\Pages;

use App\Filament\SchoolAdmin\Resources\ClassroomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassroom extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
