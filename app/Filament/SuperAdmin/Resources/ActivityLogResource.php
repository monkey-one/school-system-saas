<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\ActivityLogResource\Pages;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label(__('Log'))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label(__('Event'))
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('Subject Type'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('Performed By'))
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label(__('Log'))
                    ->options(fn () => Activity::distinct()->pluck('log_name', 'log_name')->toArray()),
                Tables\Filters\SelectFilter::make('event')
                    ->label(__('Event'))
                    ->options([
                        'created' => __('Created'),
                        'updated' => __('Updated'),
                        'deleted' => __('Deleted'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Activity Details'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('log_name')
                            ->label(__('Log')),
                        Infolists\Components\TextEntry::make('event')
                            ->label(__('Event'))
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('subject_type')
                            ->label(__('Subject Type'))
                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—'),
                        Infolists\Components\TextEntry::make('subject_id')
                            ->label(__('Subject ID')),
                        Infolists\Components\TextEntry::make('causer.name')
                            ->label(__('Performed By'))
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('Date'))
                            ->dateTime('d M Y H:i:s'),
                        Infolists\Components\KeyValueEntry::make('properties.old')
                            ->label(__('Old Values'))
                            ->columnSpanFull(),
                        Infolists\Components\KeyValueEntry::make('properties.attributes')
                            ->label(__('New Values'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('Activity Log');
    }

    public static function getModelLabel(): string
    {
        return __('Activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Activity Log');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
