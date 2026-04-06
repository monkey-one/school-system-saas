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

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Plan Information'))
                    ->icon('heroicon-o-rectangle-stack')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Plan Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('price_monthly')
                            ->label(__('Monthly Price'))
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('price_annual')
                            ->label(__('Annual Price'))
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('max_students')
                            ->label(__('Max. Students'))
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('max_teachers')
                            ->label(__('Max. Teachers'))
                            ->numeric()
                            ->required(),
                        Forms\Components\Repeater::make('features')
                            ->label(__('Features'))
                            ->simple(
                                Forms\Components\TextInput::make('feature')
                                    ->label(__('Features'))
                                    ->required(),
                            )
                            ->columnSpanFull()
                            ->defaultItems(1),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('Sort Order'))
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
                    ->label(__('Plan Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->label(__('Monthly Price'))
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_annual')
                    ->label(__('Annual Price'))
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students')
                    ->label(__('Max. Students'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_teachers')
                    ->label(__('Max. Teachers'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('Sort Order'))
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
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

    public static function getNavigationGroup(): ?string
    {
        return __('Tenant Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Subscription Plans');
    }

    public static function getModelLabel(): string
    {
        return __('Subscription Plan');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Subscription Plans');
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
