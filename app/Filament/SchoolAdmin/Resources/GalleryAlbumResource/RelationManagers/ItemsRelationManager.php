<?php

namespace App\Filament\SchoolAdmin\Resources\GalleryAlbumResource\RelationManagers;

use App\Models\GalleryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

// Photos (uploads) and videos (YouTube links) inside an album.
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Photos & videos');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Radio::make('type')
                    ->label(__('Type'))
                    ->options(['photo' => __('Photo'), 'video' => __('YouTube video')])
                    ->default('photo')
                    ->inline()
                    ->live()
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label(__('Photo'))
                    ->image()
                    ->directory('website/gallery')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->visible(fn (Forms\Get $get) => $get('type') === 'photo')
                    ->required(fn (Forms\Get $get) => $get('type') === 'photo'),
                Forms\Components\TextInput::make('video_url')
                    ->label(__('YouTube link'))
                    ->url()
                    ->regex('~^https://(www\.)?(youtube\.com|youtu\.be)/~')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'video')
                    ->required(fn (Forms\Get $get) => $get('type') === 'video'),
                Forms\Components\TextInput::make('caption')
                    ->label(__('Caption'))
                    ->maxLength(200),
                Forms\Components\TextInput::make('sort_order')
                    ->label(__('Order'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->square()
                    ->size(64)
                    ->defaultImageUrl(fn (GalleryItem $record) => $record->youtubeId() ? "https://img.youtube.com/vi/{$record->youtubeId()}/mqdefault.jpg" : null),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'video' ? __('Video') : __('Photo')),
                Tables\Columns\TextColumn::make('caption')->label(__('Caption'))->limit(50),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('Add photo / video')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order');
    }
}
