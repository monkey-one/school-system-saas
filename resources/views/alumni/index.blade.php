<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Alumni Directory') }} — {{ $tenant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 50:'#E8F0FB',100:'#D1E1F7',200:'#A3C3EF',300:'#75A5E7',400:'#4A7AAF',500:'#3B6494',600:'#2D4E7A',700:'#1E3A5F',800:'#0F2A47',900:'#091D33',950:'#051120' },
                        gold: { 50:'#FFFBEB',100:'#FEF3C7',200:'#FDE68A',300:'#FCD34D',400:'#FBBF24',500:'#F59E0B',600:'#D97706',700:'#B45309' },
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-gray-50 text-gray-800" x-data="{ mobileMenu: false }">

    {{-- Navbar --}}
    <nav class="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" class="w-10 h-10 rounded-lg object-cover" alt="{{ $tenant->name }}">
                @else
                    <div class="w-10 h-10 rounded-lg bg-navy-700 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                @endif
                <div>
                    <h1 class="text-lg font-bold text-navy-800">{{ $tenant->name }}</h1>
                    <p class="text-xs text-gray-500">{{ __('Alumni Directory') }}</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('profile.index', ['tenant' => $tenant->slug]) }}" class="text-sm font-medium text-gray-600 hover:text-navy-700 transition">{{ __('School Profile') }}</a>
                <a href="#directory" class="text-sm font-medium text-gray-600 hover:text-navy-700 transition">{{ __('Directory') }}</a>
                <a href="#testimonials" class="text-sm font-medium text-gray-600 hover:text-navy-700 transition">{{ __('Testimonials') }}</a>
                @include('components.language-switcher-public')
            </div>
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div x-show="mobileMenu" x-transition class="md:hidden bg-white border-t px-4 pb-4 space-y-2">
            <a href="{{ route('profile.index', ['tenant' => $tenant->slug]) }}" class="block py-2 text-sm font-medium text-gray-600">{{ __('School Profile') }}</a>
            <a href="#directory" @click="mobileMenu=false" class="block py-2 text-sm font-medium text-gray-600">{{ __('Directory') }}</a>
            <a href="#testimonials" @click="mobileMenu=false" class="block py-2 text-sm font-medium text-gray-600">{{ __('Testimonials') }}</a>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative bg-gradient-to-br from-navy-800 via-navy-700 to-navy-900 text-white py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(#grid)"/></svg>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-gold-500/20 border border-gold-500/30 rounded-full px-4 py-1.5 mb-6">
                <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                <span class="text-sm text-gold-300 font-medium">{{ __('Alumni Network') }}</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">{{ __('Alumni Directory') }}</h1>
            <p class="text-lg text-gray-300 max-w-2xl mx-auto mb-10">{{ __('Connecting graduates and building a strong alumni community') }}</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-3xl font-bold text-gold-400">{{ number_format($stats['total_alumni']) }}</p>
                    <p class="text-sm text-gray-300">{{ __('Total Alumni') }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-3xl font-bold text-gold-400">{{ number_format($stats['verified']) }}</p>
                    <p class="text-sm text-gray-300">{{ __('Verified') }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-3xl font-bold text-gold-400">{{ number_format($stats['pursuing_education']) }}</p>
                    <p class="text-sm text-gray-300">{{ __('Higher Education') }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4">
                    <p class="text-3xl font-bold text-gold-400">{{ number_format($stats['employed']) }}</p>
                    <p class="text-sm text-gray-300">{{ __('Employed') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Directory --}}
    <section id="directory" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-navy-800 mb-2">{{ __('Alumni Directory') }}</h2>
                <p class="text-gray-500">{{ __('Find and connect with fellow graduates') }}</p>
            </div>

            {{-- Year Filter --}}
            @if(count($graduationYears) > 0)
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <a href="{{ route('alumni.index', ['tenant' => $tenant->slug]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ !$selectedYear ? 'bg-navy-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ __('All Years') }}
                </a>
                @foreach($graduationYears as $year)
                <a href="{{ route('alumni.index', ['tenant' => $tenant->slug, 'year' => $year]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ $selectedYear == $year ? 'bg-navy-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ $year }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Alumni Grid --}}
            @if($alumni->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($alumni as $profile)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all group overflow-hidden">
                    <div class="h-2 bg-gradient-to-r from-navy-600 to-gold-500"></div>
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-navy-100 to-navy-200 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold text-navy-700">{{ substr($profile->student?->full_name ?? '?', 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-navy-800 truncate">{{ $profile->student?->full_name ?? '-' }}</h3>
                                <p class="text-xs text-gray-500">{{ $profile->alumni_number }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            @if($profile->graduated_at)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                <span>{{ __('Graduated') }} {{ $profile->graduated_at->format('Y') }}</span>
                            </div>
                            @endif
                            @if($profile->higher_education)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span class="truncate">{{ $profile->higher_education }}</span>
                            </div>
                            @endif
                            @if($profile->major)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span class="truncate">{{ $profile->major }}</span>
                            </div>
                            @endif
                            @if($profile->current_occupation)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="truncate">{{ $profile->current_occupation }}{{ $profile->current_company ? ' — ' . $profile->current_company : '' }}</span>
                            </div>
                            @endif
                            @if($profile->current_city)
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ $profile->current_city }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-10">{{ $alumni->withQueryString()->links() }}</div>
            @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                <p class="text-gray-500 text-lg">{{ __('No alumni data available yet') }}</p>
            </div>
            @endif
        </div>
    </section>

    {{-- Testimonials --}}
    @if($testimonials->count() > 0)
    <section id="testimonials" class="py-16 bg-navy-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-navy-800 mb-2">{{ __('Alumni Testimonials') }}</h2>
                <p class="text-gray-500">{{ __('Hear from our graduates') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $t)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-white">{{ substr($t->student?->full_name ?? '?', 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-800 text-sm">{{ $t->student?->full_name }}</h4>
                            <p class="text-xs text-gray-500">{{ $t->graduated_at?->format('Y') }}{{ $t->higher_education ? ' • ' . $t->higher_education : '' }}</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm italic leading-relaxed">"{{ Str::limit($t->testimonial, 200) }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Footer --}}
    <footer class="bg-navy-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} {{ $tenant->name }} — {{ __('Powered by') }} <span class="text-gold-400 font-semibold">EduSaaS</span></p>
        </div>
    </footer>
</body>
</html>
