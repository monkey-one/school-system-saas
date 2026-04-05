<?php

namespace App\Filament\Teacher\Resources\MyAttendanceSessionResource\Pages;

use App\Filament\Teacher\Resources\MyAttendanceSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyAttendanceSession extends EditRecord
{
    protected static string $resource = MyAttendanceSessionResource::class;

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
