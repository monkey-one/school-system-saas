<?php

namespace App\Filament\SuperAdmin\Resources\RoleResource\Pages;

use App\Filament\SuperAdmin\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
