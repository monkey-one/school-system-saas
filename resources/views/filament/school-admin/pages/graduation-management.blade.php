<x-filament-panels::page>
    <form wire:submit="graduate">
        {{ $this->form }}

        @if($students->isNotEmpty())
        <div class="mt-6">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header flex items-center gap-3 px-6 py-4">
                    <div class="flex-1">
                        <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            {{ __('Active Students') }} ({{ $students->count() }})
                        </h3>
                        <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Select students to graduate. All selected students will be marked as alumni.') }}
                        </p>
                    </div>
                </div>

                <div class="fi-section-content border-t border-gray-200 dark:border-white/10">
                    <div class="px-6 py-4">
                        {{-- Select/Deselect All --}}
                        <div class="mb-4 flex items-center gap-3">
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                                <input type="checkbox"
                                    class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700"
                                    {{ count($selected_students) === $students->count() ? 'checked' : '' }}
                                    wire:click="$set('selected_students', {{ count($selected_students) === $students->count() ? '[]' : $students->pluck('id')->toJson() }})">
                                {{ __('Select All') }}
                            </label>
                            <span class="text-sm text-primary-600 font-medium">
                                {{ count($selected_students) }} {{ __('selected') }}
                            </span>
                        </div>

                        {{-- Student list --}}
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            @foreach($students as $student)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition">
                                <input type="checkbox" value="{{ $student->id }}" wire:model="selected_students"
                                    class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-600 dark:border-gray-600 dark:bg-gray-700">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('NIS') }}: {{ $student->nis ?? '-' }}
                                        @if($student->nisn) &middot; {{ __('NISN') }}: {{ $student->nisn }} @endif
                                    </p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <x-filament::button type="submit" color="success" icon="heroicon-o-academic-cap" size="lg">
                {{ __('Graduate Selected Students') }} ({{ count($selected_students) }})
            </x-filament::button>
        </div>
        @elseif($classroom_id)
        <div class="mt-6 text-center py-12 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('No active students') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('All students in this classroom have already graduated or are inactive.') }}</p>
        </div>
        @endif
    </form>
</x-filament-panels::page>
