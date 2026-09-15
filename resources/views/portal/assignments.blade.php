@extends('portal.layout')

@section('title', __('Assignments'))

@section('content')
@php
    $groups = $assignments->groupBy(function ($assignment) {
        $submission = $assignment->submissions->first();

        return match (true) {
            $submission?->score !== null => 'graded',
            $submission !== null => 'submitted',
            default => 'todo',
        };
    });
    $labels = ['todo' => __('To do'), 'submitted' => __('Submitted'), 'graded' => __('Graded')];
@endphp

@if ($assignments->isEmpty())
    <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm">{{ __('No assignments yet.') }}</div>
@endif

@foreach (['todo', 'submitted', 'graded'] as $group)
    @if ($groups->has($group))
        <section>
            <h2 class="mb-3 font-heading text-lg font-bold text-navy-700">{{ $labels[$group] }} <span class="text-sm font-medium text-gray-500">({{ $groups[$group]->count() }})</span></h2>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($groups[$group] as $assignment)
                    @php $submission = $assignment->submissions->first(); $overdue = ! $submission && $assignment->due_at->isPast(); @endphp
                    <a href="{{ route($portal . '.assignments.show', $childParam + ['assignment' => $assignment->id]) }}" class="block rounded-2xl border {{ $overdue ? 'border-red-200' : 'border-gray-100' }} bg-white p-5 shadow-sm transition hover:shadow">
                        <p class="text-xs font-semibold uppercase text-gold-600">{{ $assignment->classroomSubject?->subject?->name }}</p>
                        <h3 class="mt-1 font-heading font-bold text-navy-700">{{ $assignment->title }}</h3>
                        <p class="mt-1 text-sm {{ $overdue ? 'font-semibold text-red-600' : 'text-gray-500' }}">{{ __('Due') }} {{ $assignment->due_at->translatedFormat('D, d M Y H:i') }}</p>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ $assignment->teacher?->full_name }}</span>
                            @if ($submission?->score !== null)
                                @include('portal.partials.badge', ['label' => __('Score') . ' ' . rtrim(rtrim((string) $submission->score, '0'), '.') . '/' . $assignment->max_score, 'color' => 'success'])
                            @elseif ($submission)
                                @include('portal.partials.badge', ['label' => $submission->is_late ? __('Submitted late') : __('Submitted'), 'color' => $submission->is_late ? 'warning' : 'info'])
                            @elseif ($overdue)
                                @include('portal.partials.badge', ['label' => __('Overdue'), 'color' => 'danger'])
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endforeach
@endsection
