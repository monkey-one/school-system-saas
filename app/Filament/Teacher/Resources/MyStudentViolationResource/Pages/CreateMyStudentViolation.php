<?php

namespace App\Filament\Teacher\Resources\MyStudentViolationResource\Pages;

use App\Filament\Teacher\Resources\MyStudentViolationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyStudentViolation extends CreateRecord
{
    protected static string $resource = MyStudentViolationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reported_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
