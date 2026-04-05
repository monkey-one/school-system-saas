@extends('parent-portal.layout')

@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))

@section('content')
<div class="space-y-6">

    {{-- Child Selector (Mobile) --}}
    @if(isset($children) && count($children) > 1)
    <div class="lg:hidden bg-white rounded-xl border border-gray-100 p-4">
        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">{{ __('Selected child') }}</label>
        <select onchange="window.location.href = '/parent/child/' + this.value"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-navy-600/20 focus:border-navy-600 outline-none">
            @foreach($children as $child)
            <option value="{{ $child->id }}" {{ ($selectedChild->id ?? null) === $child->id ? 'selected' : '' }}>
                {{ $child->full_name }} — {{ $child->classroom->name ?? '' }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-navy-700 to-navy-600 rounded-2xl p-6 sm:p-8 text-white">
        <h2 class="text-xl sm:text-2xl font-heading font-bold mb-1">{{ __('Welcome') }}, {{ $parent->name ?? __('Parent') }}! 👋</h2>
        <p class="text-white/60 text-sm">{{ __('Monitoring') }} <strong class="text-gold-400">{{ $selectedChild->full_name ?? '' }}</strong> — {{ $selectedChild->classroom->name ?? '' }}</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {{-- Attendance Summary --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">{{ __('Attendance This Month') }}</span>
            </div>
            <div class="text-2xl font-heading font-bold text-gray-800">{{ $attendanceSummary['percentage'] ?? 0 }}%</div>
            <div class="flex gap-4 mt-2 text-xs text-gray-400">
                <span>{{ __('Present') }}: {{ $attendanceSummary['hadir'] ?? 0 }}</span>
                <span>{{ __('Sick') }}: {{ $attendanceSummary['sakit'] ?? 0 }}</span>
                <span>{{ __('Permission') }}: {{ $attendanceSummary['izin'] ?? 0 }}</span>
                <span>{{ __('Absent') }}: {{ $attendanceSummary['alfa'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Latest Grade --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">{{ __('Average Grade') }}</span>
            </div>
            <div class="text-2xl font-heading font-bold text-gray-800">{{ $averageGrade ?? '-' }}</div>
            <p class="text-xs text-gray-400 mt-1">{{ __('This semester') }}</p>
        </div>

        {{-- SPP Status --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg {{ ($sppStatus['is_paid'] ?? false) ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ ($sppStatus['is_paid'] ?? false) ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">{{ __('Tuition Fee') }} {{ now()->translatedFormat('F') }}</span>
            </div>
            @if($sppStatus['is_paid'] ?? false)
                <div class="text-2xl font-heading font-bold text-green-600">{{ __('Paid') }}</div>
            @else
                <div class="text-2xl font-heading font-bold text-red-600">Rp {{ number_format($sppStatus['amount'] ?? 0, 0, ',', '.') }}</div>
                <a href="{{ route('parent.spp') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-white bg-gold-500 hover:bg-gold-600 px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    {{ __('Pay Now') }}
                </a>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Latest Grades --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-base font-heading font-bold text-navy-600 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('Latest Grades') }}
            </h3>
            <div class="space-y-3">
                @forelse($latestGrades ?? [] as $grade)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $grade['subject_name'] ?? '' }}</div>
                        <div class="text-xs text-gray-400">{{ $grade['assessment_name'] ?? '' }}</div>
                    </div>
                    <div class="text-lg font-heading font-bold {{ ($grade['score'] ?? 0) >= 75 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $grade['score'] ?? '-' }}
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 text-sm">{{ __('No grades yet') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Chat with Homeroom Teacher --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-base font-heading font-bold text-navy-600 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                {{ __('Homeroom Teacher Messages') }}
            </h3>

            @if(isset($homeroomTeacher))
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg mb-4">
                <div class="w-10 h-10 rounded-full bg-navy-600 text-white flex items-center justify-center text-sm font-bold">
                    {{ substr($homeroomTeacher->full_name ?? 'G', 0, 1) }}
                </div>
                <div>
                    <div class="font-semibold text-sm text-gray-800">{{ $homeroomTeacher->full_name ?? '' }}</div>
                    <div class="text-xs text-gray-400">{{ __('Homeroom Teacher') }} {{ $selectedChild->classroom->name ?? '' }}</div>
                </div>
            </div>
            @endif

            <div class="space-y-3 max-h-64 overflow-y-auto mb-4">
                @forelse($recentMessages ?? [] as $message)
                <div class="p-3 rounded-lg {{ $message['is_mine'] ? 'bg-navy-600 text-white ml-8' : 'bg-gray-100 text-gray-800 mr-8' }}">
                    <p class="text-sm">{{ $message['content'] ?? '' }}</p>
                    <p class="text-xs mt-1 {{ $message['is_mine'] ? 'text-white/50' : 'text-gray-400' }}">{{ $message['time'] ?? '' }}</p>
                </div>
                @empty
                <div class="text-center py-6 text-gray-400 text-sm">{{ __('No messages yet') }}</div>
                @endforelse
            </div>

            <a href="{{ route('parent.messages') }}" class="block text-center py-3 rounded-xl font-semibold text-sm text-navy-600 bg-navy-600/10 hover:bg-navy-600/20 transition-all">
                {{ __('Open Messages') }}
            </a>
        </div>
    </div>
</div>
@endsection
