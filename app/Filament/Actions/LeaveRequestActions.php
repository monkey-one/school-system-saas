<?php

namespace App\Filament\Actions;

use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

// Approve / reject / open-attachment actions shared by the school admin and
// the homeroom teacher leave request tables.
class LeaveRequestActions
{
    public static function approve(): Action
    {
        return Action::make('approve')
            ->label(__('Approve'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (LeaveRequest $record) => $record->status === 'pending')
            ->form([
                Forms\Components\Textarea::make('note')->label(__('Note (optional)'))->rows(2)->maxLength(500),
            ])
            ->action(function (LeaveRequest $record, array $data): void {
                $count = app(LeaveRequestService::class)->approve($record, auth()->user(), $data['note'] ?? null);

                Notification::make()
                    ->success()
                    ->title(__('Leave request approved'))
                    ->body(__('Attendance updated in :count session(s).', ['count' => $count]))
                    ->send();
            });
    }

    public static function reject(): Action
    {
        return Action::make('reject')
            ->label(__('Reject'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (LeaveRequest $record) => $record->status === 'pending')
            ->form([
                Forms\Components\Textarea::make('note')->label(__('Reason'))->required()->rows(2)->maxLength(500),
            ])
            ->action(function (LeaveRequest $record, array $data): void {
                app(LeaveRequestService::class)->reject($record, auth()->user(), $data['note']);

                Notification::make()->success()->title(__('Leave request rejected'))->send();
            });
    }

    public static function attachment(): Action
    {
        return Action::make('attachment')
            ->label(__('Attachment'))
            ->icon('heroicon-o-paper-clip')
            ->color('gray')
            ->visible(fn (LeaveRequest $record) => filled($record->attachment))
            ->url(fn (LeaveRequest $record) => route('leave-requests.attachment', $record))
            ->openUrlInNewTab();
    }
}
