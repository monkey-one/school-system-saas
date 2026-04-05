<?php

namespace App\Filament\SchoolAdmin\Resources\AssessmentTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssessmentTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessmentType extends CreateRecord
{
    protected static string $resource = AssessmentTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
