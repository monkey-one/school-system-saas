@extends('student-portal.layout')

@section('title', 'Kehadiran')
@section('page-title', 'Kehadiran')

@section('content')
<div class="space-y-6">
    {{-- Month Selector --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-lg font-heading font-bold text-navy-600">Rekap Kehadiran Bulanan</h2>
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $month ?? now()->format('Y-m') }}"
                       class="rounded-lg border-gray-300 text-sm focus:ring-navy-500 focus:border-navy-500">
                <button type="submit" class="px-4 py-2 bg-navy-600 text-white text-sm rounded-lg hover:bg-navy-700 transition">
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $summary = [
                ['label' => 'Hadir', 'count' => $stats['hadir'] ?? 0, 'color' => 'text-green-600 bg-green-50'],
                ['label' => 'Sakit', 'count' => $stats['sakit'] ?? 0, 'color' => 'text-yellow-600 bg-yellow-50'],
                ['label' => 'Izin', 'count' => $stats['izin'] ?? 0, 'color' => 'text-blue-600 bg-blue-50'],
                ['label' => 'Alfa', 'count' => $stats['alfa'] ?? 0, 'color' => 'text-red-600 bg-red-50'],
                ['label' => 'Terlambat', 'count' => $stats['terlambat'] ?? 0, 'color' => 'text-orange-600 bg-orange-50'],
            ];
        @endphp
        @foreach($summary as $item)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $item['color'] }} mb-2">
                <span class="text-lg font-bold">{{ $item['count'] }}</span>
            </div>
            <p class="text-xs text-gray-500 font-medium">{{ $item['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Tanggal</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Hari</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Jam Masuk</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances ?? [] as $attendance)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">{{ $attendance->attendanceSession->date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $attendance->attendanceSession->date?->translatedFormat('l') ?? '-' }}</td>
                        <td class="px-6 py-3">
                            @php
                                $statusColors = [
                                    'hadir' => 'bg-green-100 text-green-700',
                                    'sakit' => 'bg-yellow-100 text-yellow-700',
                                    'izin' => 'bg-blue-100 text-blue-700',
                                    'alfa' => 'bg-red-100 text-red-700',
                                    'terlambat' => 'bg-orange-100 text-orange-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$attendance->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $attendance->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $attendance->check_in_time?->format('H:i') ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            Belum ada data kehadiran untuk bulan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
