<?php

namespace App\Filament\SchoolAdmin\Resources\BookLoanResource\Pages;

use App\Filament\SchoolAdmin\Resources\BookLoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookLoan extends CreateRecord
{
    protected static string $resource = BookLoanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
