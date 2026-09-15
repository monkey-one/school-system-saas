<?php

namespace App\Console\Commands;

use App\Enums\TenantStatus;
use App\Jobs\SendOverdueSppReminders;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SendSppReminders extends Command
{
    protected $signature = 'edusaas:send-spp-reminders';

    protected $description = 'Kirim pengingat SPP yang belum dibayar via WhatsApp';

    public function handle(): int
    {
        // Trial schools use the product fully, so they get reminders too.
        $tenants = Tenant::whereIn('status', [TenantStatus::ACTIVE, TenantStatus::TRIAL])->get();

        $this->info("Dispatching SPP reminders for {$tenants->count()} schools...");

        foreach ($tenants as $tenant) {
            SendOverdueSppReminders::dispatch($tenant->id);
            $this->line(" → Tenant: {$tenant->name} (#{$tenant->id})");
        }

        $this->info('Done. Jobs dispatched to queue.');

        return self::SUCCESS;
    }
}
