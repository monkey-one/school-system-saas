<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\ViolationTypeResource\Pages;
use App\Models\ViolationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Catalogue of school rules and their discipline points (tata tertib).
class ViolationTypeResource extends Resource
{
    protected static ?string $model = ViolationType::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')->label(__('Violation'))->required()->maxLength(200)->columnSpan(2),
                        Forms\Components\Select::make('severity')->label(__('Severity'))->options(ViolationType::severityLabels())->default('light')->required(),
                        Forms\Components\TextInput::make('points')->label(__('Points'))->numeric()->minValue(1)->maxValue(100)->required(),
                        Forms\Components\Textarea::make('description')->label(__('Description'))->rows(2)->maxLength(1000)->columnSpan(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Violation'))->searchable()->wrap(),
                Tables\Columns\TextColumn::make('severity')
                    ->label(__('Severity'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'heavy' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ViolationType::severityLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('points')->label(__('Points'))->sortable(),
                Tables\Columns\TextColumn::make('violations_count')->label(__('Recorded'))->counts('violations'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('points');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('School Rules');
    }

    public static function getModelLabel(): string
    {
        return __('Violation type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('School Rules');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListViolationTypes::route('/'),
            'create' => Pages\CreateViolationType::route('/create'),
            'edit' => Pages\EditViolationType::route('/{record}/edit'),
        ];
    }
}
