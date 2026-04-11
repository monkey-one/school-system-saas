<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register Your School') }} - EduSaaS</title>
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
<body class="bg-gray-50 font-body antialiased min-h-screen">

    {{-- Header --}}
    <header class="gradient-navy">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl gradient-gold flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-2xl font-heading font-bold text-white">Edu<span class="text-gold-500">SaaS</span></span>
                </a>
                <x-language-switcher-public />
            </div>
            <h1 class="text-3xl sm:text-4xl font-heading font-extrabold text-white mb-2">{{ __('Register Your School') }}</h1>
            <p class="text-white/70 text-lg">{{ __('Fill in the form below to get started with EduSaaS.') }}</p>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
          x-data="{
              step: 1,
              registrationType: 'trial',
              billingCycle: 'monthly',
              selectedPlan: '{{ $selectedPlan ?? '' }}',
              schoolType: '',
              get selectedPlanData() {
                  const plans = @js($plans->keyBy('id'));
                  return plans[this.selectedPlan] || null;
              },
              get price() {
                  const p = this.selectedPlanData;
                  if (!p) return 0;
                  return this.billingCycle === 'annual' ? parseInt(p.price_annual) : parseInt(p.price_monthly);
              },
              formatCurrency(n) {
                  return '{{ \App\Helpers\CurrencyHelper::symbol() }} ' + new Intl.NumberFormat('id-ID').format(n);
              }
          }">

        {{-- Step Indicator --}}
        <div class="flex items-center justify-center mb-10">
            <template x-for="s in 3" :key="s">
                <div class="flex items-center">
                    <div :class="step >= s ? 'bg-navy-600 text-white' : 'bg-gray-200 text-gray-500'"
                         class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors">
                        <span x-text="s"></span>
                    </div>
                    <div x-show="s < 3" :class="step > s ? 'bg-navy-600' : 'bg-gray-200'" class="w-16 sm:w-24 h-1 mx-2 rounded transition-colors"></div>
                </div>
            </template>
        </div>
        <div class="flex justify-center gap-8 mb-8 text-sm font-medium text-gray-500">
            <span :class="step >= 1 ? 'text-navy-600' : ''">{{ __('Choose Plan') }}</span>
            <span :class="step >= 2 ? 'text-navy-600' : ''">{{ __('School Info') }}</span>
            <span :class="step >= 3 ? 'text-navy-600' : ''">{{ __('Admin Account') }}</span>
        </div>

        <form method="POST" action="{{ route('register.store') }}" x-ref="form">
            @csrf

            {{-- STEP 1: Choose Plan --}}
            <div x-show="step === 1" x-transition>
                {{-- Registration Type --}}
                <div class="mb-8">
                    <h2 class="text-xl font-heading font-bold text-navy-600 mb-4">{{ __('How would you like to start?') }}</h2>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <label @click="registrationType = 'trial'"
                               :class="registrationType === 'trial' ? 'border-navy-600 bg-navy-50 ring-2 ring-navy-600/20' : 'border-gray-200 hover:border-gray-300'"
                               class="relative cursor-pointer rounded-xl border-2 p-6 transition-all">
                            <input type="radio" name="registration_type" value="trial" x-model="registrationType" class="sr-only">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-navy-600">{{ __('Free Trial 14 Days') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('Try all features free for 14 days. No credit card required.') }}</p>
                                </div>
                            </div>
                        </label>
                        <label @click="registrationType = 'paid'"
                               :class="registrationType === 'paid' ? 'border-navy-600 bg-navy-50 ring-2 ring-navy-600/20' : 'border-gray-200 hover:border-gray-300'"
                               class="relative cursor-pointer rounded-xl border-2 p-6 transition-all">
                            <input type="radio" name="registration_type" value="paid" x-model="registrationType" class="sr-only">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gold-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-navy-600">{{ __('Subscribe Now') }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('Pay now and get full access immediately. Save 20% with annual billing.') }}</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Billing Cycle --}}
                <div x-show="registrationType === 'paid'" x-transition class="mb-8">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <button type="button" @click="billingCycle = 'monthly'"
                                :class="billingCycle === 'monthly' ? 'bg-navy-600 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-6 py-2.5 rounded-full font-semibold text-sm transition-all">
                            {{ __('Monthly') }}
                        </button>
                        <button type="button" @click="billingCycle = 'annual'"
                                :class="billingCycle === 'annual' ? 'bg-navy-600 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-6 py-2.5 rounded-full font-semibold text-sm transition-all">
                            {{ __('Annual') }} <span class="text-gold-400 font-bold ml-1">-20%</span>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="billing_cycle" x-bind:value="billingCycle">

                {{-- Plan Selection --}}
                <div class="mb-8">
                    <h2 class="text-xl font-heading font-bold text-navy-600 mb-4">{{ __('Select a Plan') }}</h2>
                    <div class="grid sm:grid-cols-3 gap-4">
                        @foreach($plans as $plan)
                        <label @click="selectedPlan = '{{ $plan->id }}'"
                               :class="selectedPlan == '{{ $plan->id }}' ? 'border-navy-600 bg-navy-50 ring-2 ring-navy-600/20' : 'border-gray-200 hover:border-gray-300'"
                               class="relative cursor-pointer rounded-xl border-2 p-6 transition-all">
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" x-model="selectedPlan" class="sr-only" required>
                            @if($plan->slug === 'professional')
                            <span class="absolute -top-3 left-4 bg-gold-500 text-white text-xs font-bold px-3 py-1 rounded-full">{{ __('Popular') }}</span>
                            @endif
                            <h3 class="font-heading font-bold text-navy-600 text-lg mb-1">{{ __($plan->name) }}</h3>
                            <div class="mb-3">
                                <span class="text-2xl font-heading font-extrabold text-navy-600"
                                      x-text="registrationType === 'trial' ? '{{ __('Free') }}' : formatCurrency(billingCycle === 'annual' ? {{ (int)$plan->price_annual }} : {{ (int)$plan->price_monthly }})">
                                </span>
                                <span x-show="registrationType === 'paid'" class="text-gray-400 text-sm">{{ __('/month') }}</span>
                            </div>
                            <ul class="space-y-1.5 text-sm text-gray-600">
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('Max.') }} {{ number_format($plan->max_students) }} {{ __('students') }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __('Max.') }} {{ number_format($plan->max_teachers) }} {{ __('teachers') }}
                                </li>
                                @foreach(array_slice($plan->features ?? [], 0, 3) as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ __(ucfirst(str_replace('_', ' ', $feature))) }}
                                </li>
                                @endforeach
                            </ul>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Summary & Next --}}
                <div x-show="selectedPlan" x-transition class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">{{ __('Selected Plan') }}</p>
                            <p class="font-heading font-bold text-navy-600 text-lg" x-text="selectedPlanData?.name"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500" x-text="registrationType === 'trial' ? '{{ __('14-day free trial') }}' : billingCycle === 'annual' ? '{{ __('Annual billing') }}' : '{{ __('Monthly billing') }}'"></p>
                            <p class="font-heading font-extrabold text-navy-600 text-xl" x-text="registrationType === 'trial' ? '{{ __('Free') }}' : formatCurrency(price)"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" @click="if(selectedPlan) step = 2" :disabled="!selectedPlan"
                            :class="selectedPlan ? 'gradient-gold hover:opacity-90' : 'bg-gray-300 cursor-not-allowed'"
                            class="px-8 py-3 rounded-xl font-bold text-white transition-all shadow-lg">
                        {{ __('Next') }} →
                    </button>
                </div>
            </div>

            {{-- STEP 2: School Information --}}
            <div x-show="step === 2" x-transition>
                <h2 class="text-xl font-heading font-bold text-navy-600 mb-6">{{ __('School Information') }}</h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('School Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="school_name" required value="{{ old('school_name') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   placeholder="{{ __('e.g. SMP Negeri 1 Jakarta') }}">
                            @error('school_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('School Type') }} <span class="text-red-500">*</span></label>
                            <select name="school_type" required x-model="schoolType"
                                    class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm">
                                <option value="">{{ __('Select type') }}</option>
                                @foreach(App\Enums\SchoolType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            @error('school_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NPSN</label>
                            <input type="text" name="npsn" value="{{ old('npsn') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   placeholder="{{ __('School NPSN number') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('City') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="city" required value="{{ old('city') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm">
                            @error('city') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Province') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="province" required value="{{ old('province') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm">
                            @error('province') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('School Phone') }} <span class="text-red-500">*</span></label>
                            <input type="tel" name="school_phone" required value="{{ old('school_phone') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   placeholder="021-1234567">
                            @error('school_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('School Email') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="school_email" required value="{{ old('school_email') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   placeholder="info@sekolah.sch.id">
                            @error('school_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Principal Name') }}</label>
                            <input type="text" name="principal_name" value="{{ old('principal_name') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 1" class="px-6 py-3 rounded-xl font-semibold text-gray-600 border-2 border-gray-300 hover:bg-gray-50 transition-all">
                        ← {{ __('Back') }}
                    </button>
                    <button type="button" @click="step = 3" class="px-8 py-3 rounded-xl font-bold text-white gradient-gold hover:opacity-90 transition-all shadow-lg">
                        {{ __('Next') }} →
                    </button>
                </div>
            </div>

            {{-- STEP 3: Admin Account --}}
            <div x-show="step === 3" x-transition>
                <h2 class="text-xl font-heading font-bold text-navy-600 mb-6">{{ __('Administrator Account') }}</h2>
                <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8 space-y-5">
                    <p class="text-sm text-gray-500 mb-4">{{ __('This account will be used to login and manage your school.') }}</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="admin_name" required value="{{ old('admin_name') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm">
                        @error('admin_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="admin_email" required value="{{ old('admin_email') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                               placeholder="admin@sekolah.sch.id">
                        @error('admin_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="admin_password" required
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   minlength="8">
                            @error('admin_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirm Password') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="admin_password_confirmation" required
                                   class="w-full rounded-lg border-gray-300 focus:border-navy-600 focus:ring-navy-600 shadow-sm"
                                   minlength="8">
                        </div>
                    </div>
                </div>

                {{-- Final Summary --}}
                <div class="bg-navy-50 rounded-xl border border-navy-200 p-6 mt-6">
                    <h3 class="font-heading font-bold text-navy-600 mb-3">{{ __('Registration Summary') }}</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Plan') }}</span>
                            <span class="font-semibold text-navy-600" x-text="selectedPlanData?.name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Type') }}</span>
                            <span class="font-semibold text-navy-600" x-text="registrationType === 'trial' ? '{{ __('Free Trial 14 Days') }}' : billingCycle === 'annual' ? '{{ __('Annual Subscription') }}' : '{{ __('Monthly Subscription') }}'"></span>
                        </div>
                        <div class="flex justify-between border-t border-navy-200 pt-2 mt-2">
                            <span class="font-semibold text-gray-700">{{ __('Total') }}</span>
                            <span class="font-heading font-extrabold text-navy-600 text-lg" x-text="registrationType === 'trial' ? '{{ __('Free') }}' : formatCurrency(price)"></span>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-6">
                    <p class="text-red-700 font-semibold text-sm mb-2">{{ __('Please fix the following errors:') }}</p>
                    <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex justify-between mt-6">
                    <button type="button" @click="step = 2" class="px-6 py-3 rounded-xl font-semibold text-gray-600 border-2 border-gray-300 hover:bg-gray-50 transition-all">
                        ← {{ __('Back') }}
                    </button>
                    <button type="submit" class="px-8 py-3 rounded-xl font-bold text-white gradient-gold hover:opacity-90 transition-all shadow-lg shadow-gold-500/30">
                        <span x-text="registrationType === 'trial' ? '{{ __('Start Free Trial') }}' : '{{ __('Continue to Payment') }}'"></span> →
                    </button>
                </div>
            </div>
        </form>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 mt-auto">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} EduSaaS. {{ __('All rights reserved.') }}
        </div>
    </footer>

</body>
</html>
