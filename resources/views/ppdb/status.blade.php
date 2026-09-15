@extends('website.layout')

@section('title', __('Check Registration Status'))

@section('content')
@php
    $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : [];
    $colors = ['success' => 'bg-green-100 text-green-800', 'danger' => 'bg-red-100 text-red-800', 'warning' => 'bg-amber-100 text-amber-800', 'info' => 'bg-blue-100 text-blue-800', 'gray' => 'bg-gray-100 text-gray-700'];
@endphp
@include('website.partials.page-header', ['title' => __('Check Registration Status'), 'subtitle' => __('PPDB :school', ['school' => $tenant->name])])

<section class="mx-auto max-w-xl px-4 py-12 sm:px-6">
    <form method="POST" action="{{ route('ppdb.check-status', $tq) }}" class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        @csrf
        <label class="block">
            <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Registration Number') }}</span>
            <input name="registration_number" value="{{ old('registration_number', request('registration_number')) }}" required maxlength="50" placeholder="PPDB-{{ now()->year }}-..." class="w-full rounded-xl border border-gray-300 px-4 py-2.5 font-mono uppercase">
        </label>
        <label class="block">
            <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Date of Birth') }}</span>
            <input type="date" name="birth_date" value="{{ old('birth_date', request('birth_date')) }}" required class="w-full rounded-xl border border-gray-300 px-4 py-2.5">
        </label>
        @error('registration_number')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <button class="w-full rounded-xl bg-navy-600 py-3 font-semibold text-white hover:bg-navy-700">{{ __('Check Status') }}</button>
    </form>

    @if ($searched)
        @if ($registration)
            <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-mono text-sm font-semibold text-navy-700">{{ $registration->registration_number }}</p>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $colors[$registration->status->color()] ?? $colors['gray'] }}">{{ $registration->status->label() }}</span>
                </div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">{{ __('Name') }}</dt><dd class="font-medium">{{ $registration->full_name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">{{ __('Wave') }}</dt><dd class="font-medium">{{ $registration->ppdbWave?->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">{{ __('Registered') }}</dt><dd class="font-medium">{{ $registration->created_at->translatedFormat('d M Y') }}</dd></div>
                    @if ($registration->reviewed_at)<div class="flex justify-between"><dt class="text-gray-500">{{ __('Reviewed') }}</dt><dd class="font-medium">{{ $registration->reviewed_at->translatedFormat('d M Y') }}</dd></div>@endif
                </dl>
                @if ($registration->notes)
                    <p class="mt-4 rounded-xl bg-slate-50 p-3 text-sm text-gray-700">{{ $registration->notes }}</p>
                @endif
                @if ($acceptanceUrl)
                    <a href="{{ $acceptanceUrl }}" class="mt-5 block rounded-xl bg-green-600 py-3 text-center font-semibold text-white hover:bg-green-700">{{ __('Download acceptance letter (PDF)') }}</a>
                @endif
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-800">
                {{ __('No registration matches this number and date of birth. Please check your data.') }}
            </div>
        @endif
    @endif
</section>
@endsection
