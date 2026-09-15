@extends('website.layout')

@section('title', $album->title)
@section('description', \Illuminate\Support\Str::limit((string) $album->description, 160))
@if ($album->cover_image)
    @section('og_image', asset('storage/' . $album->cover_image))
@endif

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => $album->title, 'subtitle' => $album->event_date?->translatedFormat('l, d F Y')])

<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6" x-data="{ open: null }">
    <a href="{{ route('website.gallery', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">&larr; {{ __('All albums') }}</a>
    @if ($album->description)<p class="mt-4 max-w-3xl text-gray-700">{{ $album->description }}</p>@endif

    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse ($album->items as $item)
            @if ($item->type === 'video' && $item->youtubeId())
                <div class="overflow-hidden rounded-2xl bg-black md:col-span-2">
                    <div class="aspect-video">
                        <iframe src="https://www.youtube-nocookie.com/embed/{{ $item->youtubeId() }}" title="{{ $item->caption ?? $album->title }}" class="h-full w-full" loading="lazy" allow="accelerometer; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    @if ($item->caption)<p class="bg-white px-4 py-2 text-sm text-gray-600">{{ $item->caption }}</p>@endif
                </div>
            @elseif ($item->image)
                <button type="button" @click="open = @js(asset('storage/' . $item->image))" class="group overflow-hidden rounded-2xl bg-slate-100 text-left">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->caption ?? $album->title }}" loading="lazy" class="aspect-square w-full object-cover transition group-hover:scale-105">
                    @if ($item->caption)<p class="px-3 py-2 text-xs text-gray-600">{{ $item->caption }}</p>@endif
                </button>
            @endif
        @empty
            <p class="text-gray-500">{{ __('This album is empty.') }}</p>
        @endforelse
    </div>

    <div x-show="open" x-cloak @click="open = null" @keydown.escape.window="open = null" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4">
        <img :src="open" alt="" class="max-h-full max-w-full rounded-lg">
    </div>
</section>
@endsection
