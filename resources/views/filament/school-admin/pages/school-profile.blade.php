<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button type="submit">
                {{ __('Save Changes') }}
            </x-filament::button>

            <a href="{{ route('profile.index') }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-500 transition">
                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                {{ __('View Public Profile') }}
            </a>
        </div>
    </form>
</x-filament-panels::page>
