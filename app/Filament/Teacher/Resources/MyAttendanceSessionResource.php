<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\MyAttendanceSessionResource\Pages;
use App\Models\AttendanceSession;
use App\Models\ClassroomSubject;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MyAttendanceSessionResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Sesi Absensi';

    protected static ?string $modelLabel = 'Sesi Absensi';

    protected static ?string $pluralModelLabel = 'Sesi Absensi';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return parent::getEloquentQuery()
            ->where('teacher_id', $teacher?->id);
    }

    public static function form(Form $form): Form
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();

        return $form
            ->schema([
                Forms\Components\Section::make('Data Sesi Absensi')
                    ->description('Informasi sesi absensi')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Hidden::make('teacher_id')
                            ->default($teacher?->id),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label('Kelas - Mapel')
                            ->options(function () use ($teacher) {
                                return ClassroomSubject::where('teacher_id', $teacher?->id)
                                    ->with(['classroom', 'subject'])
                                    ->get()
                                    ->mapWithKeys(fn ($cs) => [$cs->id => "{$cs->classroom->name} - {$cs->subject->name}"]);
                            })
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
                Tables\Columns\TextColumn::make('topic')
                    ->label('Topik')
                    ->searchable()
                    ->limit(30)
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
                    ->label('Kehadiran')
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
            ])
            ->actions([
                Tables\Actions\Action::make('generateQr')
                    ->label('Generate QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (AttendanceSession $record) {
                        $token = Str::random(32);
                        $record->update([
                            'qr_token' => $token,
                            'qr_generated_at' => now(),
                            'qr_expires_at' => now()->addMinutes(30),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate QR Code')
                    ->modalDescription('QR Code baru akan dibuat dan berlaku selama 30 menit.')
                    ->after(function (AttendanceSession $record, Tables\Actions\Action $action) {
                        $action->successNotificationTitle('QR Code berhasil dibuat')
                            ->sendSuccessNotification();
                    }),
                Tables\Actions\Action::make('closeSession')
                    ->label('Tutup Sesi')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->action(fn (AttendanceSession $record) => $record->update(['status' => 'closed']))
                    ->requiresConfirmation()
                    ->modalHeading('Tutup Sesi Absensi')
                    ->modalDescription('Sesi absensi akan ditutup dan siswa tidak bisa lagi melakukan absensi.')
                    ->visible(fn (AttendanceSession $record) => $record->status === 'open'),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMyAttendanceSessions::route('/'),
            'create' => Pages\CreateMyAttendanceSession::route('/create'),
            'edit' => Pages\EditMyAttendanceSession::route('/{record}/edit'),
        ];
    }
}
