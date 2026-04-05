<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\Gender;
use App\Enums\PPDBStatus;
use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource\Pages;
use App\Models\PPDBRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PPDBRegistrationResource extends Resource
{
    protected static ?string $model = PPDBRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Kesiswaan';

    protected static ?string $navigationLabel = 'Pendaftaran PPDB';

    protected static ?string $modelLabel = 'Pendaftaran PPDB';

    protected static ?string $pluralModelLabel = 'Pendaftaran PPDB';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pendaftaran')
                    ->description('Informasi calon peserta didik')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('ppdb_wave_id')
                            ->label('Gelombang PPDB')
                            ->relationship('ppdbWave', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('registration_number')
                            ->label('Nomor Pendaftaran')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(Gender::class)
                            ->required(),
                        Forms\Components\TextInput::make('previous_school')
                            ->label('Sekolah Asal')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('parent_name')
                            ->label('Nama Orang Tua')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('parent_phone')
                            ->label('Telepon Orang Tua')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('parent_email')
                            ->label('Email Orang Tua')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status & Catatan')
                    ->description('Status pendaftaran')
                    ->icon('heroicon-o-document-check')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(PPDBStatus::class)
                            ->default(PPDBStatus::PENDING)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label('No. Pendaftaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('parent_name')
                    ->label('Nama Orang Tua')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (PPDBStatus $state) => $state->label())
                    ->color(fn (PPDBStatus $state) => $state->color())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Ditinjau')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Belum ditinjau'),
                Tables\Columns\TextColumn::make('ppdbWave.name')
                    ->label('Gelombang')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(PPDBStatus::class),
                Tables\Filters\SelectFilter::make('ppdb_wave_id')
                    ->label('Gelombang')
                    ->relationship('ppdbWave', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (PPDBRegistration $record) => $record->update([
                        'status' => PPDBStatus::ACCEPTED,
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ]))
                    ->visible(fn (PPDBRegistration $record) => $record->status === PPDBStatus::PENDING),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(fn (PPDBRegistration $record, array $data) => $record->update([
                        'status' => PPDBStatus::REJECTED,
                        'notes' => $data['notes'],
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ]))
                    ->visible(fn (PPDBRegistration $record) => $record->status === PPDBStatus::PENDING),
                Tables\Actions\Action::make('waitlist')
                    ->label('Cadangan')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan'),
                    ])
                    ->action(fn (PPDBRegistration $record, array $data) => $record->update([
                        'status' => PPDBStatus::WAITLIST,
                        'notes' => $data['notes'] ?? $record->notes,
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ]))
                    ->visible(fn (PPDBRegistration $record) => $record->status === PPDBStatus::PENDING),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_accept')
                        ->label('Terima Semua')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update([
                            'status' => PPDBStatus::ACCEPTED,
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]))),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Tolak Semua')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update([
                            'status' => PPDBStatus::REJECTED,
                            'reviewed_at' => now(),
                            'reviewed_by' => auth()->id(),
                        ]))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPPDBRegistrations::route('/'),
            'create' => Pages\CreatePPDBRegistration::route('/create'),
            'edit' => Pages\EditPPDBRegistration::route('/{record}/edit'),
        ];
    }
}
