<?php

namespace App\Filament\Teacher\Resources\MyAssignmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyAssignment extends CreateRecord
{
    protected static string $resource = MyAssignmentResource::class;

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
