<?php

namespace App\Filament\SchoolAdmin\Resources\AlumniResource\Pages;

use App\Filament\SchoolAdmin\Resources\AlumniResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlumni extends ListRecords
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
