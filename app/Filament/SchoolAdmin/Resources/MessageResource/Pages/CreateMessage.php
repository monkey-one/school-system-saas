<?php

namespace App\Filament\SchoolAdmin\Resources\MessageResource\Pages;

use App\Filament\SchoolAdmin\Resources\MessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMessage extends CreateRecord
{
    protected static string $resource = MessageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
