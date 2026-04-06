<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages library book catalog and inventory
class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Book Data'))
                    ->description('Informasi buku perpustakaan')
                    ->icon('heroicon-o-book-open')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('isbn')
                            ->label(__('ISBN'))
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('author')
                            ->label(__('Author'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('publisher')
                            ->label(__('Publisher'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year')
                            ->label(__('Publication Year'))
                            ->numeric(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('Category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                            ]),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('available_stock')
                            ->label('Stok Tersedia')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi Rak')
                            ->maxLength(100),
                        Forms\Components\FileUpload::make('cover_path')
                            ->label('Sampul')
                            ->image()
                            ->directory('books/covers')
                            ->maxSize(2048),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('isbn')
                    ->label(__('ISBN'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('author')
                    ->label(__('Author'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('available_stock')
                    ->label(__('Available'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name'),
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
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Library');
    }

    public static function getNavigationLabel(): string
    {
        return __('Books');
    }

    public static function getModelLabel(): string
    {
        return __('Book');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Books');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
