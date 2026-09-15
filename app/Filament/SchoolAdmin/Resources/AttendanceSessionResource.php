<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\Actions\ShowAttendanceQrAction;
use App\Filament\Actions\TakeAttendanceAction;
use App\Filament\SchoolAdmin\Resources\AttendanceSessionResource\Pages;
use App\Models\AttendanceSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages student attendance sessions and records
class AttendanceSessionResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Attendance Session Data'))
                    ->description(__('Attendance session information'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
                            ->relationship('classroomSubject', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->classroom->name} - {$record->subject->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label(__('Start Time')),
                        Forms\Components\TimePicker::make('end_time')
                            ->label(__('End Time')),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'open' => 'Dibuka',
                                'closed' => 'Ditutup',
                            ])
                            ->default('open'),
                        Forms\Components\TextInput::make('topic')
                            ->label(__('Topic/Material'))
                            ->maxLength(255)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
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
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label(__('Teacher'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'open' => 'success',
                        'closed' => 'gray',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'open' => 'Dibuka',
                        'closed' => 'Ditutup',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student_attendances_count')
                    ->label('Hadir')
                    ->counts('studentAttendances')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'open' => 'Dibuka',
                        'closed' => 'Ditutup',
                    ]),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'full_name'),
            ])
            ->actions([
                ShowAttendanceQrAction::make(),
                TakeAttendanceAction::make(),
                Tables\Actions\Action::make('closeSession')
                    ->label(__('Close Session'))
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (AttendanceSession $record) => $record->status === 'open')
                    ->action(fn (AttendanceSession $record) => $record->update(['status' => 'closed'])),
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
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Attendance Sessions');
    }

    public static function getModelLabel(): string
    {
        return __('Attendance Session');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Attendance Sessions');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSessions::route('/'),
            'create' => Pages\CreateAttendanceSession::route('/create'),
            'edit' => Pages\EditAttendanceSession::route('/{record}/edit'),
        ];
    }
}
