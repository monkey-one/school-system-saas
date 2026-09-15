<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>@yield('title') · {{ $portal === 'parent' ? __('Parent Portal') : __('Student Portal') }} · {{ $tenant?->name ?? config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50: '#EEF2F7', 100: '#D4DEE9', 500: '#2D5F8A', 600: '#1E3A5F', 700: '#172D4A', 800: '#102035' },
                        gold: { 50: '#FFFBEB', 100: '#FEF3C7', 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706' },
                    },
                    fontFamily: {
                        heading: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="bg-slate-50 font-body text-gray-800 antialiased" x-data="{ nav: false }">
@php
    $isParent = $portal === 'parent';
    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'check' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'wallet' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        'book' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'megaphone' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
        'chat' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'mail' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'piggy' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        'pencil' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'computer' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'cog' =>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ];
    $childNav = [
        ['schedule', __('Schedule'), 'calendar'],
        ['attendance', __('Attendance'), 'check'],
        ['grades', __('Grades'), 'chart'],
        ['assignments', __('Assignments'), 'pencil'],
        ['exams', __('Online Exams'), 'computer'],
        ['report-cards', __('Report Cards'), 'doc'],
        ['bills', __('Bills & Payments'), 'wallet'],
        ['activities', __('Library & Activities'), 'book'],
        ['leave-requests', __('Leave Requests'), 'mail'],
        ['savings', __('Savings & Cashless'), 'piggy'],
        ['discipline', __('Discipline & Counseling'), 'shield'],
    ];
    $sections = $isParent
        ? [
            __('Family') => [['dashboard', __('My Children'), 'home', false]],
            $student->full_name => array_merge([['child', __('Overview'), 'user', true]], array_map(fn ($i) => [...$i, true], $childNav)),
            __('School') => [['announcements', __('Announcements'), 'megaphone', false], ['messages', __('Messages'), 'chat', false], ['profile', __('Account'), 'cog', false]],
        ]
        : [
            __('Menu') => array_merge([['dashboard', __('Dashboard'), 'home', false]], array_map(fn ($i) => [...$i, false], $childNav)),
            __('School') => [['announcements', __('Announcements'), 'megaphone', false], ['messages', __('Messages'), 'chat', false], ['profile', __('Account'), 'cog', false]],
        ];
@endphp

<div class="flex min-h-screen">
    <div x-show="nav" x-cloak @click="nav = false" class="fixed inset-0 z-40 bg-black/40 lg:hidden"></div>

    <aside :class="nav ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-navy-700 text-white transition-transform lg:static lg:translate-x-0">
        <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
            @if ($tenant?->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="" class="h-10 w-10 rounded-lg bg-white object-contain p-1">
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gold-500 font-heading font-bold">{{ mb_substr($tenant?->name ?? 'E', 0, 1) }}</div>
            @endif
            <div class="min-w-0">
                <p class="truncate font-heading text-sm font-bold">{{ $tenant?->name }}</p>
                <p class="text-xs text-white/60">{{ $isParent ? __('Parent Portal') : __('Student Portal') }}</p>
            </div>
        </div>

        @if ($isParent && $children->count() > 1)
            <div class="border-b border-white/10 px-4 py-3">
                <p class="mb-2 px-1 text-[11px] font-semibold uppercase tracking-wider text-white/40">{{ __('Switch child') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($children as $child)
                        <a href="{{ route('parent.child', $child) }}"
                           class="rounded-full px-3 py-1 text-xs font-medium {{ $child->id === $student->id ? 'bg-gold-500 text-navy-800' : 'bg-white/10 text-white/80 hover:bg-white/20' }}">
                            {{ \Illuminate\Support\Str::words($child->full_name, 2, '') }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-4">
            @foreach ($sections as $heading => $items)
                <div>
                    <p class="mb-1 truncate px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40">{{ $heading }}</p>
                    @foreach ($items as [$name, $label, $icon, $withChild])
                        @php $active = request()->routeIs($portal . '.' . $name) || ($name === 'messages' && request()->routeIs($portal . '.messages.*')); @endphp
                        <a href="{{ route($portal . '.' . $name, $withChild ? $childParam : []) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ $active ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}"/></svg>
                            <span class="flex-1 truncate">{{ $label }}</span>
                            @if ($name === 'messages' && $unreadMessages > 0)
                                <span class="rounded-full bg-gold-500 px-2 py-0.5 text-[11px] font-bold text-navy-800">{{ $unreadMessages }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10 p-3">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                {{ __('Sign Out') }}
            </button>
        </form>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur">
            <div class="flex items-center justify-between gap-4 px-4 py-3 sm:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" @click="nav = true" class="-ml-1 rounded-lg p-2 text-gray-500 hover:bg-gray-100 lg:hidden" aria-label="{{ __('Menu') }}">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="truncate font-heading text-lg font-bold text-navy-700">@yield('title')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('locale.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}"
                       class="rounded-lg border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-500 hover:bg-gray-50">
                        {{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}
                    </a>
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ $student->full_name }} · {{ $student->classroom?->name ?? '-' }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-navy-600 text-sm font-bold text-white">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 space-y-6 p-4 sm:p-6 lg:p-8">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-gray-200 bg-white px-4 py-4 text-center text-xs text-gray-500 sm:px-6">
            &copy; {{ date('Y') }} {{ $tenant?->name }} · {{ config('app.name') }}
        </footer>
    </div>
</div>
@stack('scripts')
</body>
</html>
