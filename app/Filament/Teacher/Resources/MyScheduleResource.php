<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyScheduleResource\Pages;
use App\Models\Teacher;
use App\Models\TeachingSchedule;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyScheduleResource extends Resource
{
    protected static ?string $model = TeachingSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Saya';

    protected static ?string $modelLabel = 'Jadwal Mengajar';

    protected static ?string $pluralModelLabel = 'Jadwal Mengajar';

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
                    ->label('Hari')
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                        default => '-',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.classroom.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroomSubject.subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('room')
                    ->label('Ruangan')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMySchedules::route('/'),
        ];
    }
}
