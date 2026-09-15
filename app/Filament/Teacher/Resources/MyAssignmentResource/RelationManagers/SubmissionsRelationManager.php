<?php

namespace App\Filament\Teacher\Resources\MyAssignmentResource\RelationManagers;

use App\Models\AssignmentSubmission;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

// Submissions of an assignment with grading (score + feedback).
class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Submissions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('student'))
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label(__('Student'))->searchable(),
                Tables\Columns\TextColumn::make('submitted_at')->label(__('Submitted'))->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\IconColumn::make('is_late')->label(__('Late'))->boolean()->trueColor('danger')->falseColor('success'),
                Tables\Columns\TextColumn::make('score')->label(__('Score'))->placeholder('—')->badge()->color('success'),
                Tables\Columns\TextColumn::make('feedback')->label(__('Feedback'))->limit(40)->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('graded')
                    ->label(__('Graded'))
                    ->queries(true: fn ($q) => $q->whereNotNull('score'), false: fn ($q) => $q->whereNull('score')),
            ])
            ->actions([
                Tables\Actions\Action::make('grade')
                    ->label(__('Review & grade'))
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading(fn (AssignmentSubmission $record) => $record->student?->full_name)
                    ->modalContent(fn (AssignmentSubmission $record) => new HtmlString(
                        '<div class="space-y-3 text-sm">'
                        . '<p class="whitespace-pre-line rounded-lg bg-gray-50 p-3 dark:bg-white/5">' . e($record->content ?: __('(no text answer)')) . '</p>'
                        . ($record->attachment ? '<a class="font-semibold text-primary-600 hover:underline" target="_blank" href="' . e(route('elearning.submissions.attachment', $record)) . '">' . e(__('Open attached file')) . '</a>' : '')
                        . '</div>'
                    ))
                    ->fillForm(fn (AssignmentSubmission $record) => ['score' => $record->score, 'feedback' => $record->feedback])
                    ->form(fn () => [
                        Forms\Components\TextInput::make('score')
                            ->label(__('Score'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue($this->getOwnerRecord()->max_score)
                            ->suffix('/ ' . $this->getOwnerRecord()->max_score)
                            ->required(),
                        Forms\Components\Textarea::make('feedback')->label(__('Feedback'))->rows(3)->maxLength(2000),
                    ])
                    ->action(function (AssignmentSubmission $record, array $data) {
                        $record->update($data + ['graded_by' => auth()->id(), 'graded_at' => now()]);
                        Notification::make()->success()->title(__('Score saved'))->send();
                    }),
            ])
            ->defaultSort('submitted_at');
    }
}
