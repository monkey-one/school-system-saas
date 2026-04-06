<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\AssessmentResource\Pages;
use App\Models\Assessment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages student assessments and evaluations
class AssessmentResource extends Resource
{
    protected static ?string $model = Assessment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Assessment Data'))
                    ->description(__('Assessment information'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Assessment Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
                            ->relationship('classroomSubject', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->classroom->name} - {$record->subject->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('assessment_type_id')
                            ->label(__('Assessment Type'))
                            ->relationship('assessmentType', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('semester_id')
                            ->label(__('Semester'))
                            ->relationship('semester', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->required(),
                        Forms\Components\TextInput::make('max_score')
                            ->label(__('Max Score'))
                            ->numeric()
                            ->default(100)
                            ->required(),
                        Forms\Components\TextInput::make('weight_override')
                            ->label('Override Bobot')
                            ->numeric()
                            ->step(0.01)
                            ->helperText('Kosongkan untuk menggunakan bobot default'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.classroom.name')
                    ->label(__('Classroom'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.subject.name')
                    ->label(__('Subject'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assessmentType.name')
                    ->label(__('Type'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->label(__('Max Score'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assessment_type_id')
                    ->label(__('Assessment Type'))
                    ->relationship('assessmentType', 'name'),
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label(__('Semester'))
                    ->relationship('semester', 'name'),
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
            ->defaultSort('date', 'desc')
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
        return __('Assessments');
    }

    public static function getModelLabel(): string
    {
        return __('Assessment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assessments');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssessments::route('/'),
            'create' => Pages\CreateAssessment::route('/create'),
            'edit' => Pages\EditAssessment::route('/{record}/edit'),
        ];
    }
}
