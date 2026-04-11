@extends('parent-portal.layout')

@section('title', __('Pay Tuition'))
@section('page-title', __('Tuition Bills'))

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

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">{{ __('Total Bills') }}</p>
            <p class="text-2xl font-heading font-bold text-navy-700">{{ \App\Helpers\CurrencyHelper::format($totalBills ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">{{ __('Already Paid') }}</p>
            <p class="text-2xl font-heading font-bold text-green-600">{{ \App\Helpers\CurrencyHelper::format($totalPaid ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">{{ __('Unpaid') }}</p>
            <p class="text-2xl font-heading font-bold text-red-600">{{ \App\Helpers\CurrencyHelper::format($totalUnpaid ?? 0) }}</p>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-heading font-bold text-navy-700">{{ __('Bill List') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-navy-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-navy-700">{{ __('Period') }}</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-700">{{ __('Type') }}</th>
                        <th class="px-6 py-3 text-right font-semibold text-navy-700">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left font-semibold text-navy-700">{{ __('Due Date') }}</th>
                        <th class="px-6 py-3 text-center font-semibold text-navy-700">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-center font-semibold text-navy-700">{{ __('Actions') }}</th>
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
                            <a href="{{ route('parent.spp.pay', $bill->id) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-gold-500 text-white text-xs font-semibold rounded-lg hover:bg-gold-600 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                {{ __('Pay') }}
                            </a>
                            @else
                            <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            {{ __('No tuition bills.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
