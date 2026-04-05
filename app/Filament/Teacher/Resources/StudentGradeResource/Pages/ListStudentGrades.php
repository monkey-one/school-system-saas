<?php

namespace App\Filament\Teacher\Resources\StudentGradeResource\Pages;

use App\Filament\Teacher\Resources\StudentGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentGrades extends ListRecords
{
    protected static string $resource = StudentGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Nilai'),
        ];
    }
}
