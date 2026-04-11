<?php

namespace App\Filament\SchoolAdmin\Resources\SppDiscountResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppDiscountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSppDiscount extends CreateRecord
{
    protected static string $resource = SppDiscountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
