<?php

namespace App\Filament\SchoolAdmin\Resources\FacilityBookingResource\Pages;

use App\Filament\SchoolAdmin\Resources\FacilityBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacilityBooking extends EditRecord
{
    protected static string $resource = FacilityBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
