<?php

namespace App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentExtracurriculars extends ListRecords
{
    protected static string $resource = StudentExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Enrollment')),
        ];
    }
}
