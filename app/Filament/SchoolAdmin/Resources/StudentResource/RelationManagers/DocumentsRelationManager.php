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

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Documents');
    }

    public static function getModelLabel(): string
    {
        return __('Document');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Documents');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(__('Document Type'))
                    ->options([
                        'akta_lahir' => __('Birth Certificate'),
                        'kartu_keluarga' => __('Family Card'),
                        'ijazah' => __('Diploma'),
                        'rapor' => __('Report Card'),
                        'foto' => __('Passport Photo'),
                        'skhun' => __('SKHUN'),
                        'lainnya' => __('Other'),
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->label(__('File'))
                    ->directory('students/documents')
                    ->required()
                    ->maxSize(5120),
                Forms\Components\TextInput::make('file_name')
                    ->label(__('File Name'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label(__('Notes'))
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'akta_lahir' => __('Birth Certificate'),
                        'kartu_keluarga' => __('Family Card'),
                        'ijazah' => __('Diploma'),
                        'rapor' => __('Report Card'),
                        'foto' => __('Passport Photo'),
                        'skhun' => __('SKHUN'),
                        default => __('Other'),
                    })
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label(__('File Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label(__('Verified'))
                    ->dateTime('d M Y H:i')
                    ->placeholder(__('Not verified')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Uploaded'))
                    ->dateTime('d M Y'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add Document')),
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
