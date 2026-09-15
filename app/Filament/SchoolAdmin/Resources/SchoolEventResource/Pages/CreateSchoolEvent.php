<?php

namespace App\Filament\SchoolAdmin\Resources\SchoolEventResource\Pages;

use App\Filament\SchoolAdmin\Resources\SchoolEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolEvent extends CreateRecord
{
    protected static string $resource = SchoolEventResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
