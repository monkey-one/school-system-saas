<a href="{{ route('website.news.show', ['slug' => $post->slug] + $tq) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
    <div class="aspect-[16/9] overflow-hidden bg-navy-50">
        @if ($post->cover_image)
            <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-navy-600 to-navy-500 p-6 text-center font-heading font-bold text-white/90">{{ \Illuminate\Support\Str::limit($post->title, 60) }}</div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-semibold uppercase text-gold-600">{{ \App\Models\Post::categoryLabels()[$post->category] ?? $post->category }} · {{ $post->published_at?->translatedFormat('d M Y') }}</p>
        <h3 class="mt-2 line-clamp-2 font-heading text-lg font-bold text-navy-700 group-hover:text-navy-500">{{ $post->title }}</h3>
        <p class="mt-2 line-clamp-3 text-sm text-gray-600">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>
    </div>
</a>
