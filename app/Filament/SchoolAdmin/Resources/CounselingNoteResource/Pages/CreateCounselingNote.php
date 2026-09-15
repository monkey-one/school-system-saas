<?php

namespace App\Filament\SchoolAdmin\Resources\CounselingNoteResource\Pages;

use App\Filament\SchoolAdmin\Resources\CounselingNoteResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCounselingNote extends CreateRecord
{
    protected static string $resource = CounselingNoteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
