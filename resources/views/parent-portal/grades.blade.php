@extends('parent-portal.layout')

@section('title', __('Child Grades'))
@section('page-title', __('Child Grades'))

@section('content')
<div class="space-y-6">
    {{-- Child Info --}}
    @if(isset($selectedChild))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-navy-100 flex items-center justify-center text-navy-700 font-bold text-lg">
            {{ strtoupper(substr($selectedChild->full_name, 0, 1)) }}
        </div>
        <div>
            <h3 class="font-heading font-bold text-navy-700">{{ $selectedChild->full_name }}</h3>
            <p class="text-sm text-gray-500">{{ $selectedChild->classroom->name ?? '-' }} — NIS: {{ $selectedChild->nis }}</p>
        </div>
    </div>
    @endif

    {{-- Semester Selector --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-lg font-heading font-bold text-navy-700">{{ __('Grades Per Subject') }}</h2>
            <form method="GET" class="flex items-center gap-2">
                <select name="semester_id" class="rounded-lg border-gray-300 text-sm focus:ring-navy-500 focus:border-navy-500">
                    @foreach($semesters ?? [] as $semester)
                    <option value="{{ $semester->id }}" {{ ($selectedSemester ?? null) == $semester->id ? 'selected' : '' }}>
                        {{ $semester->name }}
                    </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-navy-700 text-white text-sm rounded-lg hover:bg-navy-800 transition">
                    {{ __('Show') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Grades per subject --}}
    @forelse($subjects ?? [] as $subject)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-navy-50 border-b border-gray-100">
            <h3 class="font-heading font-bold text-navy-700">{{ $subject['name'] }}</h3>
            <p class="text-xs text-gray-500">{{ __('Teacher') }}: {{ $subject['teacher'] ?? '-' }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">{{ __('Assessment') }}</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600">{{ __('Grade') }}</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600">{{ __('Remedial') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($subject['grades'] ?? [] as $grade)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">{{ $grade['assessment_name'] }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-navy-50 text-navy-700">
                                {{ $grade['type'] }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center font-semibold {{ $grade['score'] >= 75 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $grade['score'] ?? '-' }}
                        </td>
                        <td class="px-6 py-3 text-center text-gray-500">
                            {{ $grade['remedial_score'] ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if(isset($subject['average']))
                <tfoot class="bg-gold-50 border-t border-gold-200">
                    <tr>
                        <td colspan="2" class="px-6 py-3 font-heading font-bold text-navy-700">{{ __('Average') }}</td>
                        <td class="px-6 py-3 text-center font-bold text-navy-700">{{ number_format($subject['average'], 1) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <p class="text-gray-400">{{ __('No grade data for this semester.') }}</p>
    </div>
    @endforelse
</div>
@endsection
