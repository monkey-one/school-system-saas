<x-filament-panels::page.simple>
    <div class="mb-6 text-center">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

        @if(str_contains(request()->path(), 'super-admin'))
            <p class="text-sm text-gray-500 dark:text-gray-400">Panel Super Administrator</p>
        @elseif(str_contains(request()->path(), 'teacher'))
            <p class="text-sm text-gray-500 dark:text-gray-400">Panel Guru</p>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Panel Administrasi Sekolah</p>
        @endif
    </div>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} EduSaaS — Sistem Manajemen Sekolah
        </p>
    </div>
</x-filament-panels::page.simple>
