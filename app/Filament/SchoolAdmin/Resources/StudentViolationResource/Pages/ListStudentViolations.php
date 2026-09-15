<?php

namespace App\Filament\SchoolAdmin\Resources\StudentViolationResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentViolations extends ListRecords
{
    protected static string $resource = StudentViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
