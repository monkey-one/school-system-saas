<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\SchoolType;
use App\Enums\TenantStatus;
use App\Filament\SuperAdmin\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

// Manages tenant school registrations and configuration
class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Manajemen Tenant';

    protected static ?string $navigationLabel = 'Sekolah';

    protected static ?string $modelLabel = 'Sekolah';

    protected static ?string $pluralModelLabel = 'Sekolah';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Sekolah')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Sekolah')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('tenants/logos')
                            ->maxSize(2048),
                        Forms\Components\Select::make('school_type')
                            ->label('Jenjang')
                            ->options(SchoolType::class)
                            ->required(),
                        Forms\Components\TextInput::make('npsn')
                            ->label('NPSN')
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('principal_name')
                            ->label('Nama Kepala Sekolah')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Kontak')
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota/Kabupaten')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->maxLength(100),
                    ]),

                Forms\Components\Section::make('Pengaturan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(TenantStatus::class)
                            ->required()
                            ->default(TenantStatus::TRIAL),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Masa Percobaan Berakhir'),
                        Forms\Components\KeyValue::make('settings')
                            ->label('Pengaturan Tambahan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('school_type')
                    ->label('Jenjang')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (TenantStatus $state): string => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('principal_name')
                    ->label('Kepala Sekolah')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Kota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(TenantStatus::class),
                Tables\Filters\SelectFilter::make('school_type')
                    ->label('Jenjang')
                    ->options(SchoolType::class),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Masuk Panel')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('info')
                    ->url(fn (Tenant $record): string => route('filament.school-admin.pages.dashboard', ['tenant' => $record->slug]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('suspend')
                    ->label('Tangguhkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->status !== TenantStatus::SUSPENDED)
                    ->action(fn (Tenant $record) => $record->update(['status' => TenantStatus::SUSPENDED])),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Tenant $record): bool => $record->status !== TenantStatus::ACTIVE)
                    ->action(fn (Tenant $record) => $record->update(['status' => TenantStatus::ACTIVE])),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
