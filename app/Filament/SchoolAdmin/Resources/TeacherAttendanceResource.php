<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\AttendanceStatus;
use App\Filament\SchoolAdmin\Resources\TeacherAttendanceResource\Pages;
use App\Models\TeacherAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeacherAttendanceResource extends Resource
{
    protected static ?string $model = TeacherAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Attendance Data'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Teacher'))
                            ->relationship('teacher', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('check_in_time')
                            ->label(__('Check In'))
                            ->seconds(false),
                        Forms\Components\TimePicker::make('check_out_time')
                            ->label(__('Check Out'))
                            ->seconds(false),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(collect(AttendanceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                            ->required(),
                        Forms\Components\Select::make('method')
                            ->label(__('Method'))
                            ->options([
                                'manual' => __('Manual'),
                                'qr' => 'QR Code',
                                'fingerprint' => __('Fingerprint'),
                                'face' => __('Face Recognition'),
                            ]),
                        Forms\Components\TextInput::make('location_lat')
                            ->label(__('Latitude'))
                            ->numeric(),
                        Forms\Components\TextInput::make('location_lng')
                            ->label(__('Longitude'))
                            ->numeric(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->label(__('Check In'))
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->label(__('Check Out'))
                    ->time('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof AttendanceStatus ? $state->label() : $state)
                    ->color(fn ($state) => $state instanceof AttendanceStatus ? $state->color() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->label(__('Method'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'full_name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(collect(AttendanceStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
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
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Teacher Attendance');
    }

    public static function getModelLabel(): string
    {
        return __('Teacher Attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Teacher Attendance');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherAttendances::route('/'),
            'create' => Pages\CreateTeacherAttendance::route('/create'),
            'edit' => Pages\EditTeacherAttendance::route('/{record}/edit'),
        ];
    }
}
