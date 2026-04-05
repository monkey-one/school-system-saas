<?php

namespace App\Filament\SchoolAdmin\Resources\SubjectResource\Pages;

use App\Filament\SchoolAdmin\Resources\SubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
