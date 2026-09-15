<?php

namespace App\Support;

use Filament\Notifications\Notification;

// Helpers for the public demo installation (see config/demo.php).
final class Demo
{
    public static function enabled(): bool
    {
        return (bool) config('demo.enabled');
    }

    // Used from model event listeners: returning false cancels the save or
    // delete, and the notification tells the visitor why nothing happened.
    public static function deny(?string $message = null): bool
    {
        Notification::make()
            ->warning()
            ->title(__('Demo mode'))
            ->body($message ?? __('This action is disabled on the public demo.'))
            ->send();

        return false;
    }
}
