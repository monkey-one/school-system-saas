<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Siswa') - EduSaaS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-body" x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">
        {{-- Sidebar Overlay (Mobile) --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-navy-600 text-white transform transition-transform duration-300 lg:translate-x-0 lg:static lg:z-auto">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                <div class="w-9 h-9 rounded-lg bg-gold-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <span class="text-lg font-heading font-bold">Edu<span class="text-gold-400">SaaS</span></span>
                    <span class="block text-xs text-white/50">Portal Siswa</span>
                </div>
            </div>

            <nav class="px-4 py-6 space-y-1">
                @php
                    $navItems = [
                        ['route' => 'student.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>', 'label' => 'Dashboard'],
                        ['route' => 'student.attendance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>', 'label' => 'Kehadiran'],
                        ['route' => 'student.grades', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>', 'label' => 'Nilai'],
                        ['route' => 'student.spp', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>', 'label' => 'SPP'],
                        ['route' => 'student.announcements', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>', 'label' => 'Pengumuman'],
                    ];
                @endphp

                @foreach($navItems as $item)
                <a href="{{ route($item['route'] ?? '#') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                          {{ request()->routeIs($item['route'] ?? '') ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </nav>

            <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t border-white/10">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-heading font-bold text-navy-600">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <button class="relative p-2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-navy-600 text-white flex items-center justify-center text-sm font-bold">
                                {{ substr($student->full_name ?? 'S', 0, 1) }}
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-sm font-semibold text-gray-800">{{ $student->full_name ?? 'Siswa' }}</div>
                                <div class="text-xs text-gray-400">{{ $student->classroom->name ?? 'Kelas' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
