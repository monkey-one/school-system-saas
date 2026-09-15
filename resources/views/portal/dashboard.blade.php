@extends('portal.layout')

@section('title', $portal === 'parent' ? $student->full_name : __('Dashboard'))

@section('content')
@php
    $days = [1 => __('Monday'), 2 => __('Tuesday'), 3 => __('Wednesday'), 4 => __('Thursday'), 5 => __('Friday'), 6 => __('Saturday'), 7 => __('Sunday')];
@endphp

<section class="rounded-2xl bg-gradient-to-r from-navy-700 to-navy-500 p-6 text-white shadow-sm">
    <p class="text-sm text-white/70">{{ now()->translatedFormat('l, d F Y') }}</p>
    <h2 class="mt-1 font-heading text-2xl font-bold">
        {{ $portal === 'parent' ? __('Progress of :name', ['name' => $student->full_name]) : __('Hello, :name!', ['name' => $student->nickname ?: $student->full_name]) }}
    </h2>
    <p class="mt-2 text-sm text-white/80">
        {{ __('Class') }} {{ $student->classroom?->name ?? '-' }}
        · NIS {{ $student->nis }}
        @if ($student->classroom?->homeroomTeacher) · {{ __('Homeroom Teacher') }}: {{ $student->classroom->homeroomTeacher->full_name }} @endif
    </p>
</section>

<section class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <a href="{{ route($portal . '.attendance', $childParam) }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Attendance this month') }}</p>
        <p class="mt-2 font-heading text-3xl font-bold text-navy-700">{{ $attendance['rate'] !== null ? $attendance['rate'] . '%' : '—' }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ __(':present present, :absent absent', ['present' => $attendance['hadir'] + $attendance['terlambat'], 'absent' => $attendance['alfa']]) }}</p>
    </a>
    <a href="{{ route($portal . '.bills', $childParam) }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Outstanding bills') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold {{ $billSummary['outstanding'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ \App\Helpers\CurrencyHelper::format($billSummary['outstanding']) }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ __(':count overdue', ['count' => $billSummary['overdue_count']]) }}</p>
    </a>
    <a href="{{ route($portal . '.report-cards', $childParam) }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Report Cards') }}</p>
        <p class="mt-2 font-heading text-3xl font-bold text-navy-700">{{ $reportCards->count() }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ $reportCards->first()?->semester?->name ? __('Latest: :semester', ['semester' => $reportCards->first()->semester->name]) : __('Not published yet') }}</p>
    </a>
    <a href="{{ route($portal . '.activities', $childParam) }}" class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Borrowed books') }}</p>
        <p class="mt-2 font-heading text-3xl font-bold text-navy-700">{{ $activeLoans }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ __('Library loans not yet returned') }}</p>
    </a>
</section>

<div class="grid gap-6 lg:grid-cols-3">
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-heading font-bold text-navy-700">{{ __('Today\'s schedule') }} · {{ $days[now()->dayOfWeekIso] }}</h3>
            <a href="{{ route($portal . '.schedule', $childParam) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('Full schedule') }}</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($todaySchedule as $lesson)
                <div class="flex items-center gap-4 py-3">
                    <div class="w-24 shrink-0 text-sm font-semibold text-navy-600">{{ substr($lesson->start_time, 0, 5) }}–{{ substr($lesson->end_time, 0, 5) }}</div>
                    <div class="min-w-0">
                        <p class="truncate font-medium text-gray-800">{{ $lesson->classroomSubject?->subject?->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $lesson->teacher?->full_name }} @if ($lesson->room) · {{ $lesson->room }} @endif</p>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">{{ __('No lessons today.') }}</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-heading font-bold text-navy-700">{{ __('Bills due') }}</h3>
            <a href="{{ route($portal . '.bills', $childParam) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All bills') }}</a>
        </div>
        <div class="space-y-3">
            @forelse ($unpaidBills as $bill)
                <div class="rounded-xl border border-gray-100 p-3">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium">{{ $bill->sppType?->name }} {{ $bill->period }}</p>
                        @include('portal.partials.badge', ['label' => $bill->status->label(), 'color' => $bill->status->color()])
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ __('Due') }} {{ $bill->due_date?->translatedFormat('d M Y') }} · {{ \App\Helpers\CurrencyHelper::format($bill->final_amount) }}</p>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-green-700">{{ __('All bills are paid. Thank you!') }}</p>
            @endforelse
        </div>
    </section>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-heading font-bold text-navy-700">{{ __('Latest grades') }}</h3>
            <a href="{{ route($portal . '.grades', $childParam) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All grades') }}</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($recentGrades as $grade)
                <div class="flex items-center justify-between gap-4 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ $grade->assessment?->classroomSubject?->subject?->name }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $grade->assessment?->name }} · {{ $grade->assessment?->assessmentType?->name }}</p>
                    </div>
                    <span class="font-heading text-lg font-bold {{ (float) $grade->score >= 75 ? 'text-green-600' : 'text-amber-600' }}">{{ rtrim(rtrim((string) $grade->score, '0'), '.') }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">{{ __('No grades yet.') }}</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-heading font-bold text-navy-700">{{ __('Announcements') }}</h3>
            <a href="{{ route($portal . '.announcements') }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('See all') }}</a>
        </div>
        <div class="space-y-4">
            @forelse ($announcements as $announcement)
                <div>
                    <p class="text-sm font-semibold text-gray-800">@if ($announcement->is_pinned)📌 @endif{{ $announcement->title }}</p>
                    <p class="text-xs text-gray-500">{{ $announcement->published_at?->translatedFormat('d M Y') }}</p>
                    <p class="mt-1 line-clamp-2 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 140) }}</p>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-gray-500">{{ __('No announcements.') }}</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
