<?php

namespace App\Filament\Actions;

use App\Models\Message;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

// Opens a conversation thread in a modal, marks it as read and lets the
// signed-in participant reply. Used by the teacher inbox and the school
// admin message center.
class MessageConversationAction
{
    public static function make(): Action
    {
        return Action::make('conversation')
            ->label(__('Open'))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('primary')
            ->modalHeading(fn (Message $record): string => $record->subject ?: __('Conversation'))
            ->modalWidth('2xl')
            ->modalContent(function (Message $record) {
                if (self::participates($record)) {
                    Message::where('thread_id', $record->thread_id)
                        ->where('recipient_id', auth()->id())
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);
                }

                return view('filament.message-thread', [
                    'messages' => Message::where('thread_id', $record->thread_id)->with('sender', 'student')->oldest()->get(),
                ]);
            })
            ->form(fn (Message $record): array => self::participates($record) ? [
                Forms\Components\Textarea::make('content')
                    ->label(__('Reply'))
                    ->rows(3)
                    ->maxLength(5000)
                    ->required(),
            ] : [])
            ->modalSubmitAction(fn (Message $record, $action) => self::participates($record) ? $action : false)
            ->modalSubmitActionLabel(__('Send Reply'))
            ->action(function (Message $record, array $data): void {
                if (! self::participates($record) || blank($data['content'] ?? null)) {
                    return;
                }

                $userId = auth()->id();

                Message::create([
                    'thread_id' => $record->thread_id,
                    'sender_id' => $userId,
                    'recipient_id' => $record->sender_id === $userId ? $record->recipient_id : $record->sender_id,
                    'student_id' => $record->student_id,
                    'subject' => $record->subject,
                    'content' => $data['content'],
                ]);

                Notification::make()->success()->title(__('Message sent.'))->send();
            });
    }

    public static function participates(Message $message): bool
    {
        return in_array(auth()->id(), [$message->sender_id, $message->recipient_id], true);
    }
}
