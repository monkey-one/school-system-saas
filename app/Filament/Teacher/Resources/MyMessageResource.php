<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Actions\MessageConversationAction;
use App\Filament\Teacher\Resources\MyMessageResource\Pages;
use App\Models\Message;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Teacher inbox: one row per conversation with students or parents (latest
// message first). Opening a row shows the thread and allows a reply.
class MyMessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('id', Message::query()->involving(auth()->id())->selectRaw('MAX(id)')->groupBy('thread_id'))
            ->with('sender', 'recipient', 'student');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = Message::where('recipient_id', auth()->id())->whereNull('read_at')->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contact')
                    ->label(__('Contact'))
                    ->getStateUsing(fn (Message $record) => $record->sender_id === auth()->id() ? $record->recipient?->name : $record->sender?->name)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('Last message'))
                    ->limit(60)
                    ->searchable(),
                Tables\Columns\TextColumn::make('unread')
                    ->label(__('Unread'))
                    ->badge()
                    ->color('warning')
                    ->getStateUsing(fn (Message $record) => Message::where('thread_id', $record->thread_id)
                        ->where('recipient_id', auth()->id())->whereNull('read_at')->count() ?: null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                MessageConversationAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('No conversations yet.'));
    }

    public static function getNavigationLabel(): string
    {
        return __('Messages');
    }

    public static function getModelLabel(): string
    {
        return __('Message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Messages');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyMessages::route('/'),
        ];
    }
}
