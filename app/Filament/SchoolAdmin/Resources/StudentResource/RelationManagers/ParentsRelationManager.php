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

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Parent / Guardian');
    }

    public static function getModelLabel(): string
    {
        return __('Parent');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Parent');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('relation')
                    ->label(__('Relation'))
                    ->options([
                        'ayah' => __('Father'),
                        'ibu' => __('Mother'),
                        'wali' => __('Guardian'),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label(__('NIK'))
                    ->maxLength(20),
                Forms\Components\DatePicker::make('birth_date')
                    ->label(__('Date of Birth')),
                Forms\Components\TextInput::make('education')
                    ->label(__('Education'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('occupation')
                    ->label('Pekerjaan')
                    ->maxLength(100),
                Forms\Components\TextInput::make('income')
                    ->label(__('Income'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->label(__('Phone'))
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->label(__('Email'))
                    ->email()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label(__('Address'))
                    ->rows(2),
                Forms\Components\Toggle::make('is_emergency_contact')
                    ->label(__('Emergency Contact')),
                Forms\Components\Toggle::make('is_whatsapp_active')
                    ->label(__('WhatsApp Active')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('relation')
                    ->label(__('Relation'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->label(__('Occupation')),
                Tables\Columns\IconColumn::make('is_emergency_contact')
                    ->label(__('Emergency Contact'))
                    ->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add Parent')),
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
