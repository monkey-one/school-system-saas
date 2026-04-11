<?php

namespace App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource\Pages;

use App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherAttendance extends EditRecord
{
    protected static string $resource = TeacherAttendanceResource::class;

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
