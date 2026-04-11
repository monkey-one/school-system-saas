<x-filament-panels::page>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-primary-600">{{ number_format($totalTenants) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Total Schools') }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-success-600">{{ number_format($activeTenants) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Active Schools') }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-info-600">{{ number_format($totalUsers) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Total Users') }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-warning-600">{{ number_format($totalStudents) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Total Students') }}</p>
            </div>
        </x-filament::section>
        <x-filament::section>
            <div class="text-center">
                <p class="text-3xl font-bold text-primary-600">{{ number_format($totalTeachers) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ __('Total Teachers') }}</p>
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Monthly Growth --}}
        <x-filament::section>
            <x-slot name="heading">{{ __('Monthly Growth (Last 6 Months)') }}</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 font-medium text-gray-600">{{ __('Month') }}</th>
                            <th class="text-right py-2 font-medium text-gray-600">{{ __('New Schools') }}</th>
                            <th class="text-right py-2 font-medium text-gray-600">{{ __('New Users') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyGrowth as $row)
                        <tr class="border-b border-gray-50">
                            <td class="py-2">{{ $row['month'] }}</td>
                            <td class="py-2 text-right">
                                <x-filament::badge color="success">{{ $row['tenants'] }}</x-filament::badge>
                            </td>
                            <td class="py-2 text-right">
                                <x-filament::badge color="info">{{ $row['users'] }}</x-filament::badge>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Subscription Stats --}}
        <x-filament::section>
            <x-slot name="heading">{{ __('Subscription Status') }}</x-slot>
            <div class="space-y-3">
                @forelse($subscriptionStats as $status => $count)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <span class="font-medium capitalize">{{ __($status) }}</span>
                    <x-filament::badge :color="match($status) {
                        'active' => 'success',
                        'trial' => 'info',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning'
                    }">{{ number_format($count) }}</x-filament::badge>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">{{ __('No subscription data') }}</p>
                @endforelse
            </div>
        </x-filament::section>
    </div>

    {{-- Top Schools --}}
    <x-filament::section>
        <x-slot name="heading">{{ __('Top 10 Schools by Students') }}</x-slot>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 font-medium text-gray-600">#</th>
                        <th class="text-left py-2 font-medium text-gray-600">{{ __('School Name') }}</th>
                        <th class="text-right py-2 font-medium text-gray-600">{{ __('Students') }}</th>
                        <th class="text-right py-2 font-medium text-gray-600">{{ __('Teachers') }}</th>
                        <th class="text-center py-2 font-medium text-gray-600">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topTenants as $i => $t)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 text-gray-500">{{ $i + 1 }}</td>
                        <td class="py-2 font-medium">{{ $t->name }}</td>
                        <td class="py-2 text-right">{{ number_format($t->students_count) }}</td>
                        <td class="py-2 text-right">{{ number_format($t->teachers_count) }}</td>
                        <td class="py-2 text-center">
                            <x-filament::badge color="success">{{ __('Active') }}</x-filament::badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-panels::page>
