<?php

namespace App\Filament\SchoolAdmin\Resources\ReportCardResource\Pages;

use App\Filament\SchoolAdmin\Resources\ReportCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportCards extends ListRecords
{
    protected static string $resource = ReportCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Report Card')),
        ];
    }
}
