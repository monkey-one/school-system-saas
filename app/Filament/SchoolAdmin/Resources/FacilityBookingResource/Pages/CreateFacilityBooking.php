<?php

namespace App\Filament\SchoolAdmin\Resources\FacilityBookingResource\Pages;

use App\Filament\SchoolAdmin\Resources\FacilityBookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFacilityBooking extends CreateRecord
{
    protected static string $resource = FacilityBookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['booked_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
