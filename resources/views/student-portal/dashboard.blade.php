@extends('student-portal.layout')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-navy-600 to-navy-700 rounded-2xl p-6 sm:p-8 text-white">
        <h2 class="text-xl sm:text-2xl font-heading font-bold mb-1">Selamat Datang, {{ $student->nickname ?? $student->full_name ?? 'Siswa' }}! 👋</h2>
        <p class="text-white/60 text-sm">{{ now()->translatedFormat('l, d F Y') }} — {{ $student->classroom->name ?? '' }}</p>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Attendance This Month --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">Kehadiran Bulan Ini</span>
            </div>
            <div class="text-2xl font-heading font-bold text-gray-800">{{ $attendanceSummary['percentage'] ?? 0 }}%</div>
            <p class="text-xs text-gray-400 mt-1">{{ $attendanceSummary['hadir'] ?? 0 }} dari {{ $attendanceSummary['total'] ?? 0 }} hari</p>
        </div>

        {{-- Latest Grade --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">Rata-rata Nilai</span>
            </div>
            <div class="text-2xl font-heading font-bold text-gray-800">{{ $averageGrade ?? '-' }}</div>
            <p class="text-xs text-gray-400 mt-1">Semester ini</p>
        </div>

        {{-- SPP Status --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg {{ ($sppStatus['is_paid'] ?? false) ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ ($sppStatus['is_paid'] ?? false) ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">SPP Bulan Ini</span>
            </div>
            <div class="text-2xl font-heading font-bold {{ ($sppStatus['is_paid'] ?? false) ? 'text-green-600' : 'text-red-600' }}">
                {{ ($sppStatus['is_paid'] ?? false) ? 'Lunas' : 'Belum Bayar' }}
            </div>
            <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>

        {{-- Announcements --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-400 uppercase">Pengumuman</span>
            </div>
            <div class="text-2xl font-heading font-bold text-gray-800">{{ $unreadAnnouncements ?? 0 }}</div>
            <p class="text-xs text-gray-400 mt-1">Belum dibaca</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Today's Schedule --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-base font-heading font-bold text-navy-600 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Jadwal Hari Ini
            </h3>
            <div class="space-y-3">
                @forelse($todaySchedule ?? [] as $schedule)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                    <div class="text-center min-w-[60px]">
                        <div class="text-xs text-gray-400">{{ $schedule['start_time'] ?? '' }}</div>
                        <div class="text-xs text-gray-400">{{ $schedule['end_time'] ?? '' }}</div>
                    </div>
                    <div class="w-1 h-10 rounded-full bg-navy-600"></div>
                    <div>
                        <div class="font-semibold text-sm text-gray-800">{{ $schedule['subject_name'] ?? '' }}</div>
                        <div class="text-xs text-gray-400">{{ $schedule['teacher_name'] ?? '' }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Tidak ada jadwal hari ini
                </div>
                @endforelse
            </div>
        </div>

        {{-- Latest Grades --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-base font-heading font-bold text-navy-600 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Nilai Terbaru
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
                <div class="text-center py-8 text-gray-400 text-sm">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Belum ada nilai
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Latest Announcements --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6">
        <h3 class="text-base font-heading font-bold text-navy-600 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Pengumuman Terbaru
        </h3>
        <div class="space-y-3">
            @forelse($latestAnnouncements ?? [] as $announcement)
            <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="font-semibold text-sm text-gray-800">{{ $announcement->title ?? '' }}</h4>
                        <p class="text-xs text-gray-400 mt-1">{{ Str::limit($announcement->content ?? '', 120) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $announcement->published_at?->diffForHumans() ?? '' }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400 text-sm">Tidak ada pengumuman terbaru</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
