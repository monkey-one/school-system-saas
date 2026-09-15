<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyAssignmentResource\Pages;
use App\Filament\Teacher\Resources\MyAssignmentResource\RelationManagers\SubmissionsRelationManager;
use App\Models\Assignment;
use App\Models\ClassroomSubject;
use App\Models\Teacher;
use App\Services\GradebookSync;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Teacher assignments (e-learning): create tasks for own classes, review and
// grade submissions, then send the scores to the gradebook.
class MyAssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 5;

    public static function teacherId(): int
    {
        return (int) (Teacher::where('user_id', auth()->id())->value('id') ?? 0);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('teacher_id', self::teacherId())
            ->with('classroomSubject.classroom', 'classroomSubject.subject')
            ->withCount(['submissions', 'submissions as graded_count' => fn (Builder $q) => $q->whereNotNull('score')]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Assignment'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Hidden::make('teacher_id')->default(fn () => self::teacherId()),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
                            ->options(fn () => ClassroomSubject::where('teacher_id', self::teacherId())->with('classroom', 'subject')->get()
                                ->mapWithKeys(fn (ClassroomSubject $cs) => [$cs->id => "{$cs->classroom?->name} - {$cs->subject?->name}"]))
                            ->searchable()
                            ->required(),
                        Forms\Components\DateTimePicker::make('due_at')->label(__('Due'))->seconds(false)->default(now()->addWeek()->setTime(23, 59))->required(),
                        Forms\Components\TextInput::make('title')->label(__('Title'))->required()->maxLength(200)->columnSpanFull(),
                        Forms\Components\RichEditor::make('instructions')
                            ->label(__('Instructions'))
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'h3', 'undo', 'redo'])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachment')
                            ->label(__('Material / worksheet'))
                            ->disk('local')
                            ->directory('assignments')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])
                            ->maxSize(10240),
                        Forms\Components\TextInput::make('max_score')->label(__('Maximum score'))->numeric()->minValue(1)->maxValue(1000)->default(100)->required(),
                        Forms\Components\Toggle::make('allow_late')->label(__('Accept late submissions'))->default(true),
                        Forms\Components\Toggle::make('is_published')->label(__('Published to students'))->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable()->limit(50)
                    ->description(fn (Assignment $record) => "{$record->classroomSubject?->classroom?->name} · {$record->classroomSubject?->subject?->name}"),
                Tables\Columns\TextColumn::make('due_at')->label(__('Due'))->dateTime('d M Y H:i')->sortable()
                    ->color(fn (Assignment $record) => $record->due_at->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('submissions_count')->label(__('Submitted'))->badge(),
                Tables\Columns\TextColumn::make('graded_count')->label(__('Graded'))->badge()->color('success'),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean(),
                Tables\Columns\IconColumn::make('assessment_id')->label(__('In gradebook'))->boolean()->getStateUsing(fn (Assignment $record) => $record->assessment_id !== null),
            ])
            ->actions([
                Tables\Actions\Action::make('syncGrades')
                    ->label(__('Send to gradebook'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(__('Graded submissions are copied into the gradebook as a "Tugas" assessment. Running it again updates the scores.'))
                    ->action(function (Assignment $record) {
                        $count = app(GradebookSync::class)->syncAssignment($record, auth()->id());
                        Notification::make()->success()->title(__(':count scores sent to the gradebook.', ['count' => $count]))->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('due_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [SubmissionsRelationManager::class];
    }

    public static function getNavigationLabel(): string
    {
        return __('Assignments');
    }

    public static function getModelLabel(): string
    {
        return __('Assignment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assignments');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAssignments::route('/'),
            'create' => Pages\CreateMyAssignment::route('/create'),
            'edit' => Pages\EditMyAssignment::route('/{record}/edit'),
        ];
    }
}
