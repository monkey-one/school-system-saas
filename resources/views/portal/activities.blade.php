@extends('portal.layout')

@section('title', __('Library & Activities'))

@section('content')
<section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="font-heading font-bold text-navy-700">{{ __('Extracurricular activities') }}</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($extracurriculars as $row)
            <div class="rounded-xl border border-gray-100 p-4">
                <p class="font-semibold text-gray-800">{{ $row->extracurricular?->name }}</p>
                <p class="text-xs text-gray-500">{{ $row->academicYear?->name }} · {{ __('Coach') }}: {{ $row->extracurricular?->teacher?->full_name ?? '-' }}</p>
                @if ($row->extracurricular?->schedule)<p class="mt-1 text-xs text-gray-500">{{ $row->extracurricular->schedule }}</p>@endif
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ __('Score') }}</span>
                    <span class="font-heading text-lg font-bold text-navy-700">{{ $row->score ?? '-' }}</span>
                </div>
                @if ($row->description)<p class="mt-2 text-sm text-gray-600">{{ $row->description }}</p>@endif
            </div>
        @empty
            <p class="text-sm text-gray-500">{{ __('Not registered in any extracurricular activity.') }}</p>
        @endforelse
    </div>
</section>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Library loans') }}</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('Book') }}</th>
                    <th class="px-5 py-3">{{ __('Loan date') }}</th>
                    <th class="px-5 py-3">{{ __('Due date') }}</th>
                    <th class="px-5 py-3">{{ __('Returned') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Fine') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($loans as $loan)
                    @php $overdue = ! $loan->return_date && $loan->due_date?->isPast(); @endphp
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $loan->book?->title }}</p>
                            <p class="text-xs text-gray-500">{{ $loan->book?->author }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-3">{{ $loan->loan_date?->translatedFormat('d M Y') }}</td>
                        <td class="whitespace-nowrap px-5 py-3 {{ $overdue ? 'font-semibold text-red-600' : '' }}">{{ $loan->due_date?->translatedFormat('d M Y') }}</td>
                        <td class="whitespace-nowrap px-5 py-3">
                            @if ($loan->return_date)
                                {{ $loan->return_date->translatedFormat('d M Y') }}
                            @else
                                @include('portal.partials.badge', ['label' => $overdue ? __('Overdue') : __('Borrowed'), 'color' => $overdue ? 'danger' : 'info'])
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">{{ (float) $loan->fine_amount > 0 ? \App\Helpers\CurrencyHelper::format($loan->fine_amount) : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">{{ __('No library loans.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
