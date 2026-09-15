@extends('portal.layout')

@section('title', __('Bills & Payments'))

@section('content')
@php $money = fn ($value) => \App\Helpers\CurrencyHelper::format($value); @endphp

<section class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Total billed') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold text-navy-700">{{ $money($summary['total']) }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Paid') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold text-green-600">{{ $money($summary['paid']) }}</p>
    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
        <p class="text-xs font-medium uppercase text-gray-500">{{ __('Outstanding') }}</p>
        <p class="mt-2 font-heading text-2xl font-bold {{ $summary['outstanding'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $money($summary['outstanding']) }}</p>
    </div>
</section>

@unless ($gatewayReady)
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        {{ __('Online payment is not available yet. Please pay at the school finance office.') }}
    </div>
@endunless

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <h3 class="border-b border-gray-100 px-5 py-4 font-heading font-bold text-navy-700">{{ __('Bills') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('Bill') }}</th>
                    <th class="px-5 py-3">{{ __('Due date') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Amount') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Outstanding') }}</th>
                    <th class="px-5 py-3">{{ __('Status') }}</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bills as $bill)
                    @php $due = $overview->outstanding($bill); @endphp
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $bill->sppType?->name ?? __('Tuition Fee') }}</p>
                            <p class="text-xs text-gray-500">{{ $bill->period }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 {{ $due > 0 && $bill->due_date?->isPast() ? 'font-semibold text-red-600' : '' }}">{{ $bill->due_date?->translatedFormat('d M Y') }}</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">{{ $money($bill->final_amount) }}</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right font-semibold">{{ $money($due) }}</td>
                        <td class="px-5 py-3">@include('portal.partials.badge', ['label' => $bill->status->label(), 'color' => $bill->status->color()])</td>
                        <td class="px-5 py-3 text-right">
                            @if ($due > 0 && $gatewayReady)
                                <form method="POST" action="{{ route($portal . '.bills.pay', $bill) }}">
                                    @csrf
                                    <button class="rounded-lg bg-gold-500 px-3 py-1.5 text-xs font-bold text-navy-800 hover:bg-gold-400">{{ __('Pay now') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">{{ __('No bills.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <h3 class="border-b border-gray-100 px-5 py-4 font-heading font-bold text-navy-700">{{ __('Payment history') }}</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3">{{ __('Reference') }}</th>
                    <th class="px-5 py-3">{{ __('Date') }}</th>
                    <th class="px-5 py-3">{{ __('Method') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Amount') }}</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="px-5 py-3 font-mono text-xs">{{ $payment->reference_number }}</td>
                        <td class="whitespace-nowrap px-5 py-3">{{ $payment->payment_date?->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">{{ $payment->method?->label() }}</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right font-semibold">{{ $money($payment->amount) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if ($overview->isSettled($payment))
                                <a href="{{ route($portal . '.payments.receipt', $payment) }}" class="text-xs font-semibold text-navy-500 hover:underline">{{ __('Receipt') }}</a>
                            @else
                                @include('portal.partials.badge', ['label' => __('Waiting for payment'), 'color' => 'warning'])
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">{{ __('No payments yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
