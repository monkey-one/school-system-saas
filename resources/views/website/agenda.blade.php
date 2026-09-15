@extends('website.layout')

@section('title', __('Agenda'))

@section('content')
@include('website.partials.page-header', ['title' => __('School Agenda'), 'subtitle' => __('Academic calendar and upcoming activities')])

<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('Upcoming') }}</h2>
    <div class="mt-6 space-y-4">
        @forelse ($upcoming as $event)
            <div class="flex gap-5 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="w-20 shrink-0 rounded-xl bg-navy-600 py-3 text-center text-white">
                    <p class="font-heading text-3xl font-bold leading-none">{{ $event->starts_at->format('d') }}</p>
                    <p class="text-xs uppercase">{{ $event->starts_at->translatedFormat('M Y') }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase text-gold-600">{{ \App\Models\SchoolEvent::categoryLabels()[$event->category] ?? $event->category }}</p>
                    <h3 class="font-heading text-lg font-bold text-gray-800">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ $event->starts_at->translatedFormat('l, d F Y H:i') }}@if ($event->ends_at) – {{ $event->ends_at->isSameDay($event->starts_at) ? $event->ends_at->format('H:i') : $event->ends_at->translatedFormat('d F Y H:i') }}@endif
                        @if ($event->location) · {{ $event->location }}@endif
                    </p>
                    @if ($event->description)<p class="mt-2 text-sm text-gray-600">{{ $event->description }}</p>@endif
                </div>
            </div>
        @empty
            <p class="text-gray-500">{{ __('No upcoming events.') }}</p>
        @endforelse
    </div>

    @if ($past->isNotEmpty())
        <h2 class="mt-14 font-heading text-2xl font-bold text-navy-700">{{ __('Past activities') }}</h2>
        <ul class="mt-4 divide-y divide-gray-100 rounded-2xl border border-gray-100 bg-white">
            @foreach ($past as $event)
                <li class="flex items-center justify-between gap-4 px-5 py-3 text-sm">
                    <span class="font-medium text-gray-700">{{ $event->title }}</span>
                    <span class="shrink-0 text-gray-500">{{ $event->starts_at->translatedFormat('d M Y') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
