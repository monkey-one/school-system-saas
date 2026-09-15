<x-filament-panels::page>
    @php
        $slug = App\Models\Tenant::current()?->slug;
        $links = [
            ['website', __('Website'), route('website.home', ['tenant' => $slug])],
            ['ppdb', __('PPDB Online'), route('ppdb.index', ['tenant' => $slug])],
            ['alumni', __('Alumni'), route('alumni.index', ['tenant' => $slug])],
            ['sitemap', 'Sitemap', route('website.sitemap', ['tenant' => $slug])],
        ];
    @endphp

    <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800" x-data="{ copied: null }">
        <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
            <x-heroicon-o-link class="h-4 w-4" />
            {{ __('Share Public URLs') }}
        </h3>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @foreach ($links as [$key, $label, $url])
                <div class="flex items-center gap-2">
                    <span class="w-24 shrink-0 text-xs font-medium text-gray-500">{{ $label }}</span>
                    <input type="text" readonly value="{{ $url }}" class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-600 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400" />
                    <button type="button" x-on:click="navigator.clipboard.writeText(@js($url)); copied = @js($key); setTimeout(() => copied = null, 2000)"
                            class="rounded-lg bg-primary-100 px-3 py-2 text-xs font-medium text-primary-700 hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300">
                        <span x-text="copied === @js($key) ? @js(__('Copied!')) : @js(__('Copy'))"></span>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button type="submit">
                {{ __('Save Changes') }}
            </x-filament::button>

            <a href="{{ route('website.home', ['tenant' => $slug]) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 transition hover:text-primary-500">
                <x-heroicon-o-arrow-top-right-on-square class="h-4 w-4" />
                {{ __('View Public Profile') }}
            </a>
        </div>
    </form>
</x-filament-panels::page>
