<?php

namespace App\Filament\SchoolAdmin\Resources\AssetResource\Pages;

use App\Filament\SchoolAdmin\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Asset')),
        ];
    }
}
