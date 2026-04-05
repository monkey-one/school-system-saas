<?php

namespace App\Filament\SchoolAdmin\Resources\SppTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSppType extends CreateRecord
{
    protected static string $resource = SppTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
