<?php

namespace App\Filament\SchoolAdmin\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ParentsRelationManager extends RelationManager
{
    protected static string $relationship = 'parents';

    protected static ?string $title = 'Orang Tua / Wali';

    protected static ?string $modelLabel = 'Orang Tua';

    protected static ?string $pluralModelLabel = 'Orang Tua';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('relation')
                    ->label('Hubungan')
                    ->options([
                        'ayah' => 'Ayah',
                        'ibu' => 'Ibu',
                        'wali' => 'Wali',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->maxLength(20),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Tanggal Lahir'),
                Forms\Components\TextInput::make('education')
                    ->label('Pendidikan')
                    ->maxLength(100),
                Forms\Components\TextInput::make('occupation')
                    ->label('Pekerjaan')
                    ->maxLength(100),
                Forms\Components\TextInput::make('income')
                    ->label('Penghasilan')
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->rows(2),
                Forms\Components\Toggle::make('is_emergency_contact')
                    ->label('Kontak Darurat'),
                Forms\Components\Toggle::make('is_whatsapp_active')
                    ->label('WhatsApp Aktif'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('relation')
                    ->label('Hubungan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->label('Pekerjaan'),
                Tables\Columns\IconColumn::make('is_emergency_contact')
                    ->label('Kontak Darurat')
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Orang Tua'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
