@extends('website.layout')

@section('title', __('PPDB Online'))
@section('description', __('Online new student admission at :school: open waves, requirements and registration.', ['school' => $tenant->name]))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
@include('website.partials.page-header', ['title' => __('New Student Admission (PPDB)'), 'subtitle' => $tenant->name])

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <div class="grid gap-4 md:grid-cols-4">
        @foreach ([
            ['1', __('Choose a wave'), __('Pick an open registration wave below.')],
            ['2', __('Fill in the form'), __('Student and parent data plus scanned documents.')],
            ['3', __('Save your number'), __('Download the registration proof (PDF).')],
            ['4', __('Check the result'), __('Use your number and date of birth on the status page.')],
        ] as [$step, $title, $text])
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gold-500 font-heading font-bold text-navy-900">{{ $step }}</span>
                <p class="mt-3 font-semibold text-navy-700">{{ $title }}</p>
                <p class="mt-1 text-sm text-gray-600">{{ $text }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="mt-12 font-heading text-2xl font-bold text-navy-700">{{ __('Registration waves') }}</h2>
    <div class="mt-6 grid gap-6 md:grid-cols-2">
        @forelse ($waves as $wave)
            @php $open = $wave->opens_at->isPast() && $wave->closes_at->isFuture(); @endphp
            <div class="flex flex-col overflow-hidden rounded-2xl border {{ $open ? 'border-gold-400' : 'border-gray-100' }} bg-white shadow-sm">
                <div class="bg-gradient-to-r from-navy-700 to-navy-600 p-5 text-white">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="font-heading text-lg font-bold">{{ $wave->name }}</h3>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $open ? 'bg-green-500 text-white' : 'bg-white/20 text-white' }}">{{ $open ? __('Open') : __('Opens soon') }}</span>
                    </div>
                    <p class="mt-1 text-sm text-white/70">{{ __('Academic Year') }} {{ $wave->academicYear?->name }}</p>
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">{{ __('Registration period') }}</dt><dd class="font-medium">{{ $wave->opens_at->translatedFormat('d M Y') }} – {{ $wave->closes_at->translatedFormat('d M Y') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ __('Quota per class') }}</dt><dd class="font-medium">{{ $wave->quota_per_class ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">{{ __('Registrants') }}</dt><dd class="font-medium">{{ $wave->registrations_count }}</dd></div>
                    </dl>
                    @if (! empty($wave->requirements))
                        <p class="mt-4 text-sm font-semibold text-gray-700">{{ __('Requirements') }}</p>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-600">
                            @foreach ($wave->requirements as $key => $value)
                                <li>{{ is_int($key) ? $value : trim($key . ($value ? ': ' . $value : '')) }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="mt-auto pt-5">
                        @if ($open)
                            <a href="{{ route('ppdb.register', ['wave' => $wave->id] + $tq) }}" class="block rounded-xl bg-gold-500 py-3 text-center font-bold text-navy-900 hover:bg-gold-400">{{ __('Register Now') }}</a>
                        @else
                            <p class="rounded-xl bg-slate-100 py-3 text-center text-sm font-semibold text-gray-600">{{ __('Opens on :date', ['date' => $wave->opens_at->translatedFormat('d F Y')]) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm md:col-span-2">
                {{ __('There are no registration waves open at this time. Please check back later.') }}
            </div>
        @endforelse
    </div>

    <div class="mt-10 rounded-2xl bg-navy-700 p-6 text-center text-white">
        <p class="font-semibold">{{ __('Already registered?') }}</p>
        <a href="{{ route('ppdb.status', $tq) }}" class="mt-3 inline-block rounded-xl bg-white px-6 py-2.5 font-bold text-navy-700 hover:bg-gold-100">{{ __('Check Registration Status') }}</a>
    </div>
</section>
@endsection
