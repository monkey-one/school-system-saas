<?php

namespace App\Filament\SchoolAdmin\Resources\CounselingNoteResource\Pages;

use App\Filament\SchoolAdmin\Resources\CounselingNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCounselingNote extends EditRecord
{
    protected static string $resource = CounselingNoteResource::class;

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
