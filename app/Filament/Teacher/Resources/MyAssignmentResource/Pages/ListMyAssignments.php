<?php

namespace App\Filament\Teacher\Resources\MyAssignmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAssignments extends ListRecords
{
    protected static string $resource = MyAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
