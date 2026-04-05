<?php

namespace App\Filament\SchoolAdmin\Resources\CurriculumSettingResource\Pages;

use App\Filament\SchoolAdmin\Resources\CurriculumSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurriculumSettings extends ListRecords
{
    protected static string $resource = CurriculumSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Kurikulum'),
        ];
    }
}
