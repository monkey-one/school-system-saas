<?php

namespace App\Filament\Teacher\Resources\MyAttendanceSessionResource\Pages;

use App\Filament\Teacher\Resources\MyAttendanceSessionResource;
use App\Models\Teacher;
use Filament\Resources\Pages\CreateRecord;

class CreateMyAttendanceSession extends CreateRecord
{
    protected static string $resource = MyAttendanceSessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $data['teacher_id'] = $teacher?->id;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
