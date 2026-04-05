<?php

namespace App\Filament\SchoolAdmin\Resources\AssessmentResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
