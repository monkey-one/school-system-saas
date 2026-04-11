<?php

namespace App\Filament\SchoolAdmin\Resources\ExtracurricularResource\Pages;

use App\Filament\SchoolAdmin\Resources\ExtracurricularResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExtracurricular extends EditRecord
{
    protected static string $resource = ExtracurricularResource::class;

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
