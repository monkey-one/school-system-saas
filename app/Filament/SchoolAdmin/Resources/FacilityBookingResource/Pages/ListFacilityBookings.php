<?php

namespace App\Filament\SchoolAdmin\Resources\FacilityBookingResource\Pages;

use App\Filament\SchoolAdmin\Resources\FacilityBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacilityBookings extends ListRecords
{
    protected static string $resource = FacilityBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Add Booking')),
        ];
    }
}
