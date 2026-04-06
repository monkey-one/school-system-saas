<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

// Manages classroom definitions and student assignments
class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Classroom Data'))
                    ->description('Informasi kelas')
                    ->icon('heroicon-o-building-library')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('grade_id')
                            ->label(__('Grade Level'))
                            ->relationship('grade', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Classroom Name'))
                            ->placeholder('A / B / C')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('homeroom_teacher_id')
                            ->label(__('Homeroom Teacher'))
                            ->relationship('homeroomTeacher', 'full_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('capacity')
                            ->label(__('Capacity'))
                            ->numeric()
                            ->default(30),
                        Forms\Components\TextInput::make('room_name')
                            ->label('Nama Ruangan')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Classroom Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->label(__('Grade Level'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('homeroomTeacher.full_name')
                    ->label(__('Homeroom Teacher'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label(__('Capacity'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label(__('Student Count'))
                    ->counts('students')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('room_name')
                    ->label(__('Room'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('grade_id')
                    ->label(__('Grade Level'))
                    ->relationship('grade', 'name'),
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
                    Tables\Actions\BulkAction::make('promote')
                        ->label('Naik Kelas')
                        ->icon('heroicon-o-arrow-up-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Naik Kelas')
                        ->modalDescription('Semua siswa di kelas yang dipilih akan dipromosikan. Lanjutkan?')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            // Promote logic placeholder
                        }),
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
        return __('Classrooms');
    }

    public static function getModelLabel(): string
    {
        return __('Classroom');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Classrooms');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassrooms::route('/'),
            'create' => Pages\CreateClassroom::route('/create'),
            'edit' => Pages\EditClassroom::route('/{record}/edit'),
        ];
    }
}
