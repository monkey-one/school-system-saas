<?php

namespace App\Filament\SchoolAdmin\Resources\NotificationTemplateResource\Pages;

use App\Filament\SchoolAdmin\Resources\NotificationTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationTemplate extends CreateRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
