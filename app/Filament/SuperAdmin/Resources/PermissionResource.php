<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Permission Data'))
                    ->icon('heroicon-o-key')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Permission Name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('guard_name')
                            ->label(__('Guard'))
                            ->options([
                                'web' => 'Web',
                            ])
                            ->default('web')
                            ->required(),
                        Forms\Components\Select::make('roles')
                            ->label(__('Roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Permission Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label(__('Guard'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles_count')
                    ->label(__('Role Count'))
                    ->counts('roles')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->label(__('Guard'))
                    ->options([
                        'web' => 'Web',
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
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('User Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Permissions');
    }

    public static function getModelLabel(): string
    {
        return __('Permission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Permissions');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
