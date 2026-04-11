<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\StudentExtracurricularResource\Pages;
use App\Models\StudentExtracurricular;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentExtracurricularResource extends Resource
{
    protected static ?string $model = StudentExtracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Enrollment Data'))
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('extracurricular_id')
                            ->label(__('Extracurricular'))
                            ->relationship('extracurricular', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('score')
                            ->label(__('Score'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
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
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extracurricular.name')
                    ->label(__('Extracurricular'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label(__('Academic Year'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->label(__('Score'))
                    ->sortable()
                    ->badge()
                    ->color(fn (?string $state) => match (true) {
                        $state === null => 'gray',
                        (int) $state >= 80 => 'success',
                        (int) $state >= 60 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('extracurricular_id')
                    ->label(__('Extracurricular'))
                    ->relationship('extracurricular', 'name'),
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label(__('Academic Year'))
                    ->relationship('academicYear', 'name'),
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
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Extracurriculars');
    }

    public static function getModelLabel(): string
    {
        return __('Student Extracurricular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Extracurriculars');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentExtracurriculars::route('/'),
            'create' => Pages\CreateStudentExtracurricular::route('/create'),
            'edit' => Pages\EditStudentExtracurricular::route('/{record}/edit'),
        ];
    }
}
