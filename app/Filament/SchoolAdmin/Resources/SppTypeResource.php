<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\SppTypeResource\Pages;
use App\Models\SppType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\CurrencyHelper;

// Manages tuition fee type definitions
class SppTypeResource extends Resource
{
    protected static ?string $model = SppType::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Tuition Type Data'))
                    ->description('Informasi jenis pembayaran SPP')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label(__('Code'))
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->prefix(CurrencyHelper::symbol())
                            ->required(),
                        Forms\Components\Select::make('frequency')
                            ->label('Frekuensi')
                            ->options([
                                'monthly' => 'Bulanan',
                                'semester' => 'Per Semester',
                                'yearly' => 'Tahunan',
                                'once' => 'Sekali Bayar',
                            ])
                            ->required(),
                        Forms\Components\Select::make('applies_to')
                            ->label('Berlaku Untuk')
                            ->options([
                                'all' => 'Semua Siswa',
                                'new' => 'Siswa Baru',
                                'specific' => 'Tertentu',
                            ])
                            ->default('all'),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Total'))
                    ->money(CurrencyHelper::code())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->label('Frekuensi')
                    ->options([
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar',
                    ]),
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
        return __('Finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tuition Types');
    }

    public static function getModelLabel(): string
    {
        return __('Tuition Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Tuition Types');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppTypes::route('/'),
            'create' => Pages\CreateSppType::route('/create'),
            'edit' => Pages\EditSppType::route('/{record}/edit'),
        ];
    }
}
