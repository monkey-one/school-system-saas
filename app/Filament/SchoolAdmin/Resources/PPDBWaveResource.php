<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\PPDBWaveResource\Pages;
use App\Models\PPDBWave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PPDBWaveResource extends Resource
{
    protected static ?string $model = PPDBWave::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Kesiswaan';

    protected static ?string $navigationLabel = 'Gelombang PPDB';

    protected static ?string $modelLabel = 'Gelombang PPDB';

    protected static ?string $pluralModelLabel = 'Gelombang PPDB';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Gelombang PPDB')
                    ->description('Informasi gelombang penerimaan peserta didik baru')
                    ->icon('heroicon-o-megaphone')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Gelombang')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('opens_at')
                            ->label('Dibuka Pada')
                            ->required(),
                        Forms\Components\DateTimePicker::make('closes_at')
                            ->label('Ditutup Pada')
                            ->required()
                            ->after('opens_at'),
                        Forms\Components\TextInput::make('quota_per_class')
                            ->label('Kuota per Kelas')
                            ->numeric()
                            ->required(),
                        Forms\Components\KeyValue::make('requirements')
                            ->label('Persyaratan')
                            ->keyLabel('Dokumen')
                            ->valueLabel('Keterangan')
                            ->addActionLabel('Tambah Persyaratan'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('opens_at')
                    ->label('Dibuka')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('closes_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quota_per_class')
                    ->label('Kuota/Kelas')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
            'index' => Pages\ListPPDBWaves::route('/'),
            'create' => Pages\CreatePPDBWave::route('/create'),
            'edit' => Pages\EditPPDBWave::route('/{record}/edit'),
        ];
    }
}
