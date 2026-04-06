<?php

namespace App\Filament\SchoolAdmin\Resources\AssessmentResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessments extends ListRecords
{
    protected static string $resource = AssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Assessment')),
        ];
    }
}
