<?php

namespace App\Filament\SchoolAdmin\Resources\GalleryAlbumResource\Pages;

use App\Filament\SchoolAdmin\Resources\GalleryAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGalleryAlbum extends EditRecord
{
    protected static string $resource = GalleryAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
