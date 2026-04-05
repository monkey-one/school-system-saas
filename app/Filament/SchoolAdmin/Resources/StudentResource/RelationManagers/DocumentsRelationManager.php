<?php

namespace App\Filament\SchoolAdmin\Resources\StudentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen';

    protected static ?string $modelLabel = 'Dokumen';

    protected static ?string $pluralModelLabel = 'Dokumen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Tipe Dokumen')
                    ->options([
                        'akta_lahir' => 'Akta Lahir',
                        'kartu_keluarga' => 'Kartu Keluarga',
                        'ijazah' => 'Ijazah',
                        'rapor' => 'Rapor',
                        'foto' => 'Pas Foto',
                        'skhun' => 'SKHUN',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->label('File')
                    ->directory('students/documents')
                    ->required()
                    ->maxSize(5120),
                Forms\Components\TextInput::make('file_name')
                    ->label('Nama File')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'akta_lahir' => 'Akta Lahir',
                        'kartu_keluarga' => 'Kartu Keluarga',
                        'ijazah' => 'Ijazah',
                        'rapor' => 'Rapor',
                        'foto' => 'Pas Foto',
                        'skhun' => 'SKHUN',
                        default => 'Lainnya',
                    })
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nama File')
                    ->searchable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum diverifikasi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Dokumen'),
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
