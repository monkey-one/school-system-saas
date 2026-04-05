<?php

namespace App\Console\Commands;

use App\Enums\TenantStatus;
use App\Jobs\GenerateMonthlyBills;
use App\Models\Tenant;
use Illuminate\Console\Command;

class GenerateMonthlyBillsCommand extends Command
{
    protected $signature = 'edusaas:generate-monthly-bills {--period=}';

    protected $description = 'Generate tagihan SPP bulanan untuk semua tenant';

    public function handle(): int
    {
        $period = $this->option('period') ?: now()->format('Y-m');

        $tenants = Tenant::where('status', TenantStatus::ACTIVE)->get();

        $this->info("Generating monthly bills for period {$period} across {$tenants->count()} active tenants...");

        foreach ($tenants as $tenant) {
            GenerateMonthlyBills::dispatch($tenant->id, $period);
            $this->line(" → Tenant: {$tenant->name} (#{$tenant->id})");
        }

        $this->info('Done. Jobs dispatched to queue.');

        return self::SUCCESS;
    }
}
