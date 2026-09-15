<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\GalleryAlbumResource\Pages;
use App\Filament\SchoolAdmin\Resources\GalleryAlbumResource\RelationManagers\ItemsRelationManager;
use App\Models\GalleryAlbum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Photo & video albums of school activities for the website gallery.
class GalleryAlbumResource extends Resource
{
    protected static ?string $model = GalleryAlbum::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Album'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('event_date')
                            ->label(__('Activity date')),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('Show on website'))
                            ->default(true)
                            ->inline(false),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')
                            ->label(__('Cover image'))
                            ->image()
                            ->directory('website/gallery')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('')->square()->size(56),
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable()->limit(50),
                Tables\Columns\TextColumn::make('items_count')->label(__('Items'))->counts('items')->badge(),
                Tables\Columns\TextColumn::make('event_date')->label(__('Activity date'))->date('d M Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getNavigationLabel(): string
    {
        return __('Gallery');
    }

    public static function getModelLabel(): string
    {
        return __('Album');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Gallery');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryAlbums::route('/'),
            'create' => Pages\CreateGalleryAlbum::route('/create'),
            'edit' => Pages\EditGalleryAlbum::route('/{record}/edit'),
        ];
    }
}
