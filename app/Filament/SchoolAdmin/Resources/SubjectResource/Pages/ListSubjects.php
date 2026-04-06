<?php

namespace App\Filament\SchoolAdmin\Resources\SubjectResource\Pages;

use App\Filament\SchoolAdmin\Resources\SubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Subject')),
        ];
    }
}
