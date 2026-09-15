<x-filament-panels::page>
    @php
        $today = $this->getToday();
        $settings = $this->settings();
    @endphp

    <div x-data="{
            busy: false,
            locate(method) {
                this.busy = true;
                const done = (lat, lng) => $wire.call(method, lat, lng).finally(() => this.busy = false);
                if (! navigator.geolocation) { return done(null, null); }
                navigator.geolocation.getCurrentPosition(
                    (pos) => done(pos.coords.latitude, pos.coords.longitude),
                    () => done(null, null),
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }
        }" class="grid gap-6 lg:grid-cols-3">
        <x-filament::section class="lg:col-span-1">
            <div class="text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
                <p class="mt-2 text-4xl font-bold tracking-tight text-gray-950 dark:text-white" x-data="{ t: '' }" x-init="setInterval(() => t = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" x-text="t"></p>

                <div class="mt-6 space-y-2 text-sm">
                    <p>{{ __('Check-in') }}: <strong>{{ $today?->check_in_time?->format('H:i') ?? '—' }}</strong>
                        @if ($today)<x-filament::badge :color="$today->status->color()" class="ml-1 inline-flex">{{ $today->status->label() }}</x-filament::badge>@endif
                    </p>
                    <p>{{ __('Check-out') }}: <strong>{{ $today?->check_out_time?->format('H:i') ?? '—' }}</strong></p>
                </div>

                <div class="mt-6 flex flex-col gap-3">
                    @if (! $today)
                        <x-filament::button size="xl" icon="heroicon-o-map-pin" x-on:click="locate('checkIn')" x-bind:disabled="busy">{{ __('Check in now') }}</x-filament::button>
                    @elseif (! $today->check_out_time)
                        <x-filament::button size="xl" color="warning" icon="heroicon-o-arrow-right-on-rectangle" x-on:click="locate('checkOut')" x-bind:disabled="busy">{{ __('Check out now') }}</x-filament::button>
                    @else
                        <p class="rounded-lg bg-success-50 px-3 py-2 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">{{ __('Attendance for today is complete. Thank you!') }}</p>
                    @endif
                </div>

                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                    @if ($settings['demo'])
                        {{ __('Location check is disabled on the public demo.') }}
                    @elseif ($settings['lat'] !== null)
                        {{ __('Allowed radius: :radius m from school. Work hours start at :time.', ['radius' => $settings['radius'], 'time' => $settings['start']]) }}
                    @else
                        {{ __('School coordinates are not set, so location is not checked.') }}
                    @endif
                </p>
            </div>
        </x-filament::section>

        <x-filament::section class="lg:col-span-2" :heading="__('Last 14 days')">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr><th class="py-2">{{ __('Date') }}</th><th>{{ __('Check-in') }}</th><th>{{ __('Check-out') }}</th><th>{{ __('Status') }}</th><th>{{ __('Method') }}</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($this->getHistory() as $row)
                            <tr>
                                <td class="py-2">{{ $row->date->translatedFormat('D, d M Y') }}</td>
                                <td>{{ $row->check_in_time?->format('H:i') ?? '—' }}</td>
                                <td>{{ $row->check_out_time?->format('H:i') ?? '—' }}</td>
                                <td><x-filament::badge :color="$row->status->color()">{{ $row->status->label() }}</x-filament::badge></td>
                                <td class="uppercase text-gray-500">{{ $row->method }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('No check-ins yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
