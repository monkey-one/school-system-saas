<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyAssessmentResource\Pages;
use App\Models\Assessment;
use App\Models\ClassroomSubject;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyAssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationLabel = 'Penilaian';

    protected static ?string $modelLabel = 'Penilaian';

    protected static ?string $pluralModelLabel = 'Penilaian';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return parent::getEloquentQuery()
            ->whereHas('classroomSubject', fn (Builder $query) => $query->where('teacher_id', $teacher?->id));
    }

    public static function form(Form $form): Form
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return $form
            ->schema([
                Forms\Components\Section::make('Data Penilaian')
                    ->description('Informasi penilaian')
                    ->icon('heroicon-o-pencil-square')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label('Kelas - Mapel')
                            ->options(function () use ($teacher) {
                                return ClassroomSubject::where('teacher_id', $teacher?->id)
                                    ->with(['classroom', 'subject'])
                                    ->get()
                                    ->mapWithKeys(fn ($cs) => [$cs->id => "{$cs->classroom->name} - {$cs->subject->name}"]);
                            })
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('assessment_type_id')
                            ->label('Jenis Penilaian')
                            ->relationship('assessmentType', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('semester_id')
                            ->label('Semester')
                            ->relationship('semester', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Penilaian')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('max_score')
                            ->label('Nilai Maksimal')
                            ->numeric()
                            ->default(100)
                            ->required(),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Penilaian')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assessmentType.name')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->label('Nilai Maks')
                    ->sortable()
                    ->toggleable(),
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
            ->defaultSort('date', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAssessments::route('/'),
            'create' => Pages\CreateMyAssessment::route('/create'),
            'edit' => Pages\EditMyAssessment::route('/{record}/edit'),
        ];
    }
}
