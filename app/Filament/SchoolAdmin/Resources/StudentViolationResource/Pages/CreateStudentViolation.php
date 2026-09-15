<?php

namespace App\Filament\SchoolAdmin\Resources\StudentViolationResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentViolationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentViolation extends CreateRecord
{
    protected static string $resource = StudentViolationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
