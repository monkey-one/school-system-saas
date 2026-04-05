<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.xendit.secret_key', '');
        $this->baseUrl = 'https://api.xendit.co';
    }

    public function createInvoice(array $params): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . '/v2/invoices', $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit create invoice failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Xendit error: ' . $e->getMessage());
            return null;
        }
    }

    public function createPaymentLink(string $externalId, int $amount, string $description, string $payerEmail): ?array
    {
        return $this->createInvoice([
            'external_id' => $externalId,
            'amount' => $amount,
            'description' => $description,
            'payer_email' => $payerEmail,
            'currency' => 'IDR',
            'should_send_email' => true,
        ]);
    }

    public function verifyWebhookToken(string $token): bool
    {
        $webhookToken = config('services.xendit.webhook_token', '');
        return hash_equals($webhookToken, $token);
    }
}
