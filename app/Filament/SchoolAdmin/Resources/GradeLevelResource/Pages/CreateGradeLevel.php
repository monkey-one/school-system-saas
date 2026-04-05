<?php

namespace App\Filament\SchoolAdmin\Resources\GradeLevelResource\Pages;

use App\Filament\SchoolAdmin\Resources\GradeLevelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGradeLevel extends CreateRecord
{
    protected static string $resource = GradeLevelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
