<a href="{{ route('website.gallery.show', ['slug' => $album->slug] + $tq) }}" class="group relative block aspect-[4/3] overflow-hidden rounded-2xl bg-navy-600">
    @if ($album->cover_image)
        <img src="{{ asset('storage/' . $album->cover_image) }}" alt="{{ $album->title }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-105">
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-navy-900/90 via-navy-900/20 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
        <p class="line-clamp-2 font-heading font-bold">{{ $album->title }}</p>
        <p class="text-xs text-white/70">{{ $album->event_date?->translatedFormat('d M Y') }} · {{ __(':count items', ['count' => $album->items_count ?? $album->items->count()]) }}</p>
    </div>
</a>
