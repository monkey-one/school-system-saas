@extends('website.layout')

@section('title', __('School Profile'))

@section('content')
@php $settings = $tenant->settings ?? []; @endphp
@include('website.partials.page-header', ['title' => __('School Profile'), 'subtitle' => $tenant->name])

<section class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-3">
    <div class="space-y-10 lg:col-span-2">
        <div>
            <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('About us') }}</h2>
            <p class="mt-3 leading-relaxed text-gray-700">{{ $tenant->description }}</p>
        </div>
        @if (filled($settings['history'] ?? null))
            <div>
                <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('History') }}</h2>
                <div class="mt-3 space-y-3 leading-relaxed text-gray-700">{!! nl2br(e($settings['history'])) !!}</div>
            </div>
        @endif
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl bg-navy-700 p-6 text-white">
                <h2 class="font-heading text-xl font-bold text-gold-400">{{ __('Vision') }}</h2>
                <p class="mt-3 leading-relaxed text-white/90">{{ $tenant->vision ?? '-' }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="font-heading text-xl font-bold text-navy-700">{{ __('Mission') }}</h2>
                <ol class="mt-3 list-inside list-decimal space-y-2 text-gray-700">
                    @foreach (array_filter(array_map('trim', preg_split('/\r?\n/', (string) $tenant->mission))) as $mission)
                        <li>{{ $mission }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
        @if ($facilities->isNotEmpty())
            <div>
                <h2 class="font-heading text-2xl font-bold text-navy-700">{{ __('Facilities') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach ($facilities as $facility)
                        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                            <p class="font-semibold text-gray-800">{{ $facility->name }}</p>
                            <p class="text-sm text-gray-500">{{ __('Capacity') }}: {{ $facility->capacity ?? '-' }} · {{ $facility->location }}</p>
                            @if ($facility->description)<p class="mt-2 text-sm text-gray-600">{{ $facility->description }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <aside class="space-y-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="font-heading font-bold text-navy-700">{{ __('School identity') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                @foreach ([
                    [__('School Name'), $tenant->name],
                    ['NPSN', $tenant->npsn],
                    [__('Level'), $tenant->school_type?->value],
                    [__('Accreditation'), $tenant->accreditation],
                    [__('Founded'), $tenant->founded_year],
                    [__('Principal'), $tenant->principal_name],
                    [__('Address'), trim($tenant->address . ', ' . $tenant->city . ', ' . $tenant->province, ', ')],
                ] as [$label, $value])
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">{{ $label }}</dt><dd class="text-right font-medium text-gray-800">{{ $value ?: '-' }}</dd></div>
                @endforeach
            </dl>
        </div>
        <div class="grid grid-cols-3 gap-3 text-center">
            @foreach ([['students', __('Students')], ['teachers', __('Teachers')], ['facilities', __('Facilities')]] as [$key, $label])
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="font-heading text-2xl font-bold text-navy-700">{{ $stats[$key] }}</p>
                    <p class="text-xs text-gray-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </aside>
</section>
@endsection
