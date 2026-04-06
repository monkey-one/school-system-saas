<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\AssessmentTypeResource\Pages;
use App\Models\AssessmentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages assessment type definitions and categories
class AssessmentTypeResource extends Resource
{
    protected static ?string $model = AssessmentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Assessment Type Data'))
                    ->description('Informasi jenis penilaian')
                    ->icon('heroicon-o-clipboard-document')
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
                        Forms\Components\TextInput::make('default_weight')
                            ->label('Bobot Default (%)')
                            ->numeric()
                            ->step(0.01)
                            ->required(),
                        Forms\Components\Toggle::make('count_for_final')
                            ->label('Masuk Nilai Akhir')
                            ->default(true),
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
                Tables\Columns\TextColumn::make('default_weight')
                    ->label('Bobot (%)')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('count_for_final')
                    ->label('Nilai Akhir')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('count_for_final')
                    ->label('Masuk Nilai Akhir'),
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
        return __('Grading');
    }

    public static function getNavigationLabel(): string
    {
        return __('Assessment Types');
    }

    public static function getModelLabel(): string
    {
        return __('Assessment Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assessment Types');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessmentTypes::route('/'),
            'create' => Pages\CreateAssessmentType::route('/create'),
            'edit' => Pages\EditAssessmentType::route('/{record}/edit'),
        ];
    }
}
