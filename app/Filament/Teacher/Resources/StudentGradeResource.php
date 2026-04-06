<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\StudentGradeResource\Pages;
use App\Models\Assessment;
use App\Models\StudentGrade;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Allows teachers to input and manage student grades
class StudentGradeResource extends Resource
{
    protected static ?string $model = StudentGrade::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?int $navigationSort = 5;

    public static function getEloquentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return parent::getEloquentQuery()
            ->whereHas('assessment.classroomSubject', fn (Builder $query) => $query->where('teacher_id', $teacher?->id));
    }

    public static function form(Form $form): Form
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return $form
            ->schema([
                Forms\Components\Section::make(__('Grade Input'))
                    ->description(__('Enter student scores'))
                    ->icon('heroicon-o-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('assessment_id')
                            ->label(__('Assessment'))
                            ->options(function () use ($teacher) {
                                return Assessment::whereHas('classroomSubject', fn (Builder $query) => $query->where('teacher_id', $teacher?->id))
                                    ->with('classroomSubject.classroom', 'classroomSubject.subject')
                                    ->get()
                                    ->mapWithKeys(fn ($a) => [$a->id => "{$a->name} ({$a->classroomSubject->classroom->name} - {$a->classroomSubject->subject->name})"]);
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('score')
                            ->label(__('Score'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Toggle::make('is_remedial')
                            ->label(__('Remedial'))
                            ->default(false)
                            ->reactive(),
                        Forms\Components\TextInput::make('remedial_score')
                            ->label(__('Remedial Score'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (Forms\Get $get) => $get('is_remedial')),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label(__('NIS'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assessment.name')
                    ->label(__('Assessment'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('score')
                    ->label(__('Score'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_remedial')
                    ->label(__('Remedial'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remedial_score')
                    ->label(__('Remedial Score'))
                    ->sortable()
                    ->toggleable()
                    ->placeholder('-'),
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
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationLabel(): string
    {
        return __('Grade Input');
    }

    public static function getModelLabel(): string
    {
        return __('Student Grade');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Grades');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentGrades::route('/'),
            'create' => Pages\CreateStudentGrade::route('/create'),
            'edit' => Pages\EditStudentGrade::route('/{record}/edit'),
        ];
    }
}
