@extends('website.layout')

@section('title', $post->title)
@section('description', $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 160))
@if ($post->cover_image)
    @section('og_image', asset('storage/' . $post->cover_image))
@endif

@section('content')
@php
    $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : [];
    $shareUrl = urlencode(url()->full());
@endphp

<article class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <a href="{{ route('website.news', $tq) }}" class="text-sm font-semibold text-navy-500 hover:underline">&larr; {{ __('All news') }}</a>
    <p class="mt-6 text-sm font-semibold uppercase text-gold-600">{{ \App\Models\Post::categoryLabels()[$post->category] ?? $post->category }}</p>
    <h1 class="mt-2 font-heading text-3xl font-extrabold leading-tight text-navy-800 sm:text-4xl">{{ $post->title }}</h1>
    <p class="mt-3 text-sm text-gray-500">{{ $post->published_at?->translatedFormat('l, d F Y') }} · {{ $post->author?->name }} · {{ __(':count views', ['count' => number_format($post->views)]) }}</p>

    @if ($post->cover_image)
        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="mt-8 w-full rounded-2xl object-cover">
    @endif

    <div class="prose prose-lg mt-8 max-w-none prose-headings:font-heading prose-headings:text-navy-700">
        {{ \App\Support\SafeHtml::basic($post->content) }}
    </div>

    <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-6 text-sm">
        <span class="font-semibold text-gray-600">{{ __('Share') }}:</span>
        <a href="https://wa.me/?text={{ urlencode($post->title) }}%20{{ $shareUrl }}" target="_blank" rel="noopener" class="rounded-full bg-green-600 px-4 py-2 font-semibold text-white hover:bg-green-700">WhatsApp</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" class="rounded-full bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Facebook</a>
        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="rounded-full bg-gray-900 px-4 py-2 font-semibold text-white hover:bg-black">X</a>
    </div>
</article>

@if ($related->isNotEmpty())
<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('Related news') }}</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach ($related as $item)
                @include('website.partials.post-card', ['post' => $item, 'tq' => $tq])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
