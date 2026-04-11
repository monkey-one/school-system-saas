<?php

namespace App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentExtracurricular extends CreateRecord
{
    protected static string $resource = StudentExtracurricularResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
