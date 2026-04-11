<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Trial Activated') }} - EduSaaS</title>
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
                        navy: { 50:'#EEF2F7',100:'#D4DEE9',200:'#A9BDD3',300:'#7E9CBD',400:'#537BA7',500:'#2D5F8A',600:'#1E3A5F',700:'#172D4A',800:'#102035',900:'#091320' },
                        gold: { 50:'#FFFBEB',100:'#FEF3C7',200:'#FDE68A',300:'#FCD34D',400:'#FBBF24',500:'#F59E0B',600:'#D97706',700:'#B45309',800:'#92400E',900:'#78350F' },
                    },
                    fontFamily: { heading: ['"Plus Jakarta Sans"','sans-serif'], body: ['Inter','sans-serif'] },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-navy { background: linear-gradient(135deg, #1E3A5F 0%, #0F2440 100%); }
        .gradient-gold { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); }
    </style>
</head>
<body class="bg-gray-50 font-body antialiased min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="gradient-navy">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-2xl font-heading font-bold text-white">Edu<span class="text-gold-500">SaaS</span></span>
                </a>
                <x-language-switcher-public />
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 flex-1 flex items-center">
        <div class="w-full text-center">
            {{-- Success Icon --}}
            <div class="mx-auto w-24 h-24 rounded-full bg-green-100 flex items-center justify-center mb-8">
                <svg class="w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-3xl sm:text-4xl font-heading font-extrabold text-navy-600 mb-4">{{ __('Free Trial Activated!') }}</h1>
            <p class="text-lg text-gray-600 mb-2">{{ __('Your school has been registered successfully.') }}</p>
            <p class="text-gray-500 mb-8 max-w-lg mx-auto">
                {{ __('Your 14-day free trial is now active. Explore all features and set up your school.') }}
            </p>

            {{-- School Info --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 text-left max-w-md mx-auto mb-8">
                <h3 class="font-heading font-bold text-navy-600 mb-4">{{ __('Your School') }}</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('School Name') }}</span>
                        <span class="font-semibold text-navy-600">{{ $tenant->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Status') }}</span>
                        <span class="font-semibold text-green-600">{{ __('Free Trial') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Trial Ends') }}</span>
                        <span class="font-semibold text-navy-600">{{ $tenant->trial_ends_at?->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Next Steps --}}
            <div class="bg-navy-50 rounded-xl border border-navy-200 p-6 text-left max-w-md mx-auto mb-8">
                <h3 class="font-heading font-bold text-navy-600 mb-3">{{ __('Next Steps') }}</h3>
                <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
                    <li>{{ __('Login to your school admin panel') }}</li>
                    <li>{{ __('Complete your school profile') }}</li>
                    <li>{{ __('Add teachers and students') }}</li>
                    <li>{{ __('Start managing your school!') }}</li>
                </ol>
            </div>

            {{-- Login Button --}}
            <a href="{{ url('/edusaas-admin') }}" class="inline-flex items-center gap-2 px-10 py-4 rounded-xl font-bold text-white text-lg gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                {{ __('Login to Dashboard') }}
            </a>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-3xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} EduSaaS. {{ __('All rights reserved.') }}
        </div>
    </footer>

</body>
</html>
