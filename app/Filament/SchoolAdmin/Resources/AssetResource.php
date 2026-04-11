<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\AssetCondition;
use App\Filament\SchoolAdmin\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\CurrencyHelper;

// Manages school asset inventory and tracking
class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Asset Data'))
                    ->description('Informasi aset sekolah')
                    ->icon('heroicon-o-archive-box')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(__('Asset Code'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
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
                        Forms\Components\Select::make('condition')
                            ->label(__('Condition'))
                            ->options(AssetCondition::class)
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label(__('Location'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('Total'))
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label(__('Value'))
                            ->numeric()
                            ->prefix(CurrencyHelper::symbol()),
                        Forms\Components\DatePicker::make('acquisition_date')
                            ->label(__('Acquisition Date')),
                        Forms\Components\FileUpload::make('photo')
                            ->label(__('Photo'))
                            ->image()
                            ->directory('assets/photos')
                            ->maxSize(2048),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(2),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label(__('Condition'))
                    ->badge()
                    ->formatStateUsing(fn (AssetCondition $state) => $state->label())
                    ->color(fn (AssetCondition $state) => $state->color())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Total'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('condition')
                    ->label(__('Condition'))
                    ->options(AssetCondition::class),
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
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Assets');
    }

    public static function getModelLabel(): string
    {
        return __('Asset');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assets');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
