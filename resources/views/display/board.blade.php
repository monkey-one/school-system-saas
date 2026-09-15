<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="600">
    <meta name="robots" content="noindex">
    <title>{{ __('Lobby Display') }} · {{ $tenant->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes marquee { from { transform: translateX(100%); } to { transform: translateX(-100%); } }
        .marquee { animation: marquee 40s linear infinite; white-space: nowrap; }
    </style>
</head>
<body class="h-screen overflow-hidden bg-[#0B1627] text-white"
      x-data="{
          slide: 0,
          slides: {{ collect([$announcements->isNotEmpty(), $events->isNotEmpty(), $achievements->isNotEmpty(), $news->isNotEmpty()])->filter()->count() ?: 1 }},
          now: new Date(),
          init() {
              setInterval(() => this.now = new Date(), 1000);
              setInterval(() => this.slide = (this.slide + 1) % this.slides, 12000);
          }
      }">
@php
    $panels = collect([
        ['key' => 'announcements', 'title' => __('Announcements'), 'items' => $announcements],
        ['key' => 'events', 'title' => __('Upcoming agenda'), 'items' => $events],
        ['key' => 'achievements', 'title' => __('Achievements'), 'items' => $achievements],
        ['key' => 'news', 'title' => __('News & Articles'), 'items' => $news],
    ])->filter(fn ($p) => $p['items']->isNotEmpty())->values();
@endphp
<div class="flex h-full flex-col">
    <header class="flex items-center justify-between bg-[#172D4A] px-10 py-5">
        <div class="flex items-center gap-4">
            @if ($tenant->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="" class="h-16 w-16 rounded-xl bg-white object-contain p-1">
            @else
                <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-amber-500 text-3xl font-extrabold">{{ mb_substr($tenant->name, 0, 1) }}</div>
            @endif
            <div>
                <p class="text-3xl font-extrabold">{{ $tenant->name }}</p>
                <p class="text-lg text-white/60">{{ $tenant->address }}, {{ $tenant->city }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-5xl font-extrabold tabular-nums" x-text="now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })"></p>
            <p class="text-xl text-amber-400" x-text="now.toLocaleDateString('{{ app()->getLocale() === 'id' ? 'id-ID' : 'en-GB' }}', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></p>
        </div>
    </header>

    <main class="grid flex-1 grid-cols-3 gap-8 overflow-hidden p-10">
        <section class="relative col-span-2 overflow-hidden rounded-3xl bg-white/5 p-10">
            @forelse ($panels as $index => $panel)
                <div x-show="slide === {{ $index }}" x-transition.opacity.duration.700ms class="absolute inset-10">
                    <h2 class="text-4xl font-extrabold text-amber-400">{{ $panel['title'] }}</h2>
                    <div class="mt-8 space-y-6">
                        @foreach ($panel['items'] as $item)
                            @if ($panel['key'] === 'announcements')
                                <div><p class="text-3xl font-bold">@if ($item->is_pinned)📌 @endif{{ $item->title }}</p><p class="mt-2 line-clamp-2 text-2xl text-white/70">{{ \Illuminate\Support\Str::limit(strip_tags($item->content), 180) }}</p></div>
                            @elseif ($panel['key'] === 'events')
                                <div class="flex items-center gap-6"><div class="w-28 shrink-0 rounded-2xl bg-amber-500 py-3 text-center text-[#0B1627]"><p class="text-4xl font-extrabold leading-none">{{ $item->starts_at->format('d') }}</p><p class="text-lg font-bold uppercase">{{ $item->starts_at->translatedFormat('M') }}</p></div><div><p class="text-3xl font-bold">{{ $item->title }}</p><p class="text-2xl text-white/70">{{ $item->starts_at->translatedFormat('l, H:i') }}@if ($item->location) · {{ $item->location }}@endif</p></div></div>
                            @elseif ($panel['key'] === 'achievements')
                                <div><p class="text-3xl font-bold">🏆 {{ $item->rank }} — {{ $item->title }}</p><p class="text-2xl text-white/70">{{ $item->participant }} · {{ \App\Models\Achievement::levelLabels()[$item->level] ?? $item->level }}</p></div>
                            @else
                                <div><p class="text-3xl font-bold">{{ $item->title }}</p><p class="text-2xl text-white/70">{{ $item->published_at?->translatedFormat('d F Y') }}</p></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-3xl text-white/60">{{ __('Welcome to :school', ['school' => $tenant->name]) }}</p>
            @endforelse
        </section>

        <aside class="flex flex-col gap-8">
            <div class="rounded-3xl bg-emerald-600 p-8">
                <p class="text-2xl font-bold text-white/80">{{ __('Student attendance today') }}</p>
                <p class="mt-2 text-7xl font-extrabold">{{ $attendanceRate !== null ? $attendanceRate . '%' : '—' }}</p>
            </div>
            <div class="rounded-3xl bg-sky-700 p-8">
                <p class="text-2xl font-bold text-white/80">{{ __('Teachers present today') }}</p>
                <p class="mt-2 text-7xl font-extrabold">{{ $teachersPresent }}<span class="text-4xl text-white/60">/{{ $teachersTotal }}</span></p>
            </div>
            <div class="flex-1 rounded-3xl bg-white/5 p-8">
                <p class="text-2xl font-bold text-amber-400">{{ __('Vision') }}</p>
                <p class="mt-3 text-2xl leading-relaxed text-white/80">{{ \Illuminate\Support\Str::limit((string) $tenant->vision, 200) }}</p>
            </div>
        </aside>
    </main>

    <footer class="overflow-hidden bg-amber-500 py-4 text-3xl font-bold text-[#0B1627]">
        <div class="marquee inline-block">
            {{ $announcements->pluck('title')->prepend(__('Welcome to :school', ['school' => $tenant->name]))->implode('   ★   ') }}
        </div>
    </footer>
</div>
</body>
</html>
