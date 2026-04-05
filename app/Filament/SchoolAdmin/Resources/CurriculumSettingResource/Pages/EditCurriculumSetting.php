<?php

namespace App\Filament\SchoolAdmin\Resources\CurriculumSettingResource\Pages;

use App\Filament\SchoolAdmin\Resources\CurriculumSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCurriculumSetting extends EditRecord
{
    protected static string $resource = CurriculumSettingResource::class;

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
