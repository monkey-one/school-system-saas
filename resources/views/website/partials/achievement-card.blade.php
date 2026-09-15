<div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
    @if ($achievement->image)
        <img src="{{ asset('storage/' . $achievement->image) }}" alt="" loading="lazy" class="h-16 w-16 shrink-0 rounded-xl object-cover">
    @else
        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-2xl">🏆</div>
    @endif
    <div class="min-w-0">
        <p class="text-xs font-semibold uppercase text-gold-600">{{ \App\Models\Achievement::levelLabels()[$achievement->level] ?? $achievement->level }} · {{ $achievement->achieved_at?->translatedFormat('M Y') }}</p>
        <p class="line-clamp-2 font-semibold text-gray-800">{{ $achievement->rank ? $achievement->rank . ' — ' : '' }}{{ $achievement->title }}</p>
        <p class="truncate text-sm text-gray-500">{{ $achievement->participant }}</p>
    </div>
</div>
