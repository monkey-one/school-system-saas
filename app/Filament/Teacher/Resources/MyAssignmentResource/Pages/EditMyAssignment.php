<?php

namespace App\Filament\Teacher\Resources\MyAssignmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyAssignment extends EditRecord
{
    protected static string $resource = MyAssignmentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['teacher_id'] = \App\Filament\Teacher\Resources\MyAssignmentResource::teacherId();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
