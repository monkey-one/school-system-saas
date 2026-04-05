<?php

namespace App\Filament\SchoolAdmin\Resources\GradeLevelResource\Pages;

use App\Filament\SchoolAdmin\Resources\GradeLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradeLevels extends ListRecords
{
    protected static string $resource = GradeLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Tingkat'),
        ];
    }
}
