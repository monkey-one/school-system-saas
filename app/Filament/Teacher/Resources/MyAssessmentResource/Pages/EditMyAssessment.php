<?php

namespace App\Filament\Teacher\Resources\MyAssessmentResource\Pages;

use App\Filament\Teacher\Resources\MyAssessmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyAssessment extends EditRecord
{
    protected static string $resource = MyAssessmentResource::class;

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
