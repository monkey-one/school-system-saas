<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\AttendanceSessionResource\Pages;
use App\Models\AttendanceSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AttendanceSessionResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Absensi';

    protected static ?string $navigationLabel = 'Sesi Absensi';

    protected static ?string $modelLabel = 'Sesi Absensi';

    protected static ?string $pluralModelLabel = 'Sesi Absensi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Sesi Absensi')
                    ->description('Informasi sesi absensi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label('Kelas - Mapel')
                            ->relationship('classroomSubject', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->classroom->name} - {$record->subject->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('teacher_id')
                            ->label('Guru')
                            ->relationship('teacher', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label('Jam Mulai'),
                        Forms\Components\TimePicker::make('end_time')
                            ->label('Jam Selesai'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Dibuka',
                                'closed' => 'Ditutup',
                            ])
                            ->default('open'),
                        Forms\Components\TextInput::make('topic')
                            ->label('Topik/Materi')
                            ->maxLength(255)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->searchable()
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
                Tables\Columns\TextColumn::make('teacher.full_name')
                    ->label('Guru')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                    ->label('Status')
                    ->options([
                        'open' => 'Dibuka',
                        'closed' => 'Ditutup',
                    ]),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Guru')
                    ->relationship('teacher', 'full_name'),
            ])
            ->actions([
                Tables\Actions\Action::make('generateQr')
                    ->label('Generate QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (AttendanceSession $record) {
                        $record->update([
                            'qr_token' => Str::random(32),
                            'qr_generated_at' => now(),
                            'qr_expires_at' => now()->addMinutes(30),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate QR Code')
                    ->modalDescription('QR Code baru akan dibuat dan berlaku selama 30 menit.'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceSessions::route('/'),
            'create' => Pages\CreateAttendanceSession::route('/create'),
            'edit' => Pages\EditAttendanceSession::route('/{record}/edit'),
        ];
    }
}
