<?php

namespace App\Filament\SchoolAdmin\Resources\SppBillResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppBillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSppBill extends CreateRecord
{
    protected static string $resource = SppBillResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
