<?php

namespace App\Filament\Teacher\Resources\MyExamResource\RelationManagers;

use App\Models\ExamQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

// Multiple-choice questions (A–E) of an exam.
class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Questions');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('question')->label(__('Question'))->required()->rows(3)->maxLength(3000)->columnSpanFull(),
                Forms\Components\Grid::make(2)->schema(
                    collect(ExamQuestion::LETTERS)->map(fn (string $letter) => Forms\Components\TextInput::make("options.{$letter}")
                        ->label(__('Option :letter', ['letter' => $letter]))
                        ->required(in_array($letter, ['A', 'B'], true))
                        ->maxLength(500))->all()
                ),
                Forms\Components\Select::make('correct_option')
                    ->label(__('Correct answer'))
                    ->options(array_combine(ExamQuestion::LETTERS, ExamQuestion::LETTERS))
                    ->required(),
                Forms\Components\TextInput::make('points')->label(__('Points'))->numeric()->minValue(1)->maxValue(100)->default(1)->required(),
                Forms\Components\TextInput::make('sort_order')->label(__('Order'))->numeric()->default(fn () => $this->getOwnerRecord()->questions()->count() + 1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#'),
                Tables\Columns\TextColumn::make('question')->label(__('Question'))->limit(80)->wrap(),
                Tables\Columns\TextColumn::make('correct_option')->label(__('Answer'))->badge()->color('success'),
                Tables\Columns\TextColumn::make('points')->label(__('Points')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('Add question'))
                    ->mutateFormDataUsing(fn (array $data) => $data + ['tenant_id' => $this->getOwnerRecord()->tenant_id]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }
}
