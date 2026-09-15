<x-filament-panels::page>
    @php $report = $this->getReport(); @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <label class="text-sm">
            <span class="mb-1 block font-medium text-gray-700 dark:text-gray-300">{{ __('Month') }}</span>
            <input type="month" wire:model.live="month" class="rounded-lg border-gray-300 text-sm dark:border-white/10 dark:bg-white/5">
        </label>
        <x-filament::button wire:click="export" icon="heroicon-o-arrow-down-tray" color="gray">{{ __('Export CSV') }}</x-filament::button>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            [__('Billed this month'), $report['billed'], 'text-gray-950 dark:text-white'],
            [__('Collected this month'), $report['collected'], 'text-success-600'],
            [__('Total outstanding'), $report['outstanding'], 'text-danger-600'],
            [__('Total student savings'), $report['savings'], 'text-primary-600'],
        ] as [$label, $value, $class])
            <x-filament::section>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold {{ $class }}">{{ $this->money($value) }}</p>
            </x-filament::section>
        @endforeach
    </div>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        {{ __('Collection rate') }}: <strong>{{ $report['billed'] > 0 ? round($report['collected'] / $report['billed'] * 100) . '%' : '—' }}</strong>
        · {{ __(':count payments', ['count' => $report['transactions']]) }}
    </p>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-filament::section :heading="__('Collected by method')">
            <ul class="divide-y divide-gray-100 text-sm dark:divide-white/5">
                @forelse ($report['byMethod'] as $method => $amount)
                    <li class="flex justify-between py-2"><span>{{ $method }}</span><strong>{{ $this->money($amount) }}</strong></li>
                @empty
                    <li class="py-4 text-center text-gray-500">{{ __('No payments this month.') }}</li>
                @endforelse
            </ul>
        </x-filament::section>

        <x-filament::section :heading="__('Outstanding per class')" class="xl:col-span-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-gray-500"><tr><th class="py-2">{{ __('Class') }}</th><th>{{ __('Students') }}</th><th>{{ __('Bills') }}</th><th class="text-right">{{ __('Outstanding') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($report['byClassroom'] as $row)
                            <tr><td class="py-2 font-medium">{{ $row['class'] }}</td><td>{{ $row['students'] }}</td><td>{{ $row['bills'] }}</td><td class="text-right font-semibold">{{ $this->money($row['outstanding']) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-500">{{ __('No outstanding bills.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    <x-filament::section :heading="__('Top 10 arrears')">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase text-gray-500"><tr><th class="py-2">NIS</th><th>{{ __('Student') }}</th><th>{{ __('Class') }}</th><th>{{ __('Bills') }}</th><th class="text-right">{{ __('Outstanding') }}</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse ($report['arrears'] as $row)
                        <tr><td class="py-2">{{ $row['nis'] }}</td><td class="font-medium">{{ $row['name'] }}</td><td>{{ $row['class'] }}</td><td>{{ $row['bills'] }}</td><td class="text-right font-semibold text-danger-600">{{ $this->money($row['outstanding']) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="py-4 text-center text-gray-500">{{ __('No arrears.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
