<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\SchoolEventResource\Pages;
use App\Models\SchoolEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// School agenda / academic calendar shown on the website and TV display.
class SchoolEventResource extends Resource
{
    protected static ?string $model = SchoolEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Event'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category')
                            ->label(__('Category'))
                            ->options(SchoolEvent::categoryLabels())
                            ->default('academic')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label(__('Location'))
                            ->maxLength(150),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label(__('Starts'))
                            ->seconds(false)
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label(__('Ends'))
                            ->seconds(false)
                            ->afterOrEqual('starts_at'),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('Show on website'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')
                    ->label(__('Date'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => SchoolEvent::categoryLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label(__('Published'))
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('Category'))
                    ->options(SchoolEvent::categoryLabels()),
                Tables\Filters\Filter::make('upcoming')
                    ->label(__('Upcoming only'))
                    ->query(fn ($query) => $query->upcoming())
                    ->default(),
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
            ->defaultSort('starts_at');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getNavigationLabel(): string
    {
        return __('Agenda');
    }

    public static function getModelLabel(): string
    {
        return __('Event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Agenda');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolEvents::route('/'),
            'create' => Pages\CreateSchoolEvent::route('/create'),
            'edit' => Pages\EditSchoolEvent::route('/{record}/edit'),
        ];
    }
}
