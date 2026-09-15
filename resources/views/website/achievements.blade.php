@extends('website.layout')

@section('title', __('Achievements'))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => __('Achievements'), 'subtitle' => __('Proud achievements of our students, teachers and school')])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('website.achievements', $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ! $level ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ __('All levels') }}</a>
        @foreach (\App\Models\Achievement::levelLabels() as $key => $label)
            <a href="{{ route('website.achievements', ['level' => $key] + $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $level === $key ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($achievements as $achievement)
            <div class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                @if ($achievement->image)
                    <img src="{{ asset('storage/' . $achievement->image) }}" alt="{{ $achievement->title }}" loading="lazy" class="aspect-[16/9] w-full object-cover">
                @endif
                <div class="flex flex-1 flex-col p-5">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-gold-100 px-2 py-0.5 text-gold-600">{{ \App\Models\Achievement::levelLabels()[$achievement->level] ?? $achievement->level }}</span>
                        <span class="rounded-full bg-navy-50 px-2 py-0.5 text-navy-600">{{ \App\Models\Achievement::categoryLabels()[$achievement->category] ?? $achievement->category }}</span>
                        <span class="text-gray-500">{{ $achievement->achieved_at?->translatedFormat('d M Y') }}</span>
                    </div>
                    @if ($achievement->rank)<p class="mt-3 font-heading text-xl font-extrabold text-gold-600">🏆 {{ $achievement->rank }}</p>@endif
                    <h3 class="mt-1 font-heading text-lg font-bold text-navy-700">{{ $achievement->title }}</h3>
                    <p class="text-sm font-medium text-gray-700">{{ $achievement->participant }}</p>
                    @if ($achievement->organizer)<p class="text-xs text-gray-500">{{ __('Organizer') }}: {{ $achievement->organizer }}</p>@endif
                    @if ($achievement->description)<p class="mt-2 text-sm text-gray-600">{{ $achievement->description }}</p>@endif
                </div>
            </div>
        @empty
            <p class="text-gray-500">{{ __('No achievements yet.') }}</p>
        @endforelse
    </div>

    <div class="mt-10">{{ $achievements->links() }}</div>
</section>
@endsection
