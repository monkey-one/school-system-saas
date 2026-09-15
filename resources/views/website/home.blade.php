@extends('website.layout')

@section('content')
@php
    $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : [];
    $settings = $tenant->settings ?? [];
    $hero = filled($settings['hero_image'] ?? null) ? asset('storage/' . $settings['hero_image']) : null;
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden bg-navy-800 text-white">
    @if ($hero)
        <img src="{{ $hero }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-30">
    @endif
    <div class="absolute inset-0 bg-gradient-to-r from-navy-900 via-navy-800/90 to-navy-700/60"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
        <div>
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-gold-400">
                @if ($tenant->accreditation) {{ __('Accreditation') }} {{ $tenant->accreditation }} · @endif {{ $tenant->school_type?->value }} · {{ $tenant->city }}
            </p>
            <h1 class="mt-5 font-heading text-4xl font-extrabold leading-tight sm:text-5xl">{{ $settings['hero_title'] ?? $tenant->name }}</h1>
            <p class="mt-5 max-w-xl text-lg text-white/80">{{ $settings['hero_subtitle'] ?? \Illuminate\Support\Str::limit(strip_tags((string) $tenant->description), 220) }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('ppdb.index', $tq) }}" class="rounded-xl bg-gold-500 px-6 py-3 font-bold text-navy-900 hover:bg-gold-400">{{ $openWave ? __('Register now (PPDB)') : __('PPDB information') }}</a>
                <a href="{{ route('website.about', $tq) }}" class="rounded-xl border border-white/30 px-6 py-3 font-semibold hover:bg-white/10">{{ __('Explore the school') }}</a>
            </div>
            @if ($openWave)
                <p class="mt-4 text-sm text-white/70">{{ __(':wave is open until :date.', ['wave' => $openWave->name, 'date' => $openWave->closes_at->translatedFormat('d F Y')]) }}</p>
            @endif
        </div>
        <div class="grid grid-cols-2 gap-4">
            @foreach ([['students', __('Active students')], ['teachers', __('Teachers & staff')], ['achievements', __('Achievements')], ['alumni', __('Alumni')]] as [$key, $label])
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <p class="font-heading text-4xl font-extrabold text-gold-400">{{ number_format($stats[$key]) }}</p>
                    <p class="mt-1 text-sm text-white/70">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Principal greeting --}}
@if (filled($settings['principal_greeting'] ?? null))
<section class="bg-slate-50">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 md:grid-cols-3">
        <div class="text-center">
            @if (filled($settings['principal_photo'] ?? null))
                <img src="{{ asset('storage/' . $settings['principal_photo']) }}" alt="{{ $tenant->principal_name }}" class="mx-auto h-56 w-56 rounded-3xl object-cover shadow-lg">
            @else
                <div class="mx-auto flex h-56 w-56 items-center justify-center rounded-3xl bg-navy-600 font-heading text-6xl font-bold text-gold-400">{{ mb_substr($tenant->principal_name ?? 'K', 0, 1) }}</div>
            @endif
            <p class="mt-4 font-heading font-bold text-navy-700">{{ $tenant->principal_name }}</p>
            <p class="text-sm text-gray-500">{{ __('Principal') }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm font-semibold uppercase tracking-wider text-gold-600">{{ __('Principal\'s greeting') }}</p>
            <div class="mt-3 space-y-4 text-lg leading-relaxed text-gray-700">{!! nl2br(e($settings['principal_greeting'])) !!}</div>
        </div>
    </div>
</section>
@endif

{{-- News --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-gold-600">{{ __('Latest') }}</p>
            <h2 class="font-heading text-3xl font-bold text-navy-700">{{ __('News & Articles') }}</h2>
        </div>
        <a href="{{ route('website.news', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All news') }} &rarr;</a>
    </div>
    <div class="mt-8 grid gap-6 md:grid-cols-3">
        @forelse ($featured as $post)
            @include('website.partials.post-card', ['post' => $post, 'tq' => $tq])
        @empty
            <p class="text-gray-500">{{ __('No news yet.') }}</p>
        @endforelse
    </div>
</section>

{{-- Agenda + achievements --}}
<section class="bg-slate-50">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2">
        <div>
            <div class="flex items-end justify-between">
                <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('Upcoming agenda') }}</h2>
                <a href="{{ route('website.agenda', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('Full agenda') }} &rarr;</a>
            </div>
            <div class="mt-6 space-y-3">
                @forelse ($events as $event)
                    <div class="flex gap-4 rounded-2xl bg-white p-4 shadow-sm">
                        <div class="w-16 shrink-0 rounded-xl bg-navy-600 py-2 text-center text-white">
                            <p class="font-heading text-2xl font-bold leading-none">{{ $event->starts_at->format('d') }}</p>
                            <p class="text-xs uppercase">{{ $event->starts_at->translatedFormat('M') }}</p>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800">{{ $event->title }}</p>
                            <p class="text-sm text-gray-500">{{ $event->starts_at->translatedFormat('l, H:i') }}@if ($event->location) · {{ $event->location }}@endif</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">{{ __('No upcoming events.') }}</p>
                @endforelse
            </div>
        </div>
        <div>
            <div class="flex items-end justify-between">
                <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('Achievements') }}</h2>
                <a href="{{ route('website.achievements', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All achievements') }} &rarr;</a>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @forelse ($achievements as $achievement)
                    @include('website.partials.achievement-card', ['achievement' => $achievement])
                @empty
                    <p class="text-gray-500">{{ __('No achievements yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- Gallery --}}
@if ($albums->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="flex items-end justify-between">
        <h2 class="font-heading text-3xl font-bold text-navy-700">{{ __('Gallery') }}</h2>
        <a href="{{ route('website.gallery', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All albums') }} &rarr;</a>
    </div>
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3">
        @foreach ($albums as $album)
            @include('website.partials.album-card', ['album' => $album, 'tq' => $tq])
        @endforeach
    </div>
</section>
@endif

{{-- Teachers --}}
@if ($teachers->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="flex items-end justify-between">
            <h2 class="font-heading text-3xl font-bold text-navy-700">{{ __('Teachers & Staff') }}</h2>
            <a href="{{ route('website.teachers', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">{{ __('All teachers') }} &rarr;</a>
        </div>
        <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ($teachers as $teacher)
                @include('website.partials.teacher-card', ['teacher' => $teacher])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- PPDB banner --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
    <div class="flex flex-col items-start justify-between gap-6 rounded-3xl bg-gradient-to-r from-gold-500 to-gold-400 p-8 text-navy-900 md:flex-row md:items-center md:p-12">
        <div>
            <h2 class="font-heading text-3xl font-extrabold">{{ __('Join :school', ['school' => $tenant->name]) }}</h2>
            <p class="mt-2 max-w-2xl text-navy-800">{{ __('Online new student admission: register, upload documents and check your status from any device.') }}</p>
        </div>
        <a href="{{ route('ppdb.index', $tq) }}" class="shrink-0 rounded-xl bg-navy-800 px-6 py-3 font-bold text-white hover:bg-navy-700">{{ __('PPDB Online') }}</a>
    </div>
</section>
@endsection
