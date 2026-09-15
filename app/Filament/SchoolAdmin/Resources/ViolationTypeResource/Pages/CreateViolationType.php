<?php

namespace App\Filament\SchoolAdmin\Resources\ViolationTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\ViolationTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateViolationType extends CreateRecord
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
