<?php

namespace App\Filament\Teacher\Resources\MyExamResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

// Read-only results of an exam (automatically graded).
class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Results');
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('student'))
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label(__('Student'))->searchable(),
                Tables\Columns\TextColumn::make('started_at')->label(__('Started'))->dateTime('d M H:i'),
                Tables\Columns\TextColumn::make('submitted_at')->label(__('Submitted'))->dateTime('d M H:i')->placeholder(__('In progress')),
                Tables\Columns\TextColumn::make('correct_count')->label(__('Correct'))->placeholder('—'),
                Tables\Columns\TextColumn::make('score')->label(__('Score'))->placeholder('—')->badge()
                    ->color(fn ($state) => $state === null ? 'gray' : ((float) $state >= 75 ? 'success' : 'warning'))
                    ->sortable(),
            ])
            ->defaultSort('score', 'desc');
    }
}
