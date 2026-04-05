<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPPDBRegistrations extends ListRecords
{
    protected static string $resource = PPDBRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pendaftaran'),
        ];
    }
}
