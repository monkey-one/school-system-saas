@extends('website.layout')

@section('title', __('Contact'))

@section('content')
@php
    $settings = $tenant->settings ?? [];
    $whatsapp = preg_replace('/\D/', '', (string) ($settings['whatsapp'] ?? ''));
    $whatsapp = str_starts_with($whatsapp, '0') ? '62' . substr($whatsapp, 1) : $whatsapp;
    $mapQuery = urlencode(trim($tenant->name . ' ' . $tenant->address . ' ' . $tenant->city));
@endphp
@include('website.partials.page-header', ['title' => __('Contact'), 'subtitle' => __('We are happy to answer your questions')])

<section class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-2">
    <div class="space-y-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="font-heading text-xl font-bold text-navy-700">{{ $tenant->name }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-gray-500">{{ __('Address') }}</dt><dd class="font-medium text-gray-800">{{ $tenant->address }}, {{ $tenant->city }}, {{ $tenant->province }}</dd></div>
                @if ($tenant->phone)<div><dt class="text-gray-500">{{ __('Phone') }}</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $tenant->phone) }}" class="font-medium text-navy-600 hover:underline">{{ $tenant->phone }}</a></dd></div>@endif
                @if ($tenant->email)<div><dt class="text-gray-500">Email</dt><dd><a href="mailto:{{ $tenant->email }}" class="font-medium text-navy-600 hover:underline">{{ $tenant->email }}</a></dd></div>@endif
                <div><dt class="text-gray-500">{{ __('Office hours') }}</dt><dd class="font-medium text-gray-800">{{ $settings['office_hours'] ?? __('Monday–Friday, 07:00–15:00') }}</dd></div>
            </dl>
            <div class="mt-6 flex flex-wrap gap-3">
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">{{ __('Chat on WhatsApp') }}</a>
                @endif
                <a href="https://www.google.com/maps/search/?api=1&query={{ $mapQuery }}" target="_blank" rel="noopener" class="rounded-xl border border-navy-600 px-5 py-2.5 text-sm font-semibold text-navy-700 hover:bg-navy-50">{{ __('Open in Google Maps') }}</a>
            </div>
        </div>
        <div class="rounded-2xl bg-navy-700 p-6 text-white">
            <h2 class="font-heading text-lg font-bold text-gold-400">{{ __('Interested in enrolling?') }}</h2>
            <p class="mt-2 text-sm text-white/80">{{ __('Registration, document upload and status checks are all online.') }}</p>
            <a href="{{ route('ppdb.index', request()->filled('tenant') ? ['tenant' => $tenant->slug] : []) }}" class="mt-4 inline-block rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-navy-900 hover:bg-gold-400">{{ __('PPDB Online') }}</a>
        </div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm">
        <iframe title="{{ __('Map') }}" src="https://maps.google.com/maps?q={{ $mapQuery }}&output=embed" class="h-full min-h-[420px] w-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>
@endsection
