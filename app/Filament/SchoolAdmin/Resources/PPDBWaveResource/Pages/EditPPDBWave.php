<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBWaveResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBWaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPPDBWave extends EditRecord
{
    protected static string $resource = PPDBWaveResource::class;

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
