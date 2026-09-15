@extends('portal.layout')

@section('title', __('Online Exams'))

@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($exams as $exam)
        @php
            $attempt = $exam->attempts->first();
            $state = match (true) {
                $attempt?->isSubmitted() => 'done',
                $exam->isOpen() => 'open',
                $exam->starts_at->isFuture() => 'upcoming',
                default => 'closed',
            };
            $badge = ['done' => [__('Completed'), 'success'], 'open' => [__('Open now'), 'warning'], 'upcoming' => [__('Upcoming'), 'info'], 'closed' => [__('Closed'), 'gray']][$state];
        @endphp
        <section class="flex flex-col rounded-2xl border {{ $state === 'open' ? 'border-gold-400' : 'border-gray-100' }} bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-semibold uppercase text-gold-600">{{ $exam->classroomSubject?->subject?->name }}</p>
                @include('portal.partials.badge', ['label' => $badge[0], 'color' => $badge[1]])
            </div>
            <h3 class="mt-1 font-heading text-lg font-bold text-navy-700">{{ $exam->title }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $exam->starts_at->translatedFormat('d M Y H:i') }} – {{ $exam->ends_at->translatedFormat('H:i') }}</p>
            <p class="text-sm text-gray-500">{{ __(':count questions', ['count' => $exam->questions_count]) }} · {{ __(':minutes minutes', ['minutes' => $exam->duration_minutes]) }}</p>

            <div class="mt-auto pt-4">
                @if ($state === 'done' && ($exam->show_result || $portal === 'parent'))
                    <p class="font-heading text-2xl font-bold {{ (float) $attempt->score >= 75 ? 'text-green-600' : 'text-amber-600' }}">{{ __('Score') }} {{ rtrim(rtrim((string) $attempt->score, '0'), '.') }}</p>
                @elseif ($state === 'done')
                    <p class="text-sm text-gray-500">{{ __('Submitted. The score will be announced by the teacher.') }}</p>
                @elseif ($portal === 'student' && ($state === 'open' || $attempt))
                    <a href="{{ route('student.exams.take', $exam) }}" class="block rounded-xl bg-gold-500 py-2.5 text-center font-bold text-navy-800 hover:bg-gold-400">{{ $attempt ? __('Continue exam') : __('Start exam') }}</a>
                @endif
            </div>
        </section>
    @empty
        <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm md:col-span-2 xl:col-span-3">{{ __('No online exams yet.') }}</div>
    @endforelse
</div>
@endsection
