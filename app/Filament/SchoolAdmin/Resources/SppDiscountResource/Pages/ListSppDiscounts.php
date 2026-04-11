<?php

namespace App\Filament\SchoolAdmin\Resources\SppDiscountResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSppDiscounts extends ListRecords
{
    protected static string $resource = SppDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Discount')),
        ];
    }
}
