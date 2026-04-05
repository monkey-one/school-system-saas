<?php

namespace App\Filament\SchoolAdmin\Resources\SppTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSppType extends EditRecord
{
    protected static string $resource = SppTypeResource::class;

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
