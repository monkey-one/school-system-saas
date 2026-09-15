<?php

namespace App\Filament\SchoolAdmin\Resources\GalleryAlbumResource\Pages;

use App\Filament\SchoolAdmin\Resources\GalleryAlbumResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGalleryAlbum extends CreateRecord
{
    protected static string $resource = GalleryAlbumResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
