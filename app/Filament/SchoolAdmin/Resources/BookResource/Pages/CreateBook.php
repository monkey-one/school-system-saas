<?php

namespace App\Filament\SchoolAdmin\Resources\BookResource\Pages;

use App\Filament\SchoolAdmin\Resources\BookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
