<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Helpers\CurrencyHelper;
use App\Models\SppBill;
use App\Models\StudentParent;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

// Marks a school's past-due bills as OVERDUE and sends ONE WhatsApp reminder
// per student (spp_reminder template) listing every overdue period and the
// total amount, instead of one message per bill.
class SendOverdueSppReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public int $tenantId,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (! $tenant) {
            return;
        }

        // Queue workers have no request tenant: use this school's scope,
        // currency and notification template.
        Tenant::setCurrent($tenant);

        try {
            $this->remind($whatsApp);
        } finally {
            Tenant::forgetCurrent();
        }
    }

    private function remind(WhatsAppService $whatsApp): void
    {
        $bills = SppBill::query()
            ->whereIn('status', [PaymentStatus::UNPAID, PaymentStatus::PARTIAL, PaymentStatus::OVERDUE])
            ->whereDate('due_date', '<', today())
            ->with('student')
            ->orderBy('due_date')
            ->get();

        SppBill::whereKey($bills->where('status', PaymentStatus::UNPAID)->pluck('id'))->update(['status' => PaymentStatus::OVERDUE]);

        $sent = 0;

        foreach ($bills->groupBy('student_id') as $studentId => $studentBills) {
            $parent = StudentParent::where('student_id', $studentId)
                ->where('is_whatsapp_active', true)
                ->whereNotNull('phone')
                ->orderByDesc('is_emergency_contact')
                ->first();

            if (! $parent || ! $studentBills->first()->student) {
                continue;
            }

            // Placeholders of the spp_reminder template.
            $whatsApp->sendTemplate($parent->phone, 'spp_reminder', [
                'student_name' => $studentBills->first()->student->full_name,
                'period' => $studentBills->pluck('period')->implode(', '),
                'amount' => CurrencyHelper::format($studentBills->sum('final_amount')),
                'due_date' => $studentBills->first()->due_date->format('d/m/Y'),
            ], 'spp_bill', $studentBills->first()->id);

            $sent++;
        }

        Log::info("SendOverdueSppReminders: Tenant #{$this->tenantId} — {$sent} reminders for {$bills->count()} overdue bills");
    }
}
