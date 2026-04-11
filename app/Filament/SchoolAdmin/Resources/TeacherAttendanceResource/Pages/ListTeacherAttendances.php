<?php

namespace App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherAttendances extends ListRecords
{
    protected static string $resource = TeacherAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Attendance')),
        ];
    }
}
