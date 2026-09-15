<?php

namespace App\Filament\SchoolAdmin\Resources\CounselingNoteResource\Pages;

use App\Filament\SchoolAdmin\Resources\CounselingNoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCounselingNotes extends ListRecords
{
    protected static string $resource = CounselingNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
