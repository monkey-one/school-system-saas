@extends('portal.layout')

@section('title', __('Report Cards'))

@section('content')
<p class="text-sm text-gray-600">{{ __('Report cards published by the school can be downloaded as PDF.') }}</p>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @forelse ($reportCards as $reportCard)
        <section class="flex flex-col rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium uppercase text-gray-500">{{ $reportCard->semester?->academicYear?->name }}</p>
            <h3 class="mt-1 font-heading text-lg font-bold text-navy-700">{{ __('Semester') }} {{ $reportCard->semester?->name }}</h3>
            <p class="mt-1 text-sm text-gray-600">{{ __('Class') }} {{ $reportCard->classroom?->name ?? '-' }} · {{ __(':count subjects', ['count' => $reportCard->report_card_subjects_count]) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Published') }} {{ $reportCard->published_at?->translatedFormat('d M Y') }}</p>
            <a href="{{ route($portal . '.report-cards.pdf', $reportCard) }}"
               class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-navy-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('Download PDF') }}
            </a>
        </section>
    @empty
        <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm md:col-span-2 xl:col-span-3">
            {{ __('No report cards have been published yet.') }}
        </div>
    @endforelse
</div>
@endsection
