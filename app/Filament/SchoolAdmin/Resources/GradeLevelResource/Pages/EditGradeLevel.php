<?php

namespace App\Filament\SchoolAdmin\Resources\GradeLevelResource\Pages;

use App\Filament\SchoolAdmin\Resources\GradeLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGradeLevel extends EditRecord
{
    protected static string $resource = GradeLevelResource::class;

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
