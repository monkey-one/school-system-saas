<?php

namespace App\Filament\SuperAdmin\Resources\ActivityLogResource\Pages;

use App\Filament\SuperAdmin\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;
}
