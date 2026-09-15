<?php

namespace App\Filament\Teacher\Resources\MyExamResource\Pages;

use App\Filament\Teacher\Resources\MyExamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyExams extends ListRecords
{
    protected static string $resource = MyExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
