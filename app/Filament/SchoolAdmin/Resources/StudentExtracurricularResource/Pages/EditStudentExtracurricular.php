<?php

namespace App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource\Pages;

use App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentExtracurricular extends EditRecord
{
    protected static string $resource = StudentExtracurricularResource::class;

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
