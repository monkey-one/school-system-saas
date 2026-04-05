<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\SppTypeResource\Pages;
use App\Models\SppType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SppTypeResource extends Resource
{
    protected static ?string $model = SppType::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Jenis SPP';

    protected static ?string $modelLabel = 'Jenis SPP';

    protected static ?string $pluralModelLabel = 'Jenis SPP';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Jenis SPP')
                    ->description('Informasi jenis pembayaran SPP')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\Select::make('frequency')
                            ->label('Frekuensi')
                            ->options([
                                'monthly' => 'Bulanan',
                                'semester' => 'Per Semester',
                                'yearly' => 'Tahunan',
                                'once' => 'Sekali Bayar',
                            ])
                            ->required(),
                        Forms\Components\Select::make('applies_to')
                            ->label('Berlaku Untuk')
                            ->options([
                                'all' => 'Semua Siswa',
                                'new' => 'Siswa Baru',
                                'specific' => 'Tertentu',
                            ])
                            ->default('all'),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(1000),
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
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('frequency')
                    ->label('Frekuensi')
                    ->options([
                        'monthly' => 'Bulanan',
                        'semester' => 'Per Semester',
                        'yearly' => 'Tahunan',
                        'once' => 'Sekali Bayar',
                    ]),
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
            'index' => Pages\ListSppTypes::route('/'),
            'create' => Pages\CreateSppType::route('/create'),
            'edit' => Pages\EditSppType::route('/{record}/edit'),
        ];
    }
}
