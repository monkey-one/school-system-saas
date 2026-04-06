<?php

namespace App\Filament\SchoolAdmin\Resources\AnnouncementResource\Pages;

use App\Filament\SchoolAdmin\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Announcement')),
        ];
    }
}
