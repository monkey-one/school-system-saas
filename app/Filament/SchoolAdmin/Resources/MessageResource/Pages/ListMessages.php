<?php

namespace App\Filament\SchoolAdmin\Resources\MessageResource\Pages;

use App\Filament\SchoolAdmin\Resources\MessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('New Message')),
        ];
    }
}
