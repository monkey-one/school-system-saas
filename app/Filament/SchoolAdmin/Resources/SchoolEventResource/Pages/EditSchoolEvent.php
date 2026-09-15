<?php

namespace App\Filament\SchoolAdmin\Resources\SchoolEventResource\Pages;

use App\Filament\SchoolAdmin\Resources\SchoolEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolEvent extends EditRecord
{
    protected static string $resource = SchoolEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
