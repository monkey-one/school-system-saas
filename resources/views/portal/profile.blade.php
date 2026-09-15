@extends('portal.layout')

@section('title', __('Account'))

@section('content')
<div class="grid gap-6 lg:grid-cols-2">
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="font-heading font-bold text-navy-700">{{ $portal === 'parent' ? __('Child data') : __('Student data') }}</h2>
        <dl class="mt-4 grid grid-cols-3 gap-x-4 gap-y-3 text-sm">
            <dt class="text-gray-500">{{ __('Full Name') }}</dt><dd class="col-span-2 font-medium">{{ $student->full_name }}</dd>
            <dt class="text-gray-500">NIS / NISN</dt><dd class="col-span-2 font-medium">{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Class') }}</dt><dd class="col-span-2 font-medium">{{ $student->classroom?->name ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Homeroom Teacher') }}</dt><dd class="col-span-2 font-medium">{{ $student->classroom?->homeroomTeacher?->full_name ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Place, Date of Birth') }}</dt><dd class="col-span-2 font-medium">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date?->translatedFormat('d F Y') ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Gender') }}</dt><dd class="col-span-2 font-medium">{{ $student->gender?->label() ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Address') }}</dt><dd class="col-span-2 font-medium">{{ $student->address ?? '-' }}</dd>
            <dt class="text-gray-500">{{ __('Status') }}</dt><dd class="col-span-2">@include('portal.partials.badge', ['label' => $student->status->label(), 'color' => $student->status->color()])</dd>
        </dl>
        <p class="mt-4 text-xs text-gray-500">{{ __('To correct this data, please contact the school administration.') }}</p>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="font-heading font-bold text-navy-700">{{ __('Login account') }}</h2>
        <dl class="mt-4 grid grid-cols-3 gap-x-4 gap-y-3 text-sm">
            <dt class="text-gray-500">{{ __('Name') }}</dt><dd class="col-span-2 font-medium">{{ $user->name }}</dd>
            <dt class="text-gray-500">{{ __('Email') }}</dt><dd class="col-span-2 font-medium">{{ $user->email }}</dd>
            <dt class="text-gray-500">{{ __('Last login') }}</dt><dd class="col-span-2 font-medium">{{ $user->last_login_at?->translatedFormat('d M Y H:i') ?? '-' }}</dd>
        </dl>

        <h3 class="mt-6 font-heading font-bold text-navy-700">{{ __('Change password') }}</h3>
        <form method="POST" action="{{ route($portal . '.profile.password') }}" class="mt-3 space-y-3">
            @csrf
            @method('PUT')
            <input type="password" name="current_password" required autocomplete="current-password" placeholder="{{ __('Current password') }}" class="w-full rounded-lg border-gray-300 text-sm">
            <input type="password" name="password" required autocomplete="new-password" placeholder="{{ __('New password (min. 8 characters, letters and numbers)') }}" class="w-full rounded-lg border-gray-300 text-sm">
            <input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirm new password') }}" class="w-full rounded-lg border-gray-300 text-sm">
            <button class="rounded-xl bg-navy-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ __('Update password') }}</button>
        </form>
    </section>
</div>
@endsection
