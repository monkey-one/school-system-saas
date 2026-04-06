<?php

namespace App\Filament\SchoolAdmin\Resources\FacilityResource\Pages;

use App\Filament\SchoolAdmin\Resources\FacilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacilities extends ListRecords
{
    protected static string $resource = FacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Facility')),
        ];
    }
}
