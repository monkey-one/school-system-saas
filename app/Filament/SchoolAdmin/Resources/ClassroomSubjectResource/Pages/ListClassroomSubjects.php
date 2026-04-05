<?php

namespace App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource\Pages;

use App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassroomSubjects extends ListRecords
{
    protected static string $resource = ClassroomSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jadwal Mapel'),
        ];
    }
}
