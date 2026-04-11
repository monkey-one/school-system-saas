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
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Super Administrator Panel') }}</p>
        @elseif(str_contains(request()->path(), 'teacher'))
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Teacher Panel') }}</p>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('School Administration Panel') }}</p>
        @endif
    </div>

    @if(config('app.default_tenant_slug') === 'demo')
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-800 dark:bg-blue-950">
            <p class="mb-2 text-xs font-semibold text-blue-700 dark:text-blue-300">🔑 Demo Credentials:</p>
            @if(str_contains(request()->path(), 'super-admin'))
                <div class="text-xs text-blue-600 dark:text-blue-400 space-y-0.5">
                    <p><span class="font-medium">Email:</span> superadmin@edusaas.id</p>
                    <p><span class="font-medium">Password:</span> password</p>
                </div>
            @elseif(str_contains(request()->path(), 'teacher'))
                <div class="text-xs text-blue-600 dark:text-blue-400 space-y-0.5">
                    <p><span class="font-medium">Email:</span> hadi.santoso@smpn1demo.id</p>
                    <p><span class="font-medium">Password:</span> password</p>
                </div>
            @else
                <div class="text-xs text-blue-600 dark:text-blue-400 space-y-0.5">
                    <p><span class="font-medium">Email:</span> admin@smpn1demo.id</p>
                    <p><span class="font-medium">Password:</span> password</p>
                </div>
            @endif
            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                @unless(str_contains(request()->path(), 'super-admin'))
                    <a href="{{ url('/super-admin/login') }}" class="text-blue-500 hover:text-blue-700 underline">Super Admin</a>
                @endunless
                @unless(str_contains(request()->path(), 'school') && !str_contains(request()->path(), 'super-admin') && !str_contains(request()->path(), 'teacher'))
                    <a href="{{ url('/school/login') }}" class="text-blue-500 hover:text-blue-700 underline">School Admin</a>
                @endunless
                @unless(str_contains(request()->path(), 'teacher'))
                    <a href="{{ url('/teacher/login') }}" class="text-blue-500 hover:text-blue-700 underline">Teacher</a>
                @endunless
            </div>
        </div>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} EduSaaS — {{ __('School Management System') }}
        </p>
    </div>
</x-filament-panels::page.simple>
