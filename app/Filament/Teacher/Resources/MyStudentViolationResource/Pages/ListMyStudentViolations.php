<?php

namespace App\Filament\Teacher\Resources\MyStudentViolationResource\Pages;

use App\Filament\Teacher\Resources\MyStudentViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyStudentViolations extends ListRecords
{
    protected static string $resource = MyStudentViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
