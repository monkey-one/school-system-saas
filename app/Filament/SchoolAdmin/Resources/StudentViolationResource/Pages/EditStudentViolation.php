<?php

namespace App\Filament\SchoolAdmin\Resources\StudentViolationResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentViolationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentViolation extends EditRecord
{
    protected static string $resource = StudentViolationResource::class;

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
