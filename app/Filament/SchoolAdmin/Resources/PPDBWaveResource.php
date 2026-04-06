<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\PPDBWaveResource\Pages;
use App\Models\PPDBWave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages admission enrollment waves and periods
class PPDBWaveResource extends Resource
{
    protected static ?string $model = PPDBWave::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

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
                            ->label(__('Wave Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
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
                            ->label(__('Active')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label(__('Academic Year'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('opens_at')
                    ->label(__('Open'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('closes_at')
                    ->label(__('Closed'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quota_per_class')
                    ->label('Kuota/Kelas')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label(__('Academic Year'))
                    ->relationship('academicYear', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
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

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('PPDB Waves');
    }

    public static function getModelLabel(): string
    {
        return __('PPDB Wave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('PPDB Waves');
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
