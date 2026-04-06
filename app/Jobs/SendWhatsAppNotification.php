<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

// Generic queued job for sending a single WhatsApp message. Retries up to
// three times with a one-minute backoff on transient failures.
class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public string $phone,
        public string $message,
        public ?string $refType = null,
        public ?int $refId = null,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $whatsApp->send($this->phone, $this->message, $this->refType, $this->refId);
    }
}
