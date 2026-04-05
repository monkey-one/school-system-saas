<?php

namespace App\Filament\SchoolAdmin\Resources\FacilityResource\Pages;

use App\Filament\SchoolAdmin\Resources\FacilityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFacility extends CreateRecord
{
    protected static string $resource = FacilityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
