<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\WhatsappLogResource\Pages;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Tenant;
use App\Models\WhatsappLog;
use App\Support\Demo;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Read-only delivery log of WhatsApp messages sent by the school, with a
// resend action for failed messages.
class WhatsappLogResource extends Resource
{
    protected static ?string $model = WhatsappLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 6;

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
                Tables\Columns\TextColumn::make('sent_at')->label(__('Sent At'))->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('to_number')->label(__('Number'))->searchable(),
                Tables\Columns\TextColumn::make('message')->label(__('Message'))->limit(60)->wrap()->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (?string $state) => $state === 'sent' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('reference_type')->label(__('Type'))->badge()->color('gray')->toggleable(),
                Tables\Columns\TextColumn::make('error_message')->label(__('Error'))->limit(40)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('Status'))->options(['sent' => __('Sent'), 'failed' => __('Failed')]),
            ])
            ->actions([
                Tables\Actions\Action::make('resend')
                    ->label(__('Resend'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (WhatsappLog $record) => $record->status === 'failed')
                    ->requiresConfirmation()
                    ->action(function (WhatsappLog $record) {
                        if (Demo::enabled()) {
                            Demo::deny();

                            return;
                        }

                        SendWhatsAppNotification::dispatch($record->to_number, $record->message, $record->reference_type, $record->reference_id, Tenant::current()?->id);
                        Notification::make()->success()->title(__('Message queued for sending.'))->send();
                    }),
            ])
            ->defaultSort('sent_at', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('WhatsApp Log');
    }

    public static function getModelLabel(): string
    {
        return __('WhatsApp message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('WhatsApp Log');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsappLogs::route('/'),
        ];
    }
}
