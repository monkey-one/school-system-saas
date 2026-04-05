<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappLog;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $this->apiToken = config('services.fonnte.token', '');
    }

    public function send(string $phone, string $message, ?string $referenceType = null, ?int $referenceId = null): bool
    {
        if (empty($this->apiToken)) {
            Log::warning('WhatsApp: Fonnte API token not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            $success = $response->successful();

            WhatsappLog::create([
                'to_number' => $phone,
                'message' => $message,
                'status' => $success ? 'sent' : 'failed',
                'sent_at' => now(),
                'error_message' => $success ? null : $response->body(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            return $success;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());

            WhatsappLog::create([
                'to_number' => $phone,
                'message' => $message,
                'status' => 'failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);

            return false;
        }
    }

    public function sendTemplate(string $phone, string $templateKey, array $variables = [], ?string $refType = null, ?int $refId = null): bool
    {
        $template = \App\Models\NotificationTemplate::where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            Log::warning("WhatsApp: Template '{$templateKey}' not found or inactive");
            return false;
        }

        $message = $template->body;
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return $this->send($phone, $message, $refType, $refId);
    }

    public function blast(array $phones, string $message): int
    {
        $sent = 0;
        foreach ($phones as $phone) {
            if ($this->send($phone, $message)) {
                $sent++;
            }
            usleep(200000); // 200ms delay between messages
        }
        return $sent;
    }
}
