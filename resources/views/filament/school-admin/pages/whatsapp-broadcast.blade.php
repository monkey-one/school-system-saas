<x-filament-panels::page>
    <form wire:submit="send" class="space-y-6">
        {{ $this->form }}

        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ __(':count unique WhatsApp numbers will receive this message.', ['count' => $this->getRecipients()->count()]) }}
                </p>
                <x-filament::button type="submit" icon="heroicon-o-paper-airplane" wire:confirm="{{ __('Send this message to all recipients?') }}">
                    {{ __('Send broadcast') }}
                </x-filament::button>
            </div>
            @if (\App\Support\Demo::enabled())
                <p class="mt-3 text-xs text-warning-600">{{ __('WhatsApp broadcast is disabled on the public demo.') }}</p>
            @endif
        </x-filament::section>
    </form>
</x-filament-panels::page>
