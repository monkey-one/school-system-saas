<?php

namespace App\Filament\Teacher\Resources\MyExamResource\Pages;

use App\Filament\Teacher\Resources\MyExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyExam extends EditRecord
{
    protected static string $resource = MyExamResource::class;

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
