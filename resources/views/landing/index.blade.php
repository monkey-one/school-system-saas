<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EduSaaS - {{ __('Modern School Management System for the Digital Era') }}">
    <title>EduSaaS - {{ __('Modern School Management System for the Digital Era') }}</title>

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
                        navy: {
                            50: '#EEF2F7',
                            100: '#D4DEE9',
                            200: '#A9BDD3',
                            300: '#7E9CBD',
                            400: '#537BA7',
                            500: '#2D5F8A',
                            600: '#1E3A5F',
                            700: '#172D4A',
                            800: '#102035',
                            900: '#091320',
                        },
                        gold: {
                            50: '#FFFBEB',
                            100: '#FEF3C7',
                            200: '#FDE68A',
                            300: '#FCD34D',
                            400: '#FBBF24',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309',
                            800: '#92400E',
                            900: '#78350F',
                        },
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
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out 2s infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .feature-card:hover .feature-icon { transform: scale(1.1) rotate(5deg); }
        .pricing-card:hover { transform: translateY(-8px); }
    </style>
</head>
<body class="bg-white text-gray-800 font-body antialiased">

    {{-- ======== NAVBAR ======== --}}
    <nav x-data="{ mobileOpen: false, scrolled: false }"
         x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
         :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-lg' : 'bg-transparent'"
         class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="#" class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl gradient-gold flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-2xl font-heading font-bold" :class="scrolled ? 'text-navy-600' : 'text-white'">Edu<span class="text-gold-500">SaaS</span></span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-navy-600' : 'text-white/80 hover:text-white'">{{ __('Features') }}</a>
                    <a href="#harga" class="font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-navy-600' : 'text-white/80 hover:text-white'">{{ __('Pricing') }}</a>
                    <a href="#faq" class="font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-navy-600' : 'text-white/80 hover:text-white'">{{ __('FAQ') }}</a>
                    <a href="#kontak" class="font-medium transition-colors" :class="scrolled ? 'text-gray-600 hover:text-navy-600' : 'text-white/80 hover:text-white'">{{ __('Contact') }}</a>
                </div>

                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ url('/school/login') }}" class="font-medium transition-colors" :class="scrolled ? 'text-navy-600 hover:text-navy-700' : 'text-white hover:text-gold-400'">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-xl font-semibold text-white gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/25">{{ __('Try Free') }}</a>
                    {{-- Language Switcher --}}
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 font-medium transition-colors px-2 py-1 rounded-lg" :class="scrolled ? 'text-gray-600 hover:text-navy-600' : 'text-white/80 hover:text-white'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-transition class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
                            <a href="{{ route('locale.switch', 'id') }}" class="block px-4 py-2.5 text-sm font-medium {{ app()->getLocale() === 'id' ? 'bg-navy-50 text-navy-600' : 'text-gray-600 hover:bg-gray-50' }}">🇮🇩 Indonesia</a>
                            <a href="{{ route('locale.switch', 'en') }}" class="block px-4 py-2.5 text-sm font-medium {{ app()->getLocale() === 'en' ? 'bg-navy-50 text-navy-600' : 'text-gray-600 hover:bg-gray-50' }}">🇬🇧 English</a>
                        </div>
                    </div>
                </div>

                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2" :class="scrolled ? 'text-navy-600' : 'text-white'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileOpen" x-transition class="md:hidden bg-white rounded-2xl shadow-xl p-6 mb-4 space-y-4">
                <a href="#fitur" @click="mobileOpen = false" class="block text-gray-700 hover:text-navy-600 font-medium">{{ __('Features') }}</a>
                <a href="#harga" @click="mobileOpen = false" class="block text-gray-700 hover:text-navy-600 font-medium">{{ __('Pricing') }}</a>
                <a href="#faq" @click="mobileOpen = false" class="block text-gray-700 hover:text-navy-600 font-medium">{{ __('FAQ') }}</a>
                <a href="#kontak" @click="mobileOpen = false" class="block text-gray-700 hover:text-navy-600 font-medium">{{ __('Contact') }}</a>
                <hr class="border-gray-200">
                <a href="{{ url('/school/login') }}" class="block text-navy-600 font-medium">{{ __('Sign In') }}</a>
                <a href="{{ route('register') }}" class="block text-center px-6 py-3 rounded-xl font-semibold text-white gradient-gold">{{ __('Try Free') }}</a>
                <div class="flex gap-2">
                    <a href="{{ route('locale.switch', 'id') }}" class="flex-1 text-center py-2 rounded-lg text-sm font-medium {{ app()->getLocale() === 'id' ? 'bg-navy-600 text-white' : 'bg-gray-100 text-gray-600' }}">🇮🇩 ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="flex-1 text-center py-2 rounded-lg text-sm font-medium {{ app()->getLocale() === 'en' ? 'bg-navy-600 text-white' : 'bg-gray-100 text-gray-600' }}">🇬🇧 EN</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ======== HERO ======== --}}
    <section class="relative gradient-navy overflow-hidden min-h-screen flex items-center">
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-72 h-72 bg-gold-500/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-navy-500/30 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 lg:py-40">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-8 border border-white/20">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-white/80 text-sm font-medium">{{ __('#1 Platform for School Management') }}</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-heading font-extrabold text-white leading-tight mb-6">
                        {{ __('Modern School Management System for the Digital Era') }}
                    </h1>
                    <p class="text-lg text-white/70 mb-10 leading-relaxed max-w-lg">
                        {{ __('Manage all your school operations in one integrated platform. From student admissions, attendance, report cards, to tuition payments — everything becomes easier.') }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl font-bold text-white gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/30 text-lg">
                            {{ __('Start Free') }}
                        </a>
                        <a href="#demo" class="group px-8 py-4 rounded-xl font-bold text-white border-2 border-white/20 hover:border-white/40 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            {{ __('Watch Demo') }}
                        </a>
                    </div>
                </div>

                {{-- Hero Illustration --}}
                <div class="hidden lg:block relative">
                    <div class="relative w-full aspect-square max-w-lg mx-auto">
                        <div class="absolute inset-0 bg-gradient-to-br from-gold-400/20 to-blue-400/20 rounded-3xl transform rotate-6"></div>
                        <div class="absolute inset-4 glass rounded-2xl p-8 flex flex-col justify-center items-center gap-6">
                            <div class="grid grid-cols-2 gap-4 w-full">
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-heading font-bold text-gold-400">500+</div>
                                    <div class="text-white/60 text-sm">{{ __('Schools') }}</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-heading font-bold text-blue-400">50K+</div>
                                    <div class="text-white/60 text-sm">{{ __('Students') }}</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-heading font-bold text-green-400">5K+</div>
                                    <div class="text-white/60 text-sm">{{ __('Teachers') }}</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-4 text-center">
                                    <div class="text-3xl font-heading font-bold text-purple-400">99.9%</div>
                                    <div class="text-white/60 text-sm">{{ __('Uptime') }}</div>
                                </div>
                            </div>
                            <div class="w-full bg-white/10 rounded-xl p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-8 h-8 bg-gold-500/30 rounded-lg"></div>
                                    <div class="flex-1">
                                        <div class="h-2.5 bg-white/20 rounded w-3/4"></div>
                                        <div class="h-2 bg-white/10 rounded w-1/2 mt-1.5"></div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="h-8 bg-green-500/30 rounded flex-1"></div>
                                    <div class="h-8 bg-gold-500/30 rounded w-1/4"></div>
                                    <div class="h-8 bg-blue-500/30 rounded w-1/6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 100" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 50L48 45.7C96 41.3 192 32.7 288 30.8C384 29 480 34 576 42.2C672 50.3 768 61.7 864 63.5C960 65.3 1056 57.7 1152 52.8C1248 48 1344 46 1392 45L1440 44V101H1392C1344 101 1248 101 1152 101C1056 101 960 101 864 101C768 101 672 101 576 101C480 101 384 101 288 101C192 101 96 101 48 101H0V50Z" fill="white"/></svg>
        </div>
    </section>

    {{-- ======== STATS BAR ======== --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-navy-600 to-navy-700 rounded-2xl p-8 shadow-xl shadow-navy-600/20">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl sm:text-4xl font-heading font-extrabold text-gold-400">500+</div>
                        <div class="text-white/70 mt-1 font-medium">{{ __('Schools') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl sm:text-4xl font-heading font-extrabold text-gold-400">50.000+</div>
                        <div class="text-white/70 mt-1 font-medium">{{ __('Students') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl sm:text-4xl font-heading font-extrabold text-gold-400">5.000+</div>
                        <div class="text-white/70 mt-1 font-medium">{{ __('Teachers') }}</div>
                    </div>
                    <div>
                        <div class="text-3xl sm:text-4xl font-heading font-extrabold text-gold-400">99.9%</div>
                        <div class="text-white/70 mt-1 font-medium">{{ __('Uptime') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======== FEATURES ======== --}}
    <section id="fitur" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-gold-100 text-gold-700 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('Complete Features') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Everything Your School Needs') }}</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ __('An integrated platform with modern features to manage school operations efficiently.') }}</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $features = [
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>', 'title' => __('Online PPDB'), 'desc' => __('Online student admission with complete forms and status tracking.'), 'color' => 'blue'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>', 'title' => __('QR Code Attendance'), 'desc' => __('Digital attendance using QR code with automatic notifications to parents.'), 'color' => 'green'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'title' => __('Digital Report Card'), 'desc' => __('Auto-generate report cards in Kurikulum Merdeka or K13 format.'), 'color' => 'purple'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>', 'title' => __('Online Tuition'), 'desc' => __('Tuition payment integrated with payment gateway and automatic reminders.'), 'color' => 'gold'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>', 'title' => __('WhatsApp Notifications'), 'desc' => __('Automatic notifications to parents via WhatsApp for attendance and information.'), 'color' => 'green'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>', 'title' => __('Parent Portal'), 'desc' => __("Dedicated portal for parents to monitor their child's progress in real-time."), 'color' => 'red'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>', 'title' => __('Library Management'), 'desc' => __('Book management, borrowing, and returns with barcode scanning.'), 'color' => 'indigo'],
                        ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>', 'title' => __('Inventory Management'), 'desc' => __('Complete school asset and inventory tracking with condition monitoring.'), 'color' => 'cyan'],
                    ];
                    $colorMap = [
                        'blue' => 'bg-blue-100 text-blue-600',
                        'green' => 'bg-green-100 text-green-600',
                        'purple' => 'bg-purple-100 text-purple-600',
                        'gold' => 'bg-gold-100 text-gold-600',
                        'red' => 'bg-red-100 text-red-600',
                        'indigo' => 'bg-indigo-100 text-indigo-600',
                        'cyan' => 'bg-cyan-100 text-cyan-600',
                    ];
                @endphp

                @foreach($features as $feature)
                <div class="feature-card group bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                    <div class="feature-icon w-14 h-14 rounded-xl {{ $colorMap[$feature['color']] }} flex items-center justify-center mb-5 transition-transform duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $feature['icon'] !!}</svg>
                    </div>
                    <h3 class="text-lg font-heading font-bold text-navy-600 mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======== HOW IT WORKS ======== --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-navy-100 text-navy-600 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('Easy to Start') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Start in 3 Steps') }}</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ __('No technical expertise needed. Your school can be up and running in minutes.') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <div class="hidden md:block absolute top-8 left-[20%] right-[20%] h-0.5 bg-gradient-to-r from-gold-400 via-gold-300 to-gold-400"></div>

                @php
                    $steps = [
                        ['num' => '1', 'title' => __('Register'), 'desc' => __('Create a free account and fill in your school profile. No credit card needed.')],
                        ['num' => '2', 'title' => __('Configure'), 'desc' => __('Set up academic year, classes, subjects, and teacher and student data.')],
                        ['num' => '3', 'title' => __('Use'), 'desc' => __('Start managing your school digitally. Our team is ready to help 24/7.')],
                    ];
                @endphp

                @foreach($steps as $step)
                <div class="relative text-center">
                    <div class="w-16 h-16 gradient-gold rounded-2xl flex items-center justify-center mx-auto mb-6 text-white text-2xl font-heading font-extrabold shadow-lg shadow-gold-500/30 relative z-10">
                        {{ $step['num'] }}
                    </div>
                    <h3 class="text-xl font-heading font-bold text-navy-600 mb-3">{{ $step['title'] }}</h3>
                    <p class="text-gray-500 max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======== PRICING ======== --}}
    <section id="harga" class="py-24 bg-gray-50" x-data="{ annual: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-gold-100 text-gold-700 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('Transparent Pricing') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Choose the Right Plan') }}</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto mb-8">{{ __('All plans include a 14-day free trial. No hidden fees.') }}</p>

                <div class="inline-flex items-center gap-4 bg-white rounded-full p-1.5 shadow-sm border border-gray-200">
                    <button @click="annual = false" :class="!annual ? 'bg-navy-600 text-white shadow-md' : 'text-gray-500'" class="px-6 py-2.5 rounded-full font-semibold transition-all text-sm">{{ __('Monthly') }}</button>
                    <button @click="annual = true" :class="annual ? 'bg-navy-600 text-white shadow-md' : 'text-gray-500'" class="px-6 py-2.5 rounded-full font-semibold transition-all text-sm">
                        {{ __('Annual') }} <span class="text-xs ml-1 text-gold-500 font-bold">-20%</span>
                    </button>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Starter --}}
                <div class="pricing-card bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-heading font-bold text-navy-600 mb-2">{{ __('Starter') }}</h3>
                        <p class="text-gray-400 text-sm">{{ __('For small schools') }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-heading font-extrabold text-navy-600">Rp <span x-text="annual ? '400.000' : '500.000'"></span></span>
                        </div>
                        <span class="text-gray-400 text-sm">{{ __('/month') }}</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Max. 200 students') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Online PPDB') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('QR Code Attendance') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Digital Report Card') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Online Tuition') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-400"><svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>{{ __('WhatsApp Notifications') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-400"><svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>{{ __('Parent Portal') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3.5 rounded-xl font-semibold border-2 border-navy-600 text-navy-600 hover:bg-navy-600 hover:text-white transition-all">{{ __('Start Free') }}</a>
                </div>

                {{-- Professional --}}
                <div class="pricing-card bg-navy-600 rounded-2xl p-8 shadow-xl shadow-navy-600/20 border-2 border-gold-400 relative transition-all duration-300 transform scale-[1.02]">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gold-500 text-white text-xs font-bold px-4 py-1.5 rounded-full">{{ __('Popular') }}</div>
                    <div class="mb-6">
                        <h3 class="text-xl font-heading font-bold text-white mb-2">{{ __('Professional') }}</h3>
                        <p class="text-white/60 text-sm">{{ __('For medium schools') }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-heading font-extrabold text-white">Rp <span x-text="annual ? '800.000' : '1.000.000'"></span></span>
                        </div>
                        <span class="text-white/50 text-sm">{{ __('/month') }}</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Max. 1,000 students') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('All Starter features') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('WhatsApp Notifications') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Parent Portal') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Library Management') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Inventory Management') }}</li>
                        <li class="flex items-center gap-3 text-sm text-white/90"><svg class="w-5 h-5 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Priority Support') }}</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3.5 rounded-xl font-bold gradient-gold text-white hover:opacity-90 transition-all shadow-lg shadow-gold-500/30">{{ __('Start Free') }}</a>
                </div>

                {{-- Enterprise --}}
                <div class="pricing-card bg-white rounded-2xl p-8 shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-heading font-bold text-navy-600 mb-2">{{ __('Enterprise') }}</h3>
                        <p class="text-gray-400 text-sm">{{ __('For foundations & large schools') }}</p>
                    </div>
                    <div class="mb-8">
                        <div class="flex items-baseline gap-1">
                            <span class="text-4xl font-heading font-extrabold text-navy-600">Rp <span x-text="annual ? '1.600.000' : '2.000.000'"></span></span>
                        </div>
                        <span class="text-gray-400 text-sm">{{ __('/month') }}</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Unlimited Students') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('All Professional features') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Multi-branch') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('API Access') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Custom Branding') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>{{ __('Dedicated Support') }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>SLA 99.99%</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block text-center w-full py-3.5 rounded-xl font-semibold border-2 border-navy-600 text-navy-600 hover:bg-navy-600 hover:text-white transition-all">{{ __('Contact Us') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ======== TESTIMONIALS ======== --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-gold-100 text-gold-700 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('Testimonials') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Trusted by Hundreds of Schools') }}</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $testimonials = [
                        [
                            'quote' => 'EduSaaS mengubah cara kami mengelola sekolah. Proses SPP yang dulu memusingkan, sekarang berjalan otomatis. Orang tua pun sangat terbantu dengan portal monitoring anak.',
                            'name' => 'Dr. Hj. Siti Aminah, M.Pd',
                            'school' => 'SMA Negeri 1 Surabaya',
                            'initial' => 'SA',
                        ],
                        [
                            'quote' => 'Fitur PPDB online sangat membantu kami mengelola ribuan pendaftar tanpa kerepotan. Sistemnya user-friendly dan tim support sangat responsif.',
                            'name' => 'Budi Santoso, S.Pd',
                            'school' => 'SMP Islam Terpadu Al-Hikmah',
                            'initial' => 'BS',
                        ],
                        [
                            'quote' => 'Sebagai yayasan dengan 5 cabang sekolah, EduSaaS membantu kami memantau seluruh operasional dari satu dashboard. Hemat waktu dan tenaga!',
                            'name' => 'H. Ahmad Fauzi, M.M',
                            'school' => 'Yayasan Pendidikan Nusantara',
                            'initial' => 'AF',
                        ],
                    ];
                @endphp

                @foreach($testimonials as $t)
                <div class="bg-gray-50 rounded-2xl p-8 border border-gray-100 hover:shadow-lg transition-all duration-300">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-gold-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed italic">"{{ $t['quote'] }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full gradient-navy flex items-center justify-center text-white font-bold text-sm">{{ $t['initial'] }}</div>
                        <div>
                            <div class="font-semibold text-navy-600">{{ $t['name'] }}</div>
                            <div class="text-gray-400 text-sm">{{ $t['school'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======== DEMO ACCESS ======== --}}
    <section id="demo" class="py-24 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-green-100 text-green-700 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('Live Demo') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Try Demo Now') }}</h2>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ __('Access our demo with 3 different user roles to explore the features of EduSaaS') }}</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Super Admin --}}
                <div class="rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-heading font-bold text-navy-600 text-lg mb-2">Super Admin</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ __('Manage all schools, users, subscriptions, and system settings.') }}</p>
                    <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs space-y-1">
                        <p><span class="font-medium text-gray-700">Email:</span> <code class="text-navy-600">superadmin@edusaas.id</code></p>
                        <p><span class="font-medium text-gray-700">Password:</span> <code class="text-navy-600">password</code></p>
                    </div>
                    <a href="{{ url('/super-admin/login') }}" class="block w-full text-center py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold text-sm transition-colors">
                        {{ __('Login as Super Admin') }} →
                    </a>
                </div>

                {{-- School Admin --}}
                <div class="rounded-2xl border-2 border-navy-500 p-6 shadow-lg relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-navy-600 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Recommended') }}</div>
                    <div class="w-12 h-12 rounded-xl bg-navy-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="font-heading font-bold text-navy-600 text-lg mb-2">School Admin</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ __('Full access to manage school data, students, teachers, finances, and more.') }}</p>
                    <div class="bg-navy-50 rounded-lg p-3 mb-4 text-xs space-y-1">
                        <p><span class="font-medium text-gray-700">Email:</span> <code class="text-navy-600">admin@smpn1demo.id</code></p>
                        <p><span class="font-medium text-gray-700">Password:</span> <code class="text-navy-600">password</code></p>
                    </div>
                    <a href="{{ url('/school/login') }}" class="block w-full text-center py-2.5 rounded-xl bg-navy-600 hover:bg-navy-700 text-white font-semibold text-sm transition-colors">
                        {{ __('Login as School Admin') }} →
                    </a>
                </div>

                {{-- Teacher --}}
                <div class="rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h3 class="font-heading font-bold text-navy-600 text-lg mb-2">Teacher</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ __('View schedules, manage attendance, and input student grades.') }}</p>
                    <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs space-y-1">
                        <p><span class="font-medium text-gray-700">Email:</span> <code class="text-navy-600">hadi.santoso@smpn1demo.id</code></p>
                        <p><span class="font-medium text-gray-700">Password:</span> <code class="text-navy-600">password</code></p>
                    </div>
                    <a href="{{ url('/teacher/login') }}" class="block w-full text-center py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors">
                        {{ __('Login as Teacher') }} →
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ======== FAQ ======== --}}
    <section id="faq" class="py-24 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block bg-navy-100 text-navy-600 font-semibold text-sm px-4 py-1.5 rounded-full mb-4">{{ __('FAQ') }}</span>
                <h2 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Frequently Asked Questions') }}</h2>
            </div>

            <div class="space-y-4" x-data="{ openFaq: null }">
                @php
                    $faqs = [
                        ['q' => __('Is there an initial setup fee?'), 'a' => __('No! You can register and use our platform right away without any setup fee. All plans include a free 14-day trial.')],
                        ['q' => __('How secure is our school data?'), 'a' => __('Your data is encrypted end-to-end and stored on ISO 27001 certified servers. We perform automatic daily backups and have a disaster recovery plan.')],
                        ['q' => __('Can we import data from the old system?'), 'a' => __('Of course! We provide data import via Excel/CSV files. Our team is also ready to help with data migration from your old system.')],
                        ['q' => __('How long is the implementation process?'), 'a' => __('For Starter and Professional plans, schools can be operational in 1-3 days. For Enterprise with customization, it usually takes 1-2 weeks.')],
                        ['q' => __('Is there training for teachers and staff?'), 'a' => __('Yes! We provide free online training through video tutorials and webinars. For Enterprise plans, we provide on-site training.')],
                        ['q' => __('What if we want to unsubscribe?'), 'a' => __('You can cancel anytime without penalty. Your data will be stored for 30 days and can be exported before deletion.')],
                    ];
                @endphp

                @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <button @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-navy-600">{{ $faq['q'] }}</span>
                        <svg :class="openFaq === {{ $index }} ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transform transition-transform flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openFaq === {{ $index }}" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="px-6 pb-5">
                        <p class="text-gray-500 leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======== CTA ======== --}}
    <section id="kontak" class="py-24 gradient-navy relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-1/4 w-64 h-64 bg-gold-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-heading font-extrabold text-white mb-6">{{ __('Start Managing Your School Now') }}</h2>
            <p class="text-white/60 text-lg mb-10 max-w-2xl mx-auto">{{ __('Join 500+ schools that have trusted EduSaaS with their school management.') }}</p>

            <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto justify-center">
                <a href="{{ route('register') }}" class="px-10 py-4 rounded-xl font-bold text-white gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/30 whitespace-nowrap text-lg">
                    {{ __('Register Now') }}
                </a>
            </div>
            <p class="text-white/40 text-sm mt-4">{{ __('Free 14 days. No credit card.') }}</p>
        </div>
    </section>

    {{-- ======== FOOTER ======== --}}
    <footer class="bg-navy-800 text-white/60 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-5 gap-8 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-10 h-10 rounded-xl gradient-gold flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-xl font-heading font-bold text-white">Edu<span class="text-gold-500">SaaS</span></span>
                    </div>
                    <p class="text-sm leading-relaxed max-w-sm">{{ __('Modern School Management System for the Digital Era. Manage all school operations in one integrated platform.') }}</p>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12.017 24c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641 0 12.017 0z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.48 6.34 6.34 0 001.82-4.48V8.73a8.19 8.19 0 004.76 1.52V6.79a4.85 4.85 0 01-1-.1z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-heading font-semibold mb-4">{{ __('Product') }}</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#fitur" class="hover:text-white transition-colors">{{ __('Features') }}</a></li>
                        <li><a href="#harga" class="hover:text-white transition-colors">{{ __('Pricing') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Changelog') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('API Docs') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-heading font-semibold mb-4">{{ __('Company') }}</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('About Us') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Blog') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Careers') }}</a></li>
                        <li><a href="#kontak" class="hover:text-white transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-heading font-semibold mb-4">{{ __('Legal') }}</h4>
                    <ul class="space-y-3 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Privacy Policy') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">{{ __('Terms & Conditions') }}</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">SLA</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 text-center text-sm">
                <p>&copy; {{ date('Y') }} EduSaaS. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

</body>
</html>
