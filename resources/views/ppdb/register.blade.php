@extends('website.layout')

@section('title', __('PPDB Registration Form'))

@section('content')
@php
    $tq = request()->filled('tenant') ? ['tenant' => $tenant->slug] : [];
    $input = 'w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-navy-500 focus:ring-2 focus:ring-navy-100';
@endphp
@include('website.partials.page-header', ['title' => __('PPDB Registration Form'), 'subtitle' => $wave->name . ' · ' . __('Academic Year') . ' ' . $wave->academicYear?->name])

<section class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-semibold">{{ __('Please correct the following:') }}</p>
            <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('ppdb.store', $tq) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        <input type="hidden" name="ppdb_wave_id" value="{{ $wave->id }}">

        <fieldset class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <legend class="px-2 font-heading text-lg font-bold text-navy-700">{{ __('Student data') }}</legend>
            <div class="mt-2 grid gap-4 sm:grid-cols-2">
                <label class="sm:col-span-2">
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Full Name') }} *</span>
                    <input name="full_name" value="{{ old('full_name') }}" required maxlength="255" class="{{ $input }}" placeholder="{{ __('As written on the birth certificate') }}">
                </label>
                <label>
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Date of Birth') }} *</span>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required max="{{ now()->subDay()->toDateString() }}" class="{{ $input }}">
                </label>
                <label>
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Gender') }} *</span>
                    <select name="gender" required class="{{ $input }}">
                        <option value="">{{ __('Select gender') }}</option>
                        @foreach (\App\Enums\Gender::cases() as $gender)
                            <option value="{{ $gender->value }}" @selected(old('gender') === $gender->value)>{{ $gender->label() }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="sm:col-span-2">
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Previous School') }}</span>
                    <input name="previous_school" value="{{ old('previous_school') }}" maxlength="255" class="{{ $input }}">
                </label>
                <label class="sm:col-span-2">
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Full Address') }} *</span>
                    <textarea name="address" rows="3" required maxlength="1000" class="{{ $input }}">{{ old('address') }}</textarea>
                </label>
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <legend class="px-2 font-heading text-lg font-bold text-navy-700">{{ __('Parent / guardian') }}</legend>
            <div class="mt-2 grid gap-4 sm:grid-cols-2">
                <label class="sm:col-span-2">
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Parent / Guardian Name') }} *</span>
                    <input name="parent_name" value="{{ old('parent_name') }}" required maxlength="255" class="{{ $input }}">
                </label>
                <label>
                    <span class="mb-1 block text-sm font-medium text-gray-700">{{ __('Phone / WhatsApp Number') }} *</span>
                    <input type="tel" name="parent_phone" value="{{ old('parent_phone') }}" required maxlength="20" placeholder="08xxxxxxxxxx" class="{{ $input }}">
                </label>
                <label>
                    <span class="mb-1 block text-sm font-medium text-gray-700">Email</span>
                    <input type="email" name="parent_email" value="{{ old('parent_email') }}" maxlength="255" class="{{ $input }}">
                    <span class="mt-1 block text-xs text-gray-500">{{ __('Used to create the parent portal account after acceptance.') }}</span>
                </label>
            </div>
        </fieldset>

        <fieldset class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <legend class="px-2 font-heading text-lg font-bold text-navy-700">{{ __('Documents') }}</legend>
            <p class="text-sm text-gray-500">{{ __('JPG, PNG or PDF, max. 2 MB per file. Documents are only visible to school staff.') }}</p>
            <div class="mt-4 space-y-4">
                @foreach ($documents as $key => [$label, $required, $mimes])
                    <label class="block rounded-xl border-2 border-dashed border-gray-200 p-4 hover:border-navy-200">
                        <span class="mb-2 block text-sm font-medium text-gray-700">{{ $label }} {{ $required ? '*' : '(' . __('optional') . ')' }}</span>
                        <input type="file" name="documents[{{ $key }}]" @required($required) accept="{{ collect(explode(',', $mimes))->map(fn ($m) => '.' . $m)->implode(',') }}"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-navy-50 file:px-4 file:py-2 file:font-semibold file:text-navy-700">
                    </label>
                @endforeach
            </div>
        </fieldset>

        <label class="flex items-start gap-3 text-sm text-gray-700">
            <input type="checkbox" name="agreement" value="1" required class="mt-1 rounded border-gray-300" @checked(old('agreement'))>
            <span>{{ __('I declare that the data and documents are true and may be verified by the school.') }}</span>
        </label>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('ppdb.index', $tq) }}" class="text-sm font-semibold text-gray-600 hover:underline">&larr; {{ __('Back') }}</a>
            <button class="rounded-xl bg-gold-500 px-8 py-3 font-bold text-navy-900 hover:bg-gold-400">{{ __('Submit Registration') }}</button>
        </div>
    </form>
</section>
@endsection
