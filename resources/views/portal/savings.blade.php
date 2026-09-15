@extends('portal.layout')

@section('title', __('Savings & Cashless'))

@section('content')
@php $money = fn ($value) => \App\Helpers\CurrencyHelper::format($value); @endphp

<section class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl bg-gradient-to-r from-navy-700 to-navy-500 p-6 text-white shadow-sm sm:col-span-1">
        <p class="text-sm text-white/70">{{ __('Current balance') }}</p>
        <p class="mt-2 font-heading text-3xl font-extrabold">{{ $money($balance) }}</p>
        <p class="mt-1 text-xs text-white/60">{{ $student->full_name }} · {{ $student->nis }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Deposits this month') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold text-green-600">{{ $money($monthIn) }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Spending this month') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold text-red-600">{{ $money($monthOut) }}</p>
    </div>
</section>

<p class="text-sm text-gray-600">{{ __('Deposits and withdrawals are made at the school finance office; cashless purchases at the school canteen and cooperative are deducted automatically.') }}</p>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('Date') }}</th>
                    <th class="px-5 py-3">{{ __('Transaction') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Amount') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transactions as $transaction)
                    <tr>
                        <td class="whitespace-nowrap px-5 py-3">{{ $transaction->transacted_at->translatedFormat('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ \App\Models\SavingsTransaction::typeLabels()[$transaction->type] ?? $transaction->type }}@if ($transaction->merchant) · {{ $transaction->merchant }}@endif</p>
                            @if ($transaction->description)<p class="text-xs text-gray-500">{{ $transaction->description }}</p>@endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right font-semibold {{ $transaction->isCredit() ? 'text-green-600' : 'text-red-600' }}">{{ $transaction->isCredit() ? '+' : '−' }} {{ $money($transaction->amount) }}</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">{{ $money($transaction->balance_after) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">{{ __('No savings transactions yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3">{{ $transactions->links() }}</div>
</section>
@endsection
