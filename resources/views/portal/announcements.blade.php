@extends('portal.layout')

@section('title', __('Announcements'))

@section('content')
<div class="space-y-4" x-data="{ open: null }">
    @forelse ($announcements as $announcement)
        <article class="rounded-2xl border {{ $announcement->is_pinned ? 'border-gold-400' : 'border-gray-100' }} bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                @if ($announcement->is_pinned)
                    @include('portal.partials.badge', ['label' => __('Pinned'), 'color' => 'warning'])
                @endif
                <span class="text-xs text-gray-500">{{ $announcement->published_at?->translatedFormat('l, d F Y') }} · {{ $announcement->author?->name }}</span>
            </div>
            <h2 class="mt-2 font-heading text-lg font-bold text-navy-700">{{ $announcement->title }}</h2>
            <div class="prose prose-sm mt-2 max-w-none text-gray-700" :class="open === {{ $announcement->id }} ? '' : 'line-clamp-3'">
                {{ \App\Support\SafeHtml::basic($announcement->content) }}
            </div>
            <button type="button" class="mt-2 text-sm font-semibold text-navy-500 hover:underline"
                    @click="open = open === {{ $announcement->id }} ? null : {{ $announcement->id }}"
                    x-text="open === {{ $announcement->id }} ? @js(__('Show less')) : @js(__('Read more'))"></button>
        </article>
    @empty
        <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm">{{ __('No announcements.') }}</div>
    @endforelse
</div>
@endsection
