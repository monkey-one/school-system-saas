<?php

namespace App\Filament\SchoolAdmin\Resources\AssessmentTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssessmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentTypes extends ListRecords
{
    protected static string $resource = AssessmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jenis Penilaian'),
        ];
    }
}
