<?php

namespace App\Filament\SchoolAdmin\Resources\SppBillResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppBillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSppBill extends EditRecord
{
    protected static string $resource = SppBillResource::class;

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
