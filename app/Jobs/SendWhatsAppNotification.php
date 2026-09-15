<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

// Generic queued job for sending a single WhatsApp message. Runs inside the
// given school so the delivery log is stored for that school. Retries up to
// three times with a one-minute backoff on transient failures.
class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public string $phone,
        public string $message,
        public ?string $refType = null,
        public ?int $refId = null,
        public ?int $tenantId = null,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $previous = Tenant::current();

        if ($this->tenantId) {
            Tenant::setCurrent(Tenant::find($this->tenantId));
        }

        try {
            $whatsApp->send($this->phone, $this->message, $this->refType, $this->refId);
        } finally {
            Tenant::setCurrent($previous);
        }
    }
}
