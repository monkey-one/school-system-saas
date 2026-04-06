<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyScheduleResource\Pages;
use App\Models\Teacher;
use App\Models\TeachingSchedule;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Displays the teacher's personal teaching schedule
class MyScheduleResource extends Resource
{
    protected static ?string $model = TeachingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return parent::getEloquentQuery()
            ->where('teacher_id', $teacher?->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label(__('Day'))
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        1 => __('Monday'),
                        2 => __('Tuesday'),
                        3 => __('Wednesday'),
                        4 => __('Thursday'),
                        5 => __('Friday'),
                        6 => __('Saturday'),
                        7 => __('Sunday'),
                        default => '-',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('Start Time'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label(__('End Time'))
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
                Tables\Columns\TextColumn::make('room')
                    ->label(__('Room'))
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->striped()
            ->defaultSort('day_of_week', 'asc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationLabel(): string
    {
        return __('My Schedule');
    }

    public static function getModelLabel(): string
    {
        return __('Teaching Schedule');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Teaching Schedules');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMySchedules::route('/'),
        ];
    }
}
