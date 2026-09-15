@extends('portal.layout')

@section('title', __('Attendance'))

@section('content')
@php
    $prev = $month->copy()->subMonth()->format('Y-m');
    $next = $month->copy()->addMonth();
    $statuses = \App\Enums\AttendanceStatus::cases();
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2">
        <a href="{{ route($portal . '.attendance', $childParam + ['month' => $prev]) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm hover:bg-gray-50">&larr;</a>
        <span class="min-w-40 text-center font-heading font-bold text-navy-700">{{ $month->translatedFormat('F Y') }}</span>
        @if ($next->lte(now()->startOfMonth()))
            <a href="{{ route($portal . '.attendance', $childParam + ['month' => $next->format('Y-m')]) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm hover:bg-gray-50">&rarr;</a>
        @endif
    </div>
    <p class="text-sm text-gray-600">
        {{ __('Overall presence rate') }}: <strong class="text-navy-700">{{ $overall['rate'] !== null ? $overall['rate'] . '%' : '—' }}</strong>
    </p>
</div>

<section class="grid grid-cols-2 gap-3 sm:grid-cols-5">
    @foreach ($statuses as $status)
        <div class="rounded-2xl border border-gray-100 bg-white p-4 text-center shadow-sm">
            <p class="text-xs font-medium uppercase text-gray-500">{{ $status->label() }}</p>
            <p class="mt-1 font-heading text-2xl font-bold text-navy-700">{{ $summary[$status->value] }}</p>
        </div>
    @endforeach
</section>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('Date') }}</th>
                    <th class="px-5 py-3">{{ __('Subject') }}</th>
                    <th class="px-5 py-3">{{ __('Status') }}</th>
                    <th class="px-5 py-3">{{ __('Check-in') }}</th>
                    <th class="px-5 py-3">{{ __('Method') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($records as $record)
                    <tr>
                        <td class="whitespace-nowrap px-5 py-3">{{ $record->attendanceSession?->date?->translatedFormat('D, d M Y') }}</td>
                        <td class="px-5 py-3">{{ $record->attendanceSession?->classroomSubject?->subject?->name ?? '-' }}</td>
                        <td class="px-5 py-3">@include('portal.partials.badge', ['label' => $record->status->label(), 'color' => $record->status->color()])</td>
                        <td class="px-5 py-3">{{ $record->check_in_time?->format('H:i') ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $record->method === 'qr_code' ? __('QR code') : __('Manual') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">{{ __('No attendance records this month.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
