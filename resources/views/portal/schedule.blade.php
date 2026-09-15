@extends('portal.layout')

@section('title', __('Schedule'))

@section('content')
@php
    $days = [1 => __('Monday'), 2 => __('Tuesday'), 3 => __('Wednesday'), 4 => __('Thursday'), 5 => __('Friday'), 6 => __('Saturday')];
@endphp

<p class="text-sm text-gray-600">{{ __('Weekly lesson schedule for class :class.', ['class' => $student->classroom?->name ?? '-']) }}</p>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($days as $number => $day)
        <section class="rounded-2xl border {{ now()->dayOfWeekIso === $number ? 'border-gold-400 ring-2 ring-gold-100' : 'border-gray-100' }} bg-white p-5 shadow-sm">
            <h3 class="mb-3 font-heading font-bold text-navy-700">{{ $day }}</h3>
            <div class="space-y-3">
                @forelse ($schedule->get($number, collect()) as $lesson)
                    <div class="flex gap-3">
                        <div class="w-20 shrink-0 text-xs font-semibold leading-5 text-navy-600">{{ substr($lesson->start_time, 0, 5) }}<br>{{ substr($lesson->end_time, 0, 5) }}</div>
                        <div class="min-w-0 border-l-2 border-navy-100 pl-3">
                            <p class="truncate text-sm font-medium text-gray-800">{{ $lesson->classroomSubject?->subject?->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ $lesson->teacher?->full_name }}@if ($lesson->room) · {{ $lesson->room }}@endif</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">{{ __('No lessons.') }}</p>
                @endforelse
            </div>
        </section>
    @endforeach
</div>
@endsection
