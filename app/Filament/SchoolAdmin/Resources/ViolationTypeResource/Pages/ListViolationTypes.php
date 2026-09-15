<?php

namespace App\Filament\SchoolAdmin\Resources\ViolationTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\ViolationTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListViolationTypes extends ListRecords
{
    protected static string $resource = ViolationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
