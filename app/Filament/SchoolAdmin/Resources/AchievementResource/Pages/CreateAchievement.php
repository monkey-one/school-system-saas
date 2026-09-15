<?php

namespace App\Filament\SchoolAdmin\Resources\AchievementResource\Pages;

use App\Filament\SchoolAdmin\Resources\AchievementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAchievement extends CreateRecord
{
    protected static string $resource = AchievementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
