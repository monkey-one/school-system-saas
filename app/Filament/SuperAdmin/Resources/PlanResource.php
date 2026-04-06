<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages subscription plan definitions and pricing
class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Manajemen Tenant';

    protected static ?string $navigationLabel = 'Paket Langganan';

    protected static ?string $modelLabel = 'Paket Langganan';

    protected static ?string $pluralModelLabel = 'Paket Langganan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Paket')
                    ->icon('heroicon-o-rectangle-stack')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('price_monthly')
                            ->label('Harga Bulanan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('price_annual')
                            ->label('Harga Tahunan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('max_students')
                            ->label('Maks. Siswa')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('max_teachers')
                            ->label('Maks. Guru')
                            ->numeric()
                            ->required(),
                        Forms\Components\Repeater::make('features')
                            ->label('Fitur')
                            ->simple(
                                Forms\Components\TextInput::make('feature')
                                    ->label('Fitur')
                                    ->required(),
                            )
                            ->columnSpanFull()
                            ->defaultItems(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Paket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('Harga Bulanan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_annual')
                    ->label('Harga Tahunan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->label('Maks. Siswa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_teachers')
                    ->label('Maks. Guru')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
