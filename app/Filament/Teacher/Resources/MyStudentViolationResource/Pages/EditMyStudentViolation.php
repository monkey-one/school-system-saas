<?php

namespace App\Filament\Teacher\Resources\MyStudentViolationResource\Pages;

use App\Filament\Teacher\Resources\MyStudentViolationResource;
use Filament\Resources\Pages\EditRecord;

class EditMyStudentViolation extends EditRecord
{
    protected static string $resource = MyStudentViolationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
