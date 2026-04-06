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

    protected static ?string $navigationLabel = 'Input Nilai';

    protected static ?string $modelLabel = 'Nilai Siswa';

    protected static ?string $pluralModelLabel = 'Nilai Siswa';

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
                Forms\Components\Section::make('Input Nilai')
                    ->description('Masukkan nilai siswa')
                    ->icon('heroicon-o-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('assessment_id')
                            ->label('Penilaian')
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
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('score')
                            ->label('Nilai')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Toggle::make('is_remedial')
                            ->label('Remedial')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\TextInput::make('remedial_score')
                            ->label('Nilai Remedial')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (Forms\Get $get) => $get('is_remedial')),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
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
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assessment.name')
                    ->label('Penilaian')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Nilai')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_remedial')
                    ->label('Remedial')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('remedial_score')
                    ->label('Nilai Remedial')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentGrades::route('/'),
            'create' => Pages\CreateStudentGrade::route('/create'),
            'edit' => Pages\EditStudentGrade::route('/{record}/edit'),
        ];
    }
}
