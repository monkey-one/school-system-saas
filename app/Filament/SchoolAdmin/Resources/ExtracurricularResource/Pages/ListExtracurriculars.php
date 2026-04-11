<?php

namespace App\Filament\SchoolAdmin\Resources\ExtracurricularResource\Pages;

use App\Filament\SchoolAdmin\Resources\ExtracurricularResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExtracurriculars extends ListRecords
{
    protected static string $resource = ExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Extracurricular')),
        ];
    }
}
