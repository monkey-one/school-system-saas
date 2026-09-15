<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyExamResource\Pages;
use App\Filament\Teacher\Resources\MyExamResource\RelationManagers\AttemptsRelationManager;
use App\Filament\Teacher\Resources\MyExamResource\RelationManagers\QuestionsRelationManager;
use App\Models\ClassroomSubject;
use App\Models\Exam;
use App\Services\GradebookSync;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Online exams (CBT) created by the teacher: schedule, multiple-choice
// questions, automatic grading and gradebook export.
class MyExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('teacher_id', MyAssignmentResource::teacherId())
            ->with('classroomSubject.classroom', 'classroomSubject.subject')
            ->withCount(['questions', 'attempts as submitted_count' => fn (Builder $q) => $q->whereNotNull('submitted_at')]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Exam'))
                    ->columns(3)
                    ->schema([
                        Forms\Components\Hidden::make('teacher_id')->default(fn () => MyAssignmentResource::teacherId()),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
                            ->options(fn () => ClassroomSubject::where('teacher_id', MyAssignmentResource::teacherId())->with('classroom', 'subject')->get()
                                ->mapWithKeys(fn (ClassroomSubject $cs) => [$cs->id => "{$cs->classroom?->name} - {$cs->subject?->name}"]))
                            ->searchable()
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('duration_minutes')->label(__('Duration (minutes)'))->numeric()->minValue(5)->maxValue(300)->default(60)->required(),
                        Forms\Components\TextInput::make('title')->label(__('Title'))->required()->maxLength(200)->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('starts_at')->label(__('Opens'))->seconds(false)->default(now()->addDay()->setTime(8, 0))->required(),
                        Forms\Components\DateTimePicker::make('ends_at')->label(__('Closes'))->seconds(false)->default(now()->addDay()->setTime(12, 0))->after('starts_at')->required(),
                        Forms\Components\Toggle::make('is_published')->label(__('Published to students'))->inline(false),
                        Forms\Components\Textarea::make('instructions')->label(__('Instructions'))->rows(3)->maxLength(2000)->columnSpanFull(),
                        Forms\Components\Toggle::make('shuffle_questions')->label(__('Shuffle question order'))->default(true),
                        Forms\Components\Toggle::make('show_result')->label(__('Show score to students after submitting'))->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable()->limit(50)
                    ->description(fn (Exam $record) => "{$record->classroomSubject?->classroom?->name} · {$record->classroomSubject?->subject?->name}"),
                Tables\Columns\TextColumn::make('starts_at')->label(__('Opens'))->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('duration_minutes')->label(__('Duration'))->suffix(' ' . __('min')),
                Tables\Columns\TextColumn::make('questions_count')->label(__('Questions'))->badge(),
                Tables\Columns\TextColumn::make('submitted_count')->label(__('Submitted'))->badge()->color('success'),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('syncGrades')
                    ->label(__('Send to gradebook'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(__('Submitted attempts are copied into the gradebook as an "Ulangan Harian" assessment. Running it again updates the scores.'))
                    ->action(function (Exam $record) {
                        $count = app(GradebookSync::class)->syncExam($record, auth()->id());
                        Notification::make()->success()->title(__(':count scores sent to the gradebook.', ['count' => $count]))->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [QuestionsRelationManager::class, AttemptsRelationManager::class];
    }

    public static function getNavigationLabel(): string
    {
        return __('Online Exams');
    }

    public static function getModelLabel(): string
    {
        return __('Exam');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Online Exams');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyExams::route('/'),
            'create' => Pages\CreateMyExam::route('/create'),
            'edit' => Pages\EditMyExam::route('/{record}/edit'),
        ];
    }
}
