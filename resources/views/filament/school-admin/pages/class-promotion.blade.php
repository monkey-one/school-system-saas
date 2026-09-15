<x-filament-panels::page>
    {{ $this->form }}

    @php $students = $this->getStudents(); @endphp

    @if ($students->isNotEmpty())
        <x-filament::section :heading="__('Students')" :description="__(':selected of :total selected. Unselected students stay in their class.', ['selected' => count($selected), 'total' => $students->count()])">
            <div class="mb-3 flex gap-3 text-sm">
                <button type="button" wire:click="selectAll" class="font-semibold text-primary-600 hover:underline">{{ __('Select all') }}</button>
                <button type="button" wire:click="$set('selected', [])" class="font-semibold text-gray-500 hover:underline">{{ __('Clear') }}</button>
            </div>
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($students as $student)
                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-white/10">
                        <input type="checkbox" value="{{ $student->id }}" wire:model.live="selected" class="rounded border-gray-300 text-primary-600">
                        <span>{{ $student->full_name }} <span class="text-gray-500">· {{ $student->nis }}</span></span>
                    </label>
                @endforeach
            </div>

            <div class="mt-6">
                <x-filament::button wire:click="promote" wire:confirm="{{ __('Move the selected students to the target class?') }}" icon="heroicon-o-arrow-trending-up">
                    {{ __('Promote selected students') }}
                </x-filament::button>
            </div>
        </x-filament::section>
    @elseif ($data['source_classroom_id'] ?? null)
        <x-filament::section>
            <p class="text-sm text-gray-500">{{ __('No active students in this class.') }}</p>
        </x-filament::section>
    @endif
</x-filament-panels::page>
