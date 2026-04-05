<?php

namespace App\Filament\Teacher\Resources\MyAssessmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyAssessment extends CreateRecord
{
    protected static string $resource = MyAssessmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
