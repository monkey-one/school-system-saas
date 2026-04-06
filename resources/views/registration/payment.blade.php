<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Complete Payment') }} - EduSaaS</title>
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
    <script src="https://app.{{ $isProduction ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
</head>
<body class="bg-gray-50 font-body antialiased min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="gradient-navy">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gold-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-2xl font-heading font-bold text-white">Edu<span class="text-gold-500">SaaS</span></span>
                </a>
                <x-language-switcher-public />
            </div>
            <h1 class="text-3xl font-heading font-extrabold text-white mb-2">{{ __('Complete Payment') }}</h1>
            <p class="text-white/70">{{ __('Complete your payment to activate your subscription.') }}</p>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1">
        {{-- Order Summary --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 mb-8">
            <h2 class="font-heading font-bold text-navy-600 text-xl mb-6">{{ __('Order Summary') }}</h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ __('School') }}</span>
                    <span class="font-semibold text-navy-600">{{ $tenant->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ __('Plan') }}</span>
                    <span class="font-semibold text-navy-600">{{ __($plan->name) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ __('Billing Cycle') }}</span>
                    <span class="font-semibold text-navy-600">
                        {{ $subscription->billing_cycle === 'annual' ? __('Annual') : __('Monthly') }}
                        @if($subscription->billing_cycle === 'annual')
                        <span class="text-green-600 text-sm ml-1">({{ __('Save 20%') }})</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">{{ __('Order ID') }}</span>
                    <span class="font-mono text-sm text-gray-500">{{ $subscription->payment_reference }}</span>
                </div>

                <hr class="my-4">

                <div class="flex justify-between items-center">
                    <span class="font-heading font-bold text-navy-600 text-lg">{{ __('Total') }}</span>
                    <span class="font-heading font-extrabold text-navy-600 text-2xl">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Payment Button --}}
        <div class="text-center">
            <button id="pay-button"
                    class="inline-flex items-center gap-3 px-10 py-4 rounded-xl font-bold text-white text-lg gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                {{ __('Pay Now') }}
            </button>
            <p class="text-sm text-gray-400 mt-4">{{ __('You will be redirected to a secure payment page.') }}</p>
        </div>

        {{-- Payment Methods Info --}}
        <div class="mt-10 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-heading font-bold text-gray-700 mb-3">{{ __('Available Payment Methods') }}</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    {{ __('Credit Card') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ __('Bank Transfer') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    {{ __('E-Wallet') }}
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-navy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Convenience Store') }}
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-3xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} EduSaaS. {{ __('All rights reserved.') }}
        </div>
    </footer>

    <script>
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = '{{ route("register.success", ["tenant" => $tenant->slug]) }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("register.success", ["tenant" => $tenant->slug]) }}';
                },
                onError: function(result) {
                    alert('{{ __("Payment failed. Please try again.") }}');
                },
                onClose: function() {
                    // user closed popup without finishing payment
                }
            });
        });
    </script>
</body>
</html>
