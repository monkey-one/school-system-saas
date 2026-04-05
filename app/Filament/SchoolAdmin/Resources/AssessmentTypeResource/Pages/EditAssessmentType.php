<?php

namespace App\Filament\SchoolAdmin\Resources\AssessmentTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssessmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssessmentType extends EditRecord
{
    protected static string $resource = AssessmentTypeResource::class;

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
