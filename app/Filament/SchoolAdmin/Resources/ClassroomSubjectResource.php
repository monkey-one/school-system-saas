<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\ClassroomSubjectResource\Pages;
use App\Models\ClassroomSubject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages subject assignments to classrooms
class ClassroomSubjectResource extends Resource
{
    protected static ?string $model = ClassroomSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Subject Schedule Data'))
                    ->description('Penugasan mata pelajaran ke kelas')
                    ->icon('heroicon-o-table-cells')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('classroom_id')
                            ->label(__('Classroom'))
                            ->relationship('classroom', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('subject_id')
                            ->label(__('Subject'))
                            ->relationship('subject', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('hours_per_week')
                            ->label('Jam per Minggu')
                            ->numeric()
                            ->required()
                            ->default(2),
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('semester_id')
                            ->label(__('Semester'))
                            ->relationship('semester', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label(__('Classroom'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label(__('Teacher'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hours_per_week')
                    ->label('Jam/Minggu')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label(__('Academic Year'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label(__('Semester'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->label(__('Classroom'))
                    ->relationship('classroom', 'name'),
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label(__('Subject'))
                    ->relationship('subject', 'name'),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'full_name'),
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
        return __('Academic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Subject Schedules');
    }

    public static function getModelLabel(): string
    {
        return __('Subject Schedule');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Subject Schedules');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassroomSubjects::route('/'),
            'create' => Pages\CreateClassroomSubject::route('/create'),
            'edit' => Pages\EditClassroomSubject::route('/{record}/edit'),
        ];
    }
}
