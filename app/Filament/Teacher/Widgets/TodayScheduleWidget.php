<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\TeachingSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TodayScheduleWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getTableHeading(): string
    {
        return __('Today\'s Schedule') . ' — ' . Carbon::now()->translatedFormat('l, d F Y');
    }

    public function table(Table $table): Table
    {
        $teacher = auth()->user()->teacher ?? null;

        return $table
            ->query(
                TeachingSchedule::query()
                    ->with(['classroomSubject.subject', 'classroomSubject.classroom'])
                    ->when($teacher, fn ($q) => $q->where('teacher_id', $teacher->id))
                    ->when(!$teacher, fn ($q) => $q->whereRaw('1 = 0'))
                    ->where('day_of_week', Carbon::now()->dayOfWeekIso)
                    ->where('is_active', true)
                    ->orderBy('start_time')
            )
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('Time'))
                    ->formatStateUsing(fn ($record) => substr($record->start_time, 0, 5) . ' - ' . substr($record->end_time, 0, 5)),
                Tables\Columns\TextColumn::make('classroomSubject.subject.name')
                    ->label(__('Subject')),
                Tables\Columns\TextColumn::make('classroomSubject.classroom.name')
                    ->label(__('Classroom')),
                Tables\Columns\TextColumn::make('room')
                    ->label(__('Room'))
                    ->badge()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('No classes today'))
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
