<?php

namespace App\Filament\Teacher\Resources;

use App\Enums\AttendanceStatus;
use App\Filament\Teacher\Resources\StudentAttendanceResource\Pages;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

// Allows teachers to view and manage student attendance records
class StudentAttendanceResource extends Resource
{
    protected static ?string $model = StudentAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return parent::getEloquentQuery()
            ->whereHas('attendanceSession', fn (Builder $query) => $query->where('teacher_id', $teacher?->id));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Attendance Data'))
                    ->description(__('Edit student attendance status'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('Attendance Status'))
                            ->options(
                                collect(AttendanceStatus::cases())
                                    ->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => $status->label()])
                            )
                            ->required(),
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
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label(__('NIS'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('attendanceSession.date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (AttendanceStatus $state) => $state->color())
                    ->formatStateUsing(fn (AttendanceStatus $state) => $state->label())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label(__('Check-in Time'))
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('Method'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(
                        collect(AttendanceStatus::cases())
                            ->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => $status->label()])
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markHadir')
                        ->label(__('Mark Present'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => AttendanceStatus::HADIR->value])))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('markAlfa')
                        ->label(__('Mark Absent'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => AttendanceStatus::ALFA->value])))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->striped()
            ->defaultSort('attendanceSession.date', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Attendance');
    }

    public static function getModelLabel(): string
    {
        return __('Student Attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Attendance');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAttendances::route('/'),
            'edit' => Pages\EditStudentAttendance::route('/{record}/edit'),
        ];
    }
}
