<?php

namespace App\Filament\SchoolAdmin\Resources\AcademicYearResource\Pages;

use App\Filament\SchoolAdmin\Resources\AcademicYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicYears extends ListRecords
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Academic Year')),
        ];
    }
}
