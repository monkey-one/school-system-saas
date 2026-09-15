<?php

namespace App\Filament\Teacher\Resources\MyExamResource\Pages;

use App\Filament\Teacher\Resources\MyExamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyExam extends CreateRecord
{
    protected static string $resource = MyExamResource::class;

    // Always the signed-in teacher, whatever the form state says.
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = \App\Filament\Teacher\Resources\MyAssignmentResource::teacherId();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
