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

// Allows teachers to manage their own attendance sessions
class MyAttendanceSessionResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

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
                Forms\Components\Section::make(__('Attendance Session Data'))
                    ->description(__('Attendance session information'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Hidden::make('teacher_id')
                            ->default($teacher?->id),
                        Forms\Components\Select::make('classroom_subject_id')
                            ->label(__('Classroom - Subject'))
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
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label(__('Start Time')),
                        Forms\Components\TimePicker::make('end_time')
                            ->label(__('End Time')),
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
                Tables\Columns\TextColumn::make('topic')
                    ->label(__('Topic'))
                    ->searchable()
                    ->limit(30)
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
                        'open' => __('Open'),
                        'closed' => __('Closed'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('student_attendances_count')
                    ->label(__('Attendance'))
                    ->counts('studentAttendances')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'open' => __('Open'),
                        'closed' => __('Closed'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('generateQr')
                    ->label(__('Generate QR'))
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
                    ->modalHeading(__('Generate QR Code'))
                    ->modalDescription(__('A new QR Code will be generated and valid for 30 minutes.'))
                    ->after(function (AttendanceSession $record, Tables\Actions\Action $action) {
                        $action->successNotificationTitle(__('QR Code generated successfully'))
                            ->sendSuccessNotification();
                    }),
                Tables\Actions\Action::make('closeSession')
                    ->label(__('Close Session'))
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->action(fn (AttendanceSession $record) => $record->update(['status' => 'closed']))
                    ->requiresConfirmation()
                    ->modalHeading(__('Close Attendance Session'))
                    ->modalDescription(__('The attendance session will be closed and students can no longer submit attendance.'))
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
            'index' => Pages\ListMyAttendanceSessions::route('/'),
            'create' => Pages\CreateMyAttendanceSession::route('/create'),
            'edit' => Pages\EditMyAttendanceSession::route('/{record}/edit'),
        ];
    }
}
