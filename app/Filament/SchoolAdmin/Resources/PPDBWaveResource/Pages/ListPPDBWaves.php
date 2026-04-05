<?php

namespace App\Filament\SchoolAdmin\Resources\PPDBWaveResource\Pages;

use App\Filament\SchoolAdmin\Resources\PPDBWaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPPDBWaves extends ListRecords
{
    protected static string $resource = PPDBWaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Gelombang PPDB'),
        ];
    }
}
