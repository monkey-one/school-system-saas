<?php

namespace App\Filament\Teacher\Resources\StudentGradeResource\Pages;

use App\Filament\Teacher\Resources\StudentGradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentGrade extends CreateRecord
{
    protected static string $resource = StudentGradeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['graded_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
