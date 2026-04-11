<x-filament-panels::page>
    {{-- Share URLs Section --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4 mb-6" x-data="{
        copied: null,
        copyUrl(type, url) {
            navigator.clipboard.writeText(url);
            this.copied = type;
            setTimeout(() => this.copied = null, 2000);
        }
    }">
        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <x-heroicon-o-link class="w-4 h-4" />
            {{ __('Share Public URLs') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ route('profile.index', ['tenant' => App\Models\Tenant::current()?->slug]) }}" class="flex-1 text-xs bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-600 dark:text-gray-400" />
                <button @click="copyUrl('profile', '{{ route('profile.index', ['tenant' => App\Models\Tenant::current()?->slug]) }}')" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium rounded-lg transition" :class="copied === 'profile' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300 hover:bg-primary-200'">
                    <template x-if="copied === 'profile'"><x-heroicon-o-check class="w-3.5 h-3.5" /></template>
                    <template x-if="copied !== 'profile'"><x-heroicon-o-clipboard-document class="w-3.5 h-3.5" /></template>
                    <span x-text="copied === 'profile' ? '{{ __('Copied!') }}' : '{{ __('Copy Profile URL') }}'"></span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ route('alumni.index', ['tenant' => App\Models\Tenant::current()?->slug]) }}" class="flex-1 text-xs bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-600 dark:text-gray-400" />
                <button @click="copyUrl('alumni', '{{ route('alumni.index', ['tenant' => App\Models\Tenant::current()?->slug]) }}')" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium rounded-lg transition" :class="copied === 'alumni' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300 hover:bg-primary-200'">
                    <template x-if="copied === 'alumni'"><x-heroicon-o-check class="w-3.5 h-3.5" /></template>
                    <template x-if="copied !== 'alumni'"><x-heroicon-o-clipboard-document class="w-3.5 h-3.5" /></template>
                    <span x-text="copied === 'alumni' ? '{{ __('Copied!') }}' : '{{ __('Copy Alumni URL') }}'"></span>
                </button>
            </div>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button type="submit">
                {{ __('Save Changes') }}
            </x-filament::button>

            <a href="{{ route('profile.index', ['tenant' => App\Models\Tenant::current()?->slug]) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-500 transition">
                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                {{ __('View Public Profile') }}
            </a>
        </div>
    </form>
</x-filament-panels::page>
