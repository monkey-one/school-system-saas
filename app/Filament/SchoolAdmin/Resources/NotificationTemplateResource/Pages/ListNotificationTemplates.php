<?php

namespace App\Filament\SchoolAdmin\Resources\NotificationTemplateResource\Pages;

use App\Filament\SchoolAdmin\Resources\NotificationTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationTemplates extends ListRecords
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Template')),
        ];
    }
}
