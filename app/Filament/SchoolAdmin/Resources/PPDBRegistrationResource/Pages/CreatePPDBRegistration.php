<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePPDBRegistration extends CreateRecord
{
    protected static string $resource = PPDBRegistrationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
