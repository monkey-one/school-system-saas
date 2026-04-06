<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Enums\UserType;
use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

// Manages platform user accounts across all tenants
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope('tenant');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Data'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label(__('User Type'))
                            ->options(UserType::class)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->disabled(fn (?User $record) => $record?->id === auth()->id()),
                    ]),

                Forms\Components\Section::make(__('Tenant'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('tenant_id')
                            ->label(__('School'))
                            ->relationship('tenant', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
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
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label(__('School'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->badge()
                    ->color('info')
                    ->separator(','),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('Active'))
                    ->sortable()
                    ->disabled(fn ($record) => $record?->id === auth()->id()),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label(__('Last Login'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder(__('Never')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('User Type'))
                    ->options(UserType::class),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label(__('School'))
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->id === auth()->id()),
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
        return __('All Users');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
