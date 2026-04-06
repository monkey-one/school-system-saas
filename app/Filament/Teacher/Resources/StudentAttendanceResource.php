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

    protected static ?string $navigationLabel = 'Kehadiran Siswa';

    protected static ?string $modelLabel = 'Kehadiran Siswa';

    protected static ?string $pluralModelLabel = 'Kehadiran Siswa';

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
                Forms\Components\Section::make('Data Kehadiran')
                    ->description('Edit status kehadiran siswa')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Kehadiran')
                            ->options(
                                collect(AttendanceStatus::cases())
                                    ->mapWithKeys(fn (AttendanceStatus $status) => [$status->value => $status->label()])
                            )
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
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
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('attendanceSession.date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (AttendanceStatus $state) => $state->color())
                    ->formatStateUsing(fn (AttendanceStatus $state) => $state->label())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label('Waktu Masuk')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Metode')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
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
                        ->label('Tandai Hadir')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['status' => AttendanceStatus::HADIR->value])))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('markAlfa')
                        ->label('Tandai Alfa')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAttendances::route('/'),
            'edit' => Pages\EditStudentAttendance::route('/{record}/edit'),
        ];
    }
}
