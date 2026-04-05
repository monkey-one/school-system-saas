<?php

namespace App\Filament\SchoolAdmin\Resources\SemesterResource\Pages;

use App\Filament\SchoolAdmin\Resources\SemesterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSemester extends CreateRecord
{
    protected static string $resource = SemesterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
