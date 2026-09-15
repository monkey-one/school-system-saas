@extends('portal.layout')

@section('title', __('Leave Requests'))

@section('content')
@php
    $colors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $input = 'w-full rounded-lg border-gray-300 text-sm';
@endphp
<div class="grid gap-6 lg:grid-cols-5">
    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
        <h2 class="font-heading font-bold text-navy-700">{{ __('Submit a leave request') }}</h2>
        <p class="mt-1 text-sm text-gray-500">{{ __('The homeroom teacher of :name will review it. Approved requests are recorded in attendance automatically.', ['name' => $student->full_name]) }}</p>

        <form method="POST" action="{{ route($portal . '.leave-requests.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="type">{{ __('Type') }}</label>
                <select id="type" name="type" required class="{{ $input }}">
                    @foreach (\App\Models\LeaveRequest::typeLabels() as $key => $label)
                        <option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="start_date">{{ __('From') }}</label>
                    <input id="start_date" type="date" name="start_date" value="{{ old('start_date', today()->toDateString()) }}" min="{{ today()->subDays(7)->toDateString() }}" required class="{{ $input }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700" for="end_date">{{ __('Until') }}</label>
                    <input id="end_date" type="date" name="end_date" value="{{ old('end_date', today()->toDateString()) }}" required class="{{ $input }}">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="reason">{{ __('Reason') }}</label>
                <textarea id="reason" name="reason" rows="4" maxlength="1000" required class="{{ $input }}">{{ old('reason') }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700" for="attachment">{{ __('Attachment (optional)') }}</label>
                <input id="attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-navy-50 file:px-3 file:py-2 file:font-semibold file:text-navy-700">
                <p class="mt-1 text-xs text-gray-500">{{ __('Doctor\'s note or letter. PDF/JPG/PNG, max. 2 MB.') }}</p>
            </div>
            <button class="w-full rounded-xl bg-navy-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">{{ __('Submit request') }}</button>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm lg:col-span-3">
        <h2 class="border-b border-gray-100 px-6 py-4 font-heading font-bold text-navy-700">{{ __('Request history') }}</h2>
        <div class="divide-y divide-gray-100">
            @forelse ($requests as $request)
                <div class="px-6 py-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="font-semibold text-gray-800">
                            {{ \App\Models\LeaveRequest::typeLabels()[$request->type] ?? $request->type }}
                            · {{ $request->start_date->translatedFormat('d M') }} – {{ $request->end_date->translatedFormat('d M Y') }}
                        </p>
                        @include('portal.partials.badge', ['label' => \App\Models\LeaveRequest::statusLabels()[$request->status] ?? $request->status, 'color' => $colors[$request->status] ?? 'gray'])
                    </div>
                    <p class="mt-1 text-sm text-gray-600">{{ $request->reason }}</p>
                    @if ($request->review_note)
                        <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-gray-700">{{ __('Note') }}: {{ $request->review_note }}</p>
                    @endif
                    <p class="mt-2 text-xs text-gray-500">
                        {{ __('Submitted') }} {{ $request->created_at->diffForHumans() }}
                        @if ($request->reviewer) · {{ __('Reviewed by :name', ['name' => $request->reviewer->name]) }} @endif
                        @if ($request->attachment) · <a href="{{ route('leave-requests.attachment', $request) }}" target="_blank" class="font-semibold text-navy-500 hover:underline">{{ __('Attachment') }}</a> @endif
                    </p>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-gray-500">{{ __('No leave requests yet.') }}</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
