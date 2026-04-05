<?php

namespace App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource\Pages;

use App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassroomSubject extends EditRecord
{
    protected static string $resource = ClassroomSubjectResource::class;

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
