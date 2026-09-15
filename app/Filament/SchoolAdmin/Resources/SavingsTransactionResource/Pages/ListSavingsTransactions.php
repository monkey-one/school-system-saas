<?php

namespace App\Filament\SchoolAdmin\Resources\SavingsTransactionResource\Pages;

use App\Filament\SchoolAdmin\Resources\SavingsTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavingsTransactions extends ListRecords
{
    protected static string $resource = SavingsTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
