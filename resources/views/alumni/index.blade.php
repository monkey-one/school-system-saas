@extends('website.layout')

@section('title', __('Alumni'))
@section('description', __('Alumni directory and testimonials of :school', ['school' => $tenant->name]))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => __('Alumni Directory'), 'subtitle' => __('Where our graduates continue their journey')])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        @foreach ([['total_alumni', __('Total alumni')], ['verified', __('Verified')], ['pursuing_education', __('Continuing education')], ['employed', __('Working')]] as [$key, $label])
            <div class="rounded-2xl border border-gray-100 bg-white p-5 text-center shadow-sm">
                <p class="font-heading text-3xl font-extrabold text-navy-700">{{ number_format($stats[$key]) }}</p>
                <p class="text-sm text-gray-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-10 flex flex-wrap gap-2">
        <a href="{{ route('alumni.index', $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ ! $selectedYear ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ __('All years') }}</a>
        @foreach ($graduationYears as $year)
            <a href="{{ route('alumni.index', ['year' => $year] + $tq) }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ (string) $selectedYear === (string) $year ? 'bg-navy-600 text-white' : 'bg-slate-100 text-gray-700 hover:bg-slate-200' }}">{{ $year }}</a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($alumni as $profile)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold-100 font-heading text-lg font-bold text-navy-700">{{ mb_substr($profile->student?->full_name ?? 'A', 0, 1) }}</div>
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-gray-800">{{ $profile->student?->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ __('Class of :year', ['year' => $profile->graduated_at?->format('Y') ?? $profile->student?->graduation_year]) }}</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-1 text-sm">
                    @if ($profile->higher_education)<div><dt class="inline text-gray-500">{{ __('Education') }}:</dt> <dd class="inline font-medium">{{ $profile->higher_education }}@if ($profile->major) — {{ $profile->major }}@endif</dd></div>@endif
                    @if ($profile->current_occupation)<div><dt class="inline text-gray-500">{{ __('Occupation') }}:</dt> <dd class="inline font-medium">{{ $profile->current_occupation }}@if ($profile->current_company) · {{ $profile->current_company }}@endif</dd></div>@endif
                    @if ($profile->current_city)<div><dt class="inline text-gray-500">{{ __('City') }}:</dt> <dd class="inline font-medium">{{ $profile->current_city }}</dd></div>@endif
                </dl>
            </div>
        @empty
            <p class="text-gray-500">{{ __('No alumni data yet.') }}</p>
        @endforelse
    </div>
    <div class="mt-8">{{ $alumni->links() }}</div>
</section>

@if ($testimonials->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <h2 class="font-heading text-3xl font-bold text-navy-700">{{ __('Alumni testimonials') }}</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach ($testimonials as $profile)
                <figure class="rounded-2xl bg-white p-6 shadow-sm">
                    <blockquote class="text-gray-700">“{{ $profile->testimonial }}”</blockquote>
                    <figcaption class="mt-4 text-sm">
                        <span class="font-semibold text-navy-700">{{ $profile->student?->full_name }}</span>
                        <span class="block text-gray-500">{{ __('Class of :year', ['year' => $profile->graduated_at?->format('Y')]) }}@if ($profile->current_occupation) · {{ $profile->current_occupation }}@endif</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
