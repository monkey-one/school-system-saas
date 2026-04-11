<?php

namespace App\Filament\SchoolAdmin\Resources\ReportCardResource\Pages;

use App\Filament\SchoolAdmin\Resources\ReportCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportCard extends EditRecord
{
    protected static string $resource = ReportCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
