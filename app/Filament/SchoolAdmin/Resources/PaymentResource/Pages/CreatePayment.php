<?php

namespace App\Filament\SchoolAdmin\Resources\PaymentResource\Pages;

use App\Filament\SchoolAdmin\Resources\PaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
