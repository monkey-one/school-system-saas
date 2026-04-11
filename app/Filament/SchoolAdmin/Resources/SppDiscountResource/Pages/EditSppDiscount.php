<?php

namespace App\Filament\SchoolAdmin\Resources\SppDiscountResource\Pages;

use App\Filament\SchoolAdmin\Resources\SppDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSppDiscount extends EditRecord
{
    protected static string $resource = SppDiscountResource::class;

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
