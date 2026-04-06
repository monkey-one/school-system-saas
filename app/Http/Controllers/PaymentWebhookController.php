<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\SppBill;
use App\Services\MidtransService;
use App\Services\WhatsAppService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Receives asynchronous payment notifications from Midtrans and Xendit.
// On successful payment the corresponding SPP bills are marked as paid,
// and an optional WhatsApp receipt is sent to the student's parent.
class PaymentWebhookController extends Controller
{
    // Midtrans sends a POST with a signature derived from the server key.
    // Invalid signatures are rejected with 403.
    public function midtrans(Request $request)
    {
        $notification = $request->all();

        $midtransService = app(MidtransService::class);

        if (! $midtransService->verifySignature($notification)) {
            Log::warning('Midtrans webhook: invalid signature', $notification);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $orderId = $notification['order_id'] ?? null;
        $transactionStatus = $notification['transaction_status'] ?? null;
        $transactionId = $notification['transaction_id'] ?? null;

        $payment = Payment::where('reference_number', $orderId)->first();

        if (! $payment) {
            Log::warning('Midtrans webhook: payment not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        DB::transaction(function () use ($payment, $transactionStatus, $transactionId, $notification) {
            $payment->update([
                'gateway_transaction_id' => $transactionId,
                'gateway_status' => $transactionStatus,
            ]);

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $this->markBillsPaid($payment);
                $this->sendPaymentReceipt($payment);
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
                $payment->update(['gateway_status' => $transactionStatus]);
            }
        });

        return response()->json(['message' => 'OK']);
    }

    // Xendit sends a POST with an x-callback-token header for verification.
    public function xendit(Request $request)
    {
        $callbackToken = $request->header('x-callback-token', '');

        $xenditService = app(XenditService::class);

        if (! $xenditService->verifyWebhookToken($callbackToken)) {
            Log::warning('Xendit webhook: invalid token');
            return response()->json(['message' => 'Invalid token'], 403);
        }

        $data = $request->all();
        $externalId = $data['external_id'] ?? null;
        $status = $data['status'] ?? null;

        $payment = Payment::where('reference_number', $externalId)->first();

        if (! $payment) {
            Log::warning('Xendit webhook: payment not found', ['external_id' => $externalId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        DB::transaction(function () use ($payment, $status, $data) {
            $payment->update([
                'gateway_transaction_id' => $data['id'] ?? null,
                'gateway_status' => strtolower($status),
            ]);

            if (strtolower($status) === 'paid') {
                $this->markBillsPaid($payment);
                $this->sendPaymentReceipt($payment);
            }
        });

        return response()->json(['message' => 'OK']);
    }

    // Mark all SPP bills linked to this payment as paid. When no explicit
    // bill allocations exist, auto-allocate the payment amount to unpaid
    // bills ordered by due date until the balance runs out.
    protected function markBillsPaid(Payment $payment): void
    {
        $allocations = PaymentBillAllocation::where('payment_id', $payment->id)->get();

        foreach ($allocations as $allocation) {
            $bill = SppBill::find($allocation->spp_bill_id);
            if ($bill) {
                $bill->update(['status' => PaymentStatus::PAID]);
            }
        }

        if ($allocations->isEmpty()) {
            $bills = SppBill::where('student_id', $payment->student_id)
                ->where('status', PaymentStatus::UNPAID)
                ->where('final_amount', '<=', $payment->amount)
                ->orderBy('due_date')
                ->get();

            $remaining = (float) $payment->amount;
            foreach ($bills as $bill) {
                if ($remaining >= (float) $bill->final_amount) {
                    $bill->update(['status' => PaymentStatus::PAID]);

                    PaymentBillAllocation::create([
                        'tenant_id' => $payment->tenant_id,
                        'payment_id' => $payment->id,
                        'spp_bill_id' => $bill->id,
                        'amount' => $bill->final_amount,
                    ]);

                    $remaining -= (float) $bill->final_amount;
                }
            }
        }
    }

    // Try to send a payment receipt to the student's parent via WhatsApp.
    // Silently returns when no phone number is available.
    protected function sendPaymentReceipt(Payment $payment): void
    {
        $student = $payment->student;
        if (! $student) {
            return;
        }

        // Prefer the parent's WhatsApp number; fall back to the student's phone.
        $parent = $student->parents()->where('is_whatsapp_active', true)->first();
        $phone = $parent?->phone ?? $student->phone;

        if (! $phone) {
            return;
        }

        $whatsapp = app(WhatsAppService::class);
        $whatsapp->sendTemplate($phone, 'payment_receipt', [
            'student_name' => $student->full_name,
            'amount' => number_format((float) $payment->amount, 0, ',', '.'),
            'reference' => $payment->reference_number,
            'date' => $payment->payment_date->format('d/m/Y'),
        ], 'payment', $payment->id);
    }
}
