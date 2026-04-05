<?php

namespace App\Filament\SchoolAdmin\Resources\CurriculumSettingResource\Pages;

use App\Filament\SchoolAdmin\Resources\CurriculumSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurriculumSetting extends CreateRecord
{
    protected static string $resource = CurriculumSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
