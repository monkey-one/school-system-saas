<?php

namespace App\Filament\SchoolAdmin\Resources\ViolationTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\ViolationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditViolationType extends EditRecord
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
