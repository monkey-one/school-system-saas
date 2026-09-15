@php
    // Keep ?tenant= on every link when the school was chosen that way.
    $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : [];
    $settings = $tenant->settings ?? [];
    $logo = $tenant->logo ? asset('storage/' . $tenant->logo) : null;
    $pageTitle = trim($__env->yieldContent('title'));
    $description = trim($__env->yieldContent('description')) ?: \Illuminate\Support\Str::limit(strip_tags((string) $tenant->description), 160);
    $nav = [
        ['website.home', __('Home')],
        ['website.about', __('Profile')],
        ['website.news', __('News')],
        ['website.agenda', __('Agenda')],
        ['website.achievements', __('Achievements')],
        ['website.gallery', __('Gallery')],
        ['website.teachers', __('Teachers & Staff')],
        ['alumni.index', __('Alumni')],
        ['website.contact', __('Contact')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ? $pageTitle . ' · ' : '' }}{{ $tenant->name }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $tenant->name }}">
    <meta property="og:title" content="{{ $pageTitle ?: $tenant->name }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->full() }}">
    @hasSection('og_image')<meta property="og:image" content="@yield('og_image')">@elseif ($logo)<meta property="og:image" content="{{ $logo }}">@endif
    <link rel="icon" href="{{ $logo ?? asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=typography,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50: '#EEF2F7', 100: '#D4DEE9', 500: '#2D5F8A', 600: '#1E3A5F', 700: '#172D4A', 800: '#102035', 900: '#0B1627' },
                        gold: { 50: '#FFFBEB', 100: '#FEF3C7', 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706' },
                    },
                    fontFamily: { heading: ['"Plus Jakarta Sans"', 'sans-serif'], body: ['Inter', 'sans-serif'] },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'School',
            'name' => $tenant->name,
            'url' => route('website.home', $tq),
            'telephone' => $tenant->phone,
            'email' => $tenant->email,
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => $tenant->address, 'addressLocality' => $tenant->city, 'addressRegion' => $tenant->province, 'addressCountry' => 'ID'],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-white font-body text-gray-800 antialiased">
<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-100 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('website.home', $tq) }}" class="flex min-w-0 items-center gap-3">
            @if ($logo)
                <img src="{{ $logo }}" alt="{{ $tenant->name }}" class="h-11 w-11 rounded-lg object-contain">
            @else
                <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-navy-600 font-heading text-lg font-bold text-gold-400">{{ mb_substr($tenant->name, 0, 1) }}</span>
            @endif
            <span class="min-w-0">
                <span class="block truncate font-heading font-bold text-navy-700">{{ $tenant->name }}</span>
                <span class="block truncate text-xs text-gray-500">{{ $tenant->city }}@if ($tenant->accreditation) · {{ __('Accreditation') }} {{ $tenant->accreditation }}@endif</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 xl:flex">
            @foreach ($nav as [$route, $label])
                <a href="{{ route($route, $tq) }}" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs($route . '*') ? 'bg-navy-50 text-navy-700' : 'text-gray-600 hover:bg-gray-50 hover:text-navy-700' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('locale.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="hidden rounded-lg border border-gray-200 px-2 py-1 text-xs font-semibold text-gray-500 hover:bg-gray-50 sm:inline-block">{{ app()->getLocale() === 'id' ? 'EN' : 'ID' }}</a>
            <a href="{{ route('ppdb.index', $tq) }}" class="hidden rounded-lg bg-gold-500 px-4 py-2 text-sm font-bold text-navy-800 hover:bg-gold-400 sm:inline-block">{{ __('PPDB Online') }}</a>
            <a href="{{ url('/edusaas-admin/login') }}" class="hidden rounded-lg border border-navy-600 px-4 py-2 text-sm font-semibold text-navy-700 hover:bg-navy-50 md:inline-block">{{ __('Login') }}</a>
            <button type="button" @click="open = !open" class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 xl:hidden" aria-label="{{ __('Menu') }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <nav x-show="open" x-cloak class="border-t border-gray-100 px-4 py-3 xl:hidden">
        <div class="grid grid-cols-2 gap-1">
            @foreach ($nav as [$route, $label])
                <a href="{{ route($route, $tq) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">{{ $label }}</a>
            @endforeach
            <a href="{{ route('ppdb.index', $tq) }}" class="rounded-lg bg-gold-500 px-3 py-2 text-sm font-bold text-navy-800">{{ __('PPDB Online') }}</a>
            <a href="{{ url('/edusaas-admin/login') }}" class="rounded-lg border border-navy-600 px-3 py-2 text-sm font-semibold text-navy-700">{{ __('Login') }}</a>
        </div>
    </nav>
</header>

<main class="flex-1">
    @yield('content')
</main>

<footer class="bg-navy-900 text-white/80">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3">
        <div>
            <p class="font-heading text-lg font-bold text-white">{{ $tenant->name }}</p>
            @if ($tenant->npsn)<p class="mt-1 text-sm">NPSN {{ $tenant->npsn }}</p>@endif
            <p class="mt-3 text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit(strip_tags((string) $tenant->description), 180) }}</p>
        </div>
        <div>
            <p class="font-heading font-bold text-white">{{ __('Contact') }}</p>
            <ul class="mt-3 space-y-2 text-sm">
                @if ($tenant->address)<li>{{ $tenant->address }}, {{ $tenant->city }}</li>@endif
                @if ($tenant->phone)<li>{{ __('Phone') }}: {{ $tenant->phone }}</li>@endif
                @if ($tenant->email)<li>Email: <a href="mailto:{{ $tenant->email }}" class="hover:text-gold-400">{{ $tenant->email }}</a></li>@endif
            </ul>
            <div class="mt-4 flex flex-wrap gap-3 text-sm">
                @foreach (($tenant->social_links ?? []) as $network => $link)
                    @if (filled($link) && str_starts_with($link, 'https://'))
                        <a href="{{ $link }}" target="_blank" rel="noopener nofollow" class="rounded-lg bg-white/10 px-3 py-1 capitalize hover:bg-white/20">{{ $network }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <div>
            <p class="font-heading font-bold text-white">{{ __('Quick links') }}</p>
            <ul class="mt-3 grid grid-cols-2 gap-2 text-sm">
                @foreach ($nav as [$route, $label])
                    <li><a href="{{ route($route, $tq) }}" class="hover:text-gold-400">{{ $label }}</a></li>
                @endforeach
                <li><a href="{{ route('ppdb.index', $tq) }}" class="hover:text-gold-400">{{ __('PPDB Online') }}</a></li>
                <li><a href="{{ route('ppdb.status', $tq) }}" class="hover:text-gold-400">{{ __('PPDB status') }}</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 px-4 py-4 text-center text-xs text-white/60">
        &copy; {{ date('Y') }} {{ $tenant->name }}. {{ __('All rights reserved.') }}
        @include('partials.author-credit')
    </div>
</footer>
@include('partials.demo-cta')
@stack('scripts')
</body>
</html>
