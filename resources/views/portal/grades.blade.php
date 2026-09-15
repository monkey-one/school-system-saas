@extends('portal.layout')

@section('title', __('Grades'))

@section('content')
<form method="GET" class="flex flex-wrap items-center gap-3">
    <label for="semester" class="text-sm font-medium text-gray-600">{{ __('Semester') }}</label>
    <select id="semester" name="semester" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm">
        @foreach ($semesters as $semester)
            <option value="{{ $semester->id }}" @selected($semester->id === $semesterId)>{{ $semester->name }} {{ $semester->academicYear?->name }}</option>
        @endforeach
    </select>
    <noscript><button class="rounded-lg bg-navy-600 px-3 py-2 text-sm text-white">{{ __('Show') }}</button></noscript>
</form>

@if ($grades->isEmpty())
    <div class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 shadow-sm">{{ __('No grades for this semester yet.') }}</div>
@else
    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($grades as $subject => $items)
            @php
                $scores = $items->map(fn ($g) => (float) ($g->is_remedial && $g->remedial_score ? $g->remedial_score : $g->score));
                $average = $scores->avg();
            @endphp
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-heading font-bold text-navy-700">{{ $subject }}</h3>
                    <span class="rounded-lg bg-navy-50 px-3 py-1 text-sm font-bold {{ $average >= 75 ? 'text-green-700' : 'text-amber-700' }}">{{ __('Avg') }} {{ number_format($average, 1) }}</span>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $grade)
                            <tr>
                                <td class="py-2">
                                    <p class="font-medium text-gray-800">{{ $grade->assessment?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $grade->assessment?->assessmentType?->name }} · {{ $grade->assessment?->date?->translatedFormat('d M Y') }}</p>
                                </td>
                                <td class="py-2 text-right">
                                    <span class="font-heading text-base font-bold">{{ rtrim(rtrim((string) $grade->score, '0'), '.') }}</span>
                                    @if ($grade->is_remedial && $grade->remedial_score)
                                        <p class="text-xs text-amber-700">{{ __('Remedial') }}: {{ rtrim(rtrim((string) $grade->remedial_score, '0'), '.') }}</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endforeach
    </div>
@endif
@endsection
