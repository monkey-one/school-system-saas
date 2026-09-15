<?php

namespace App\Filament\Teacher\Resources\MyMessageResource\Pages;

use App\Filament\Teacher\Resources\MyMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListMyMessages extends ListRecords
{
    protected static string $resource = MyMessageResource::class;
}
