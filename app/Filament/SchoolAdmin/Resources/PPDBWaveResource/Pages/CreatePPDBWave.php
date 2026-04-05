<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBWaveResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBWaveResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePPDBWave extends CreateRecord
{
    protected static string $resource = PPDBWaveResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
