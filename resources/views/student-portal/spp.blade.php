@extends('student-portal.layout')

@section('title', 'SPP')
@section('page-title', 'Tagihan SPP')

@section('content')
<div class="space-y-6">
    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
            <p class="text-2xl font-heading font-bold text-navy-600">{{ \App\Helpers\CurrencyHelper::format($totalBills ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Sudah Dibayar</p>
            <p class="text-2xl font-heading font-bold text-green-600">{{ \App\Helpers\CurrencyHelper::format($totalPaid ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Belum Dibayar</p>
            <p class="text-2xl font-heading font-bold text-red-600">{{ \App\Helpers\CurrencyHelper::format($totalUnpaid ?? 0) }}</p>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-heading font-bold text-navy-600">Daftar Tagihan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Periode</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Jenis</th>
                        <th class="px-6 py-3 text-right font-semibold text-navy-600">Jumlah</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-600">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-center font-semibold text-navy-600">Status</th>
                        <th class="px-6 py-3 text-center font-semibold text-navy-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bills ?? [] as $bill)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-medium">{{ $bill->period }}</td>
                        <td class="px-6 py-3">{{ $bill->sppType->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-right font-semibold">{{ \App\Helpers\CurrencyHelper::format($bill->final_amount) }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $bill->due_date->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-center">
                            @php
                                $statusColors = [
                                    'unpaid' => 'bg-yellow-100 text-yellow-700',
                                    'partial' => 'bg-blue-100 text-blue-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'overdue' => 'bg-red-100 text-red-700',
                                    'waived' => 'bg-gray-100 text-gray-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $bill->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if(in_array($bill->status->value, ['unpaid', 'overdue', 'partial']))
                            <a href="{{ route('student.spp.pay', $bill->id) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gold-500 text-white text-xs font-semibold rounded-lg hover:bg-gold-600 transition">
                                Bayar
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            Belum ada tagihan SPP.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
