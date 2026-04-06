<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\TeachingScheduleResource\Pages;
use App\Models\TeachingSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages teaching schedule assignments
class TeachingScheduleResource extends Resource
{
    protected static ?string $model = TeachingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Jadwal Mengajar')
                    ->description('Informasi jadwal mengajar guru')
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
                            ->relationship('classroomSubject', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->classroom->name} - {$record->subject->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('day_of_week')
                            ->label(__('Day'))
                            ->options([
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                            ])
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->label(__('Start Time'))
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->label(__('End Time'))
                            ->required()
                            ->after('start_time'),
                        Forms\Components\TextInput::make('room')
                            ->label(__('Room'))
                            ->maxLength(100),
                        Forms\Components\Select::make('semester_id')
                            ->label(__('Semester'))
                            ->relationship('semester', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label(__('Teacher'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label(__('Day'))
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        default => '-',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('Start'))
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i')
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
            ->filters([
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'full_name'),
                Tables\Filters\SelectFilter::make('day_of_week')
                    ->label(__('Day'))
                    ->options([
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                    ]),
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label(__('Semester'))
                    ->relationship('semester', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('day_of_week', 'asc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Staff Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Teaching Schedules');
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
            'index' => Pages\ListTeachingSchedules::route('/'),
            'create' => Pages\CreateTeachingSchedule::route('/create'),
            'edit' => Pages\EditTeachingSchedule::route('/{record}/edit'),
        ];
    }
}
