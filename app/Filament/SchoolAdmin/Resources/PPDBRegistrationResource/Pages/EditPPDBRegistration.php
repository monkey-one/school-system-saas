<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPPDBRegistration extends EditRecord
{
    protected static string $resource = PPDBRegistrationResource::class;

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
