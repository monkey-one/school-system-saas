<?php

namespace App\Filament\SchoolAdmin\Resources\ClassroomResource\Pages;

use App\Filament\SchoolAdmin\Resources\ClassroomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassrooms extends ListRecords
{
    protected static string $resource = ClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Classroom')),
        ];
    }
}
