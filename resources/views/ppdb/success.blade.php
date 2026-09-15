@extends('website.layout')

@section('title', __('Registration submitted'))

@section('content')
@php $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : []; @endphp
<section class="mx-auto max-w-2xl px-4 py-16 text-center sm:px-6">
    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-4xl">✓</div>
    <h1 class="mt-6 font-heading text-3xl font-extrabold text-navy-800">{{ __('Registration submitted') }}</h1>
    <p class="mt-3 text-gray-600">{{ __('Thank you, :name. Keep this registration number to check the result.', ['name' => $registration->parent_name]) }}</p>

    <div class="mt-8 rounded-2xl border-2 border-dashed border-gold-400 bg-gold-50 p-6">
        <p class="text-sm font-semibold uppercase text-gold-600">{{ __('Registration Number') }}</p>
        <p class="mt-2 break-all font-mono text-3xl font-bold tracking-wide text-navy-800">{{ $registration->registration_number }}</p>
        <p class="mt-2 text-sm text-gray-600">{{ $registration->full_name }} · {{ $registration->ppdbWave?->name }}</p>
    </div>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="{{ $proofUrl }}" class="rounded-xl bg-navy-600 px-6 py-3 font-semibold text-white hover:bg-navy-700">{{ __('Download registration proof (PDF)') }}</a>
        <a href="{{ route('ppdb.status', $tq) }}" class="rounded-xl border border-navy-600 px-6 py-3 font-semibold text-navy-700 hover:bg-navy-50">{{ __('Check Registration Status') }}</a>
    </div>
    <p class="mt-6 text-xs text-gray-500">{{ __('The school will review your registration. You will be notified via WhatsApp when the status changes.') }}</p>
</section>
@endsection
