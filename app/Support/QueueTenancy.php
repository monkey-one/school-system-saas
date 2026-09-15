<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

// Makes every queued job run inside the school that dispatched it: the
// current tenant ID is stored in the job payload and restored when a worker
// processes the job (Filament imports/exports and database notifications,
// WhatsApp jobs, ...). The previous tenant is put back afterwards, so jobs
// executed synchronously inside a web request never change its tenant.
final class QueueTenancy
{
    private const PAYLOAD_KEY = 'edusaas_tenant_id';

    /** @var array<int, Tenant|null> */
    private static array $previous = [];

    public static function register(): void
    {
        Queue::createPayloadUsing(fn () => [self::PAYLOAD_KEY => Tenant::current()?->id]);

        Event::listen(JobProcessing::class, function (JobProcessing $event) {
            self::$previous[] = Tenant::current();

            $tenantId = $event->job->payload()[self::PAYLOAD_KEY] ?? null;

            if ($tenantId) {
                Tenant::setCurrent(Tenant::find($tenantId));
            }
        });

        Event::listen([JobProcessed::class, JobFailed::class, JobExceptionOccurred::class], function () {
            if (self::$previous !== []) {
                Tenant::setCurrent(array_pop(self::$previous));
            }
        });
    }
}
