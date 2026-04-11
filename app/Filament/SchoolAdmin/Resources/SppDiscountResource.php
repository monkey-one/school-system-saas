<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\SppDiscountResource\Pages;
use App\Models\SppDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\CurrencyHelper;

class SppDiscountResource extends Resource
{
    protected static ?string $model = SppDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Discount Data'))
                    ->icon('heroicon-o-receipt-percent')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('grade_id')
                            ->label(__('Grade Level'))
                            ->relationship('grade', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Discount Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options([
                                'percentage' => __('Percentage (%)'),
                                'fixed' => __('Fixed Amount'),
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label(__('Value'))
                            ->numeric()
                            ->required()
                            ->minValue(0),
                        Forms\Components\DatePicker::make('valid_from')
                            ->label(__('Valid From'))
                            ->required(),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label(__('Valid Until'))
                            ->required()
                            ->afterOrEqual('valid_from'),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Discount Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'percentage' => __('Percentage'),
                        'fixed' => __('Fixed'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('Value'))
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? $record->value . '%' : CurrencyHelper::format($record->value))
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label(__('Valid From'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label(__('Valid Until'))
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'percentage' => __('Percentage'),
                        'fixed' => __('Fixed'),
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
            ->defaultSort('created_at', 'desc');
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
        return __('SPP Discounts');
    }

    public static function getModelLabel(): string
    {
        return __('SPP Discount');
    }

    public static function getPluralModelLabel(): string
    {
        return __('SPP Discounts');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppDiscounts::route('/'),
            'create' => Pages\CreateSppDiscount::route('/create'),
            'edit' => Pages\EditSppDiscount::route('/{record}/edit'),
        ];
    }
}
