<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Models\SppBill;
use App\Models\StudentParent;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOverdueSppReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public int $tenantId,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $bills = SppBill::where('tenant_id', $this->tenantId)
            ->whereIn('status', [PaymentStatus::UNPAID, PaymentStatus::OVERDUE])
            ->where('due_date', '<', now())
            ->with(['student', 'sppType'])
            ->get();

        $sent = 0;

        foreach ($bills as $bill) {
            // Update status to overdue if still unpaid
            if ($bill->status === PaymentStatus::UNPAID) {
                $bill->update(['status' => PaymentStatus::OVERDUE]);
            }

            $parent = StudentParent::where('student_id', $bill->student_id)
                ->where('is_whatsapp_active', true)
                ->first();

            if (! $parent || empty($parent->phone)) {
                continue;
            }

            $whatsApp->sendTemplate($parent->phone, 'spp_reminder', [
                'nama_siswa' => $bill->student->full_name,
                'jenis_spp' => $bill->sppType->name,
                'periode' => $bill->period,
                'jumlah' => number_format($bill->final_amount, 0, ',', '.'),
                'jatuh_tempo' => $bill->due_date->format('d/m/Y'),
            ], 'spp_bill', $bill->id);

            $sent++;
        }

        Log::info("SendOverdueSppReminders: Tenant #{$this->tenantId} — sent {$sent} reminders for {$bills->count()} overdue bills");
    }
}
