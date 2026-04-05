<?php

namespace App\Filament\Teacher\Resources\MyAssessmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyAssessments extends ListRecords
{
    protected static string $resource = MyAssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Penilaian'),
        ];
    }
}
