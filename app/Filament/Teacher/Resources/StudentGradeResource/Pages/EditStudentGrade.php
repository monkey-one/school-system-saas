<?php

namespace App\Filament\Teacher\Resources\StudentGradeResource\Pages;

use App\Filament\Teacher\Resources\StudentGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentGrade extends EditRecord
{
    protected static string $resource = StudentGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
