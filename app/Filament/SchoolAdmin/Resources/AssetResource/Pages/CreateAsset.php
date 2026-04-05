<?php

namespace App\Filament\SchoolAdmin\Resources\AssetResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
