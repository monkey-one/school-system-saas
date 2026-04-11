<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\ExtracurricularResource\Pages;
use App\Models\Extracurricular;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExtracurricularResource extends Resource
{
    protected static ?string $model = Extracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Extracurricular Data'))
                    ->icon('heroicon-o-trophy')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Advisor'))
                            ->relationship('teacher', 'full_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('schedule')
                            ->label(__('Schedule'))
                            ->maxLength(255)
                            ->placeholder('e.g. Senin & Rabu, 15:00-17:00'),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label(__('Advisor'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule')
                    ->label(__('Schedule'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Extracurriculars');
    }

    public static function getModelLabel(): string
    {
        return __('Extracurricular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Extracurriculars');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtracurriculars::route('/'),
            'create' => Pages\CreateExtracurricular::route('/create'),
            'edit' => Pages\EditExtracurricular::route('/{record}/edit'),
        ];
    }
}
