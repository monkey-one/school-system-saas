@extends('portal.layout')

@section('title', __('Discipline & Counseling'))

@section('content')
<section class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Discipline points this year') }}</p>
        <p class="mt-2 font-heading text-3xl font-extrabold {{ $totalPoints >= 50 ? 'text-red-600' : ($totalPoints >= 20 ? 'text-amber-600' : 'text-green-600') }}">{{ $totalPoints }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ __('Lower is better.') }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Recorded violations') }}</p>
        <p class="mt-2 font-heading text-3xl font-extrabold text-navy-700">{{ $violations->count() }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Achievements') }}</p>
        <p class="mt-2 font-heading text-3xl font-extrabold text-gold-600">{{ $achievements->count() }}</p>
    </div>
</section>

<div class="grid gap-6 lg:grid-cols-2">
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Discipline records') }}</h2>
        <div class="divide-y divide-gray-100">
            @forelse ($violations as $violation)
                <div class="flex items-start justify-between gap-4 px-6 py-4">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-800">{{ $violation->violationType?->name ?? __('Violation') }}</p>
                        <p class="text-xs text-gray-500">{{ $violation->occurred_at->translatedFormat('d M Y') }}@if ($violation->action_taken) · {{ $violation->action_taken }}@endif</p>
                        @if ($violation->description)<p class="mt-1 text-sm text-gray-600">{{ $violation->description }}</p>@endif
                    </div>
                    @include('portal.partials.badge', ['label' => '+' . $violation->points, 'color' => $violation->points >= 25 ? 'danger' : 'warning'])
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-green-700">{{ __('No discipline records. Keep it up!') }}</p>
            @endforelse
        </div>
    </section>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Achievements') }}</h2>
            <div class="divide-y divide-gray-100">
                @forelse ($achievements as $achievement)
                    <div class="px-6 py-4">
                        <p class="font-medium text-gray-800">🏆 {{ $achievement->rank }} — {{ $achievement->title }}</p>
                        <p class="text-xs text-gray-500">{{ \App\Models\Achievement::levelLabels()[$achievement->level] ?? $achievement->level }} · {{ $achievement->achieved_at->translatedFormat('d M Y') }}</p>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-500">{{ __('No achievements yet.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Counseling notes shared by the school') }}</h2>
            <div class="divide-y divide-gray-100">
                @forelse ($counseling as $note)
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold uppercase text-gold-600">{{ \App\Models\CounselingNote::categoryLabels()[$note->category] ?? $note->category }} · {{ $note->session_date->translatedFormat('d M Y') }}</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $note->summary }}</p>
                        @if ($note->follow_up)<p class="mt-2 text-sm text-gray-600"><span class="font-semibold">{{ __('Follow-up') }}:</span> {{ $note->follow_up }}</p>@endif
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-500">{{ __('No shared counseling notes.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
