<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $tenant->description ?? $tenant->name . ' - ' . __('School Profile') }}">
    <title>{{ $tenant->name }} — {{ __('School Profile') }}</title>

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
                        navy: { 50: '#EEF2F7', 100: '#D4DEE9', 200: '#A9BDD3', 300: '#7E9CBD', 400: '#537BA7', 500: '#2D5F8A', 600: '#1E3A5F', 700: '#172D4A', 800: '#102035', 900: '#091320' },
                        gold: { 50: '#FFFBEB', 100: '#FEF3C7', 200: '#FDE68A', 300: '#FCD34D', 400: '#FBBF24', 500: '#F59E0B', 600: '#D97706', 700: '#B45309', 800: '#92400E', 900: '#78350F' },
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
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-navy { background: linear-gradient(135deg, #1E3A5F 0%, #0F2440 100%); }
        .gradient-gold { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
        .glass { background: rgba(255,255,255,0.06); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); }
        .animate-fade-up { animation: fadeUp .6s ease-out both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 font-body antialiased" x-data="{ mobileMenu: false }">

    {{-- ═══════════════════ NAVBAR ═══════════════════ --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    @if($tenant->logo)
                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="h-10 w-10 rounded-xl object-cover">
                    @else
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif
                    <span class="text-lg font-heading font-bold text-navy-700">{{ $tenant->name }}</span>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="#hero" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('Home') }}</a>
                    <a href="#about" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('About') }}</a>
                    <a href="#teachers" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('Teachers') }}</a>
                    <a href="#facilities" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('Facilities') }}</a>
                    <a href="#news" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('News') }}</a>
                    <a href="#contact" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-navy-600 rounded-lg hover:bg-navy-50 transition">{{ __('Contact') }}</a>
                    <div class="ml-2 flex items-center gap-1 border-l pl-3 border-gray-200">
                        <a href="{{ route('locale.switch', 'id') }}" class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'id' ? 'bg-navy-600 text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">ID</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-navy-600 text-white' : 'text-gray-500 hover:bg-gray-100' }} transition">EN</a>
                    </div>
                    @if(Route::has('ppdb.index'))
                        <a href="{{ route('ppdb.index') }}" class="ml-3 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gold-500 text-white text-sm font-semibold hover:bg-gold-600 shadow-sm shadow-gold-500/20 transition">
                            {{ __('PPDB Registration') }}
                        </a>
                    @endif
                </div>

                {{-- Mobile toggle --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Nav --}}
        <div x-show="mobileMenu" x-transition class="md:hidden border-t bg-white px-4 pb-4 space-y-1">
            <a href="#hero" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('Home') }}</a>
            <a href="#about" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('About') }}</a>
            <a href="#teachers" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('Teachers') }}</a>
            <a href="#facilities" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('Facilities') }}</a>
            <a href="#news" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('News') }}</a>
            <a href="#contact" @click="mobileMenu=false" class="block px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:bg-navy-50">{{ __('Contact') }}</a>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('locale.switch', 'id') }}" class="px-3 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'id' ? 'bg-navy-600 text-white' : 'text-gray-500 bg-gray-100' }}">ID</a>
                <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1 text-xs font-medium rounded {{ app()->getLocale() === 'en' ? 'bg-navy-600 text-white' : 'text-gray-500 bg-gray-100' }}">EN</a>
            </div>
        </div>
    </nav>

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <section id="hero" class="relative gradient-navy pt-32 pb-20 sm:pt-40 sm:pb-28 overflow-hidden">
        {{-- Decorative blobs --}}
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-72 h-72 bg-navy-400/20 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            @if($tenant->logo)
                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="mx-auto h-24 w-24 rounded-2xl object-cover shadow-xl shadow-black/20 mb-6 ring-4 ring-white/10">
            @endif

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-heading font-extrabold text-white leading-tight mb-4">
                {{ $tenant->name }}
            </h1>

            @if($tenant->school_type)
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-gold-300 text-sm font-medium mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $tenant->school_type->label() }}
                    @if($tenant->accreditation)
                        <span class="text-white/40">•</span>
                        <span>{{ __('Accreditation') }}: {{ $tenant->accreditation }}</span>
                    @endif
                </div>
            @endif

            @if($tenant->description)
                <p class="max-w-2xl mx-auto text-lg text-white/70 mb-8">{{ $tenant->description }}</p>
            @else
                <p class="max-w-2xl mx-auto text-lg text-white/70 mb-8">{{ __('Building a brighter future through quality education') }}</p>
            @endif

            <div class="flex flex-wrap justify-center gap-4">
                @if(Route::has('ppdb.index'))
                    <a href="{{ route('ppdb.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gold-500 text-white font-bold hover:bg-gold-400 shadow-lg shadow-gold-500/30 transition-all hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        {{ __('Register Now') }}
                    </a>
                @endif
                <a href="#about" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white/10 text-white font-semibold hover:bg-white/20 backdrop-blur-sm border border-white/10 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Learn More') }}
                </a>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="glass rounded-2xl p-5 text-center">
                    <p class="text-3xl font-heading font-extrabold text-gold-400">{{ number_format($stats['students']) }}</p>
                    <p class="text-sm text-white/60 mt-1">{{ __('Active Students') }}</p>
                </div>
                <div class="glass rounded-2xl p-5 text-center">
                    <p class="text-3xl font-heading font-extrabold text-gold-400">{{ number_format($stats['teachers']) }}</p>
                    <p class="text-sm text-white/60 mt-1">{{ __('Teachers') }}</p>
                </div>
                <div class="glass rounded-2xl p-5 text-center">
                    <p class="text-3xl font-heading font-extrabold text-gold-400">{{ number_format($stats['alumni']) }}</p>
                    <p class="text-sm text-white/60 mt-1">{{ __('Alumni') }}</p>
                </div>
                <div class="glass rounded-2xl p-5 text-center">
                    <p class="text-3xl font-heading font-extrabold text-gold-400">{{ number_format($stats['facilities']) }}</p>
                    <p class="text-sm text-white/60 mt-1">{{ __('Facilities') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ ABOUT / VISION / MISSION ═══════════════════ --}}
    <section id="about" class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-navy-50 text-navy-600 text-xs font-semibold uppercase tracking-wider">{{ __('About Our School') }}</span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-heading font-extrabold text-gray-900">
                    {{ __('Vision & Mission') }}
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                {{-- Vision --}}
                <div class="group bg-gradient-to-br from-navy-50 to-white rounded-2xl p-8 border border-navy-100 hover:shadow-xl hover:shadow-navy-500/5 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-navy-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-navy-700 mb-3">{{ __('Vision') }}</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $tenant->vision ?? __('Creating a generation of excellence with character, achievement, and global competitiveness.') }}
                    </p>
                </div>

                {{-- Mission --}}
                <div class="group bg-gradient-to-br from-gold-50 to-white rounded-2xl p-8 border border-gold-100 hover:shadow-xl hover:shadow-gold-500/5 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gold-500 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h3 class="text-xl font-heading font-bold text-gold-700 mb-3">{{ __('Mission') }}</h3>
                    @if($tenant->mission)
                        <div class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $tenant->mission }}</div>
                    @else
                        <ul class="text-gray-600 space-y-2">
                            <li class="flex items-start gap-2"><svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ __('Providing quality and character-based education') }}</li>
                            <li class="flex items-start gap-2"><svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ __('Developing academic and non-academic potential') }}</li>
                            <li class="flex items-start gap-2"><svg class="w-5 h-5 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>{{ __('Building a modern and innovative learning environment') }}</li>
                        </ul>
                    @endif
                </div>
            </div>

            {{-- School Info Cards --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-12">
                @if($tenant->principal_name)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                    <div class="w-10 h-10 rounded-lg bg-navy-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">{{ __('Principal') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $tenant->principal_name }}</p>
                    </div>
                </div>
                @endif
                @if($tenant->npsn)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                    <div class="w-10 h-10 rounded-lg bg-navy-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">{{ __('NPSN') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $tenant->npsn }}</p>
                    </div>
                </div>
                @endif
                @if($tenant->founded_year)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                    <div class="w-10 h-10 rounded-lg bg-navy-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">{{ __('Founded') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $tenant->founded_year }}</p>
                    </div>
                </div>
                @endif
                @if($tenant->accreditation)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-4">
                    <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">{{ __('Accreditation') }}</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $tenant->accreditation }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════ TEACHERS ═══════════════════ --}}
    @if($teachers->isNotEmpty())
    <section id="teachers" class="py-20 sm:py-28 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-navy-50 text-navy-600 text-xs font-semibold uppercase tracking-wider">{{ __('Our Team') }}</span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-heading font-extrabold text-gray-900">{{ __('Teachers & Staff') }}</h2>
                <p class="mt-3 text-gray-500 max-w-xl mx-auto">{{ __('Dedicated educators committed to nurturing the next generation') }}</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($teachers as $teacher)
                <div class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-navy-500/5 transition-all duration-300">
                    <div class="aspect-square bg-gradient-to-br from-navy-100 to-navy-50 flex items-center justify-center overflow-hidden">
                        @if($teacher->photo)
                            <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="w-20 h-20 text-navy-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                    </div>
                    <div class="p-5 text-center">
                        <h3 class="font-heading font-bold text-gray-900">{{ $teacher->full_name }}</h3>
                        @if($teacher->position)
                            <p class="text-sm text-navy-500 font-medium mt-1">{{ $teacher->position }}</p>
                        @endif
                        @if($teacher->education && $teacher->major)
                            <p class="text-xs text-gray-400 mt-1">{{ $teacher->education }} — {{ $teacher->major }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════ FACILITIES ═══════════════════ --}}
    @if($facilities->isNotEmpty())
    <section id="facilities" class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gold-50 text-gold-600 text-xs font-semibold uppercase tracking-wider">{{ __('Infrastructure') }}</span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-heading font-extrabold text-gray-900">{{ __('School Facilities') }}</h2>
                <p class="mt-3 text-gray-500 max-w-xl mx-auto">{{ __('Modern facilities supporting a comfortable and effective learning experience') }}</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($facilities as $facility)
                <div class="group bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:shadow-navy-500/5 transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-navy-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-heading font-bold text-gray-900 mb-1">{{ $facility->name }}</h3>
                    @if($facility->type)
                        <span class="inline-flex px-2 py-0.5 rounded-md bg-navy-50 text-navy-600 text-xs font-medium">{{ $facility->type }}</span>
                    @endif
                    @if($facility->description)
                        <p class="text-sm text-gray-500 mt-3">{{ Str::limit($facility->description, 100) }}</p>
                    @endif
                    @if($facility->capacity)
                        <p class="text-xs text-gray-400 mt-2">{{ __('Capacity') }}: {{ $facility->capacity }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════ NEWS / ANNOUNCEMENTS ═══════════════════ --}}
    @if($announcements->isNotEmpty())
    <section id="news" class="py-20 sm:py-28 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-navy-50 text-navy-600 text-xs font-semibold uppercase tracking-wider">{{ __('Latest Updates') }}</span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-heading font-extrabold text-gray-900">{{ __('News & Announcements') }}</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($announcements as $news)
                <article class="group bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-navy-500/5 transition-all duration-300">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            @if($news->is_pinned)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gold-50 text-gold-600 text-xs font-semibold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ __('Pinned') }}
                                </span>
                            @endif
                            <time class="text-xs text-gray-400">{{ $news->published_at?->translatedFormat('d M Y') }}</time>
                        </div>
                        <h3 class="font-heading font-bold text-gray-900 mb-2 group-hover:text-navy-600 transition-colors">{{ $news->title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ Str::limit(strip_tags($news->content), 120) }}</p>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════ CONTACT ═══════════════════ --}}
    <section id="contact" class="py-20 sm:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-navy-50 text-navy-600 text-xs font-semibold uppercase tracking-wider">{{ __('Get In Touch') }}</span>
                <h2 class="mt-4 text-3xl sm:text-4xl font-heading font-extrabold text-gray-900">{{ __('Contact Information') }}</h2>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="grid sm:grid-cols-2 gap-6">
                    @if($tenant->address)
                    <div class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6">
                        <div class="w-12 h-12 rounded-xl bg-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900 mb-1">{{ __('Address') }}</h4>
                            <p class="text-sm text-gray-500">{{ $tenant->address }}@if($tenant->city), {{ $tenant->city }}@endif @if($tenant->province), {{ $tenant->province }}@endif</p>
                        </div>
                    </div>
                    @endif

                    @if($tenant->phone)
                    <div class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6">
                        <div class="w-12 h-12 rounded-xl bg-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900 mb-1">{{ __('Phone') }}</h4>
                            <p class="text-sm text-gray-500">{{ $tenant->phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($tenant->email)
                    <div class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6">
                        <div class="w-12 h-12 rounded-xl bg-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900 mb-1">{{ __('Email') }}</h4>
                            <p class="text-sm text-gray-500">{{ $tenant->email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($tenant->website)
                    <div class="flex items-start gap-4 bg-gray-50 rounded-2xl p-6">
                        <div class="w-12 h-12 rounded-xl bg-navy-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <h4 class="font-heading font-bold text-gray-900 mb-1">{{ __('Website') }}</h4>
                            <p class="text-sm text-gray-500">{{ $tenant->website }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Social Links --}}
                @if($tenant->social_links && count($tenant->social_links) > 0)
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500 mb-4">{{ __('Follow Us') }}</p>
                    <div class="flex justify-center gap-3">
                        @foreach($tenant->social_links as $platform => $url)
                            @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl bg-navy-50 flex items-center justify-center text-navy-600 hover:bg-navy-600 hover:text-white transition-all">
                                <span class="text-xs font-bold uppercase">{{ Str::substr($platform, 0, 2) }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════ FOOTER ═══════════════════ --}}
    <footer class="gradient-navy py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-white/40 text-sm">
                &copy; {{ date('Y') }} {{ $tenant->name }} — {{ __('Powered by') }} <span class="text-gold-400 font-semibold">EduSaaS</span>
            </p>
        </div>
    </footer>

</body>
</html>
