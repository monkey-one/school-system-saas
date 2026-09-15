@extends('portal.layout')

@section('title', __('Online Payment'))

@push('head')
    <script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ $clientKey }}"></script>
@endpush

@section('content')
<section class="mx-auto max-w-lg rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">
    <p class="text-sm text-gray-500">{{ $bill->sppType?->name ?? __('Tuition Fee') }} · {{ $bill->period }}</p>
    <p class="mt-2 font-heading text-3xl font-bold text-navy-700">{{ \App\Helpers\CurrencyHelper::format($amount) }}</p>
    <p class="mt-1 text-sm text-gray-600">{{ $student->full_name }} · {{ $student->classroom?->name }}</p>

    <button id="pay-button" type="button" class="mt-6 w-full rounded-xl bg-gold-500 px-4 py-3 font-bold text-navy-800 hover:bg-gold-400">
        {{ __('Pay now') }}
    </button>
    <a href="{{ route($portal . '.bills', $childParam) }}" class="mt-3 inline-block text-sm text-gray-500 hover:underline">{{ __('Back to bills') }}</a>
    <p class="mt-4 text-xs text-gray-400">{{ __('Payments are processed securely by Midtrans (QRIS, e-wallet, virtual account, card).') }}</p>
</section>
@endsection

@push('scripts')
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        window.snap.pay(@js($snapToken), {
            onSuccess: () => window.location.href = @js(route($portal . '.bills', $childParam)),
            onPending: () => window.location.href = @js(route($portal . '.bills', $childParam)),
            onError: () => alert(@js(__('The payment failed. Please try again.'))),
        });
    });
</script>
@endpush
