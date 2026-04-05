<?php

namespace App\Filament\SchoolAdmin\Resources\SppTypeResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSppTypes extends ListRecords
{
    protected static string $resource = SppTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jenis SPP'),
        ];
    }
}
