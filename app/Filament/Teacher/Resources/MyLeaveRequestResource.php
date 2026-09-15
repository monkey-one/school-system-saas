<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Actions\LeaveRequestActions;
use App\Filament\Teacher\Resources\MyLeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use App\Models\Teacher;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Homeroom teachers review the leave requests of their own class.
class MyLeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $teacherId = Teacher::where('user_id', auth()->id())->value('id') ?? 0;

        return parent::getEloquentQuery()
            ->whereHas('student.classroom', fn (Builder $q) => $q->where('homeroom_teacher_id', $teacherId))
            ->with('student.classroom', 'requester');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getEloquentQuery()->where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label(__('Student'))->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => LeaveRequest::typeLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Dates'))
                    ->formatStateUsing(fn (LeaveRequest $record) => $record->start_date->translatedFormat('d M') . ' – ' . $record->end_date->translatedFormat('d M Y')),
                Tables\Columns\TextColumn::make('reason')->label(__('Reason'))->limit(60)->wrap(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state) => LeaveRequest::statusColor($state))
                    ->formatStateUsing(fn (string $state) => LeaveRequest::statusLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('requester.name')->label(__('Requested by')),
                Tables\Columns\TextColumn::make('created_at')->label(__('Submitted'))->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('Status'))->options(LeaveRequest::statusLabels())->default('pending'),
            ])
            ->actions([
                LeaveRequestActions::approve(),
                LeaveRequestActions::reject(),
                LeaveRequestActions::attachment(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationLabel(): string
    {
        return __('Leave Requests');
    }

    public static function getModelLabel(): string
    {
        return __('Leave Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Leave Requests');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyLeaveRequests::route('/'),
        ];
    }
}
