<?php

namespace App\Filament\SchoolAdmin\Resources\ReportCardResource\Pages;

use App\Filament\SchoolAdmin\Resources\ReportCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReportCard extends CreateRecord
{
    protected static string $resource = ReportCardResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
