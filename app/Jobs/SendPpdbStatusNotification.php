<?php

namespace App\Jobs;

use App\Models\PPDBRegistration;
use App\Models\Tenant;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

// Sends the "ppdb_status" WhatsApp template to the applicant's parent after
// the school reviews a registration. Runs inside the registration's school
// so the school's own template and logs are used.
class SendPpdbStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public int $registrationId) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        $registration = PPDBRegistration::withoutGlobalScopes()->find($this->registrationId);

        if (! $registration || blank($registration->parent_phone)) {
            return;
        }

        Tenant::setCurrent(Tenant::find($registration->tenant_id));

        try {
            $whatsApp->sendTemplate($registration->parent_phone, 'ppdb_status', [
                'parent_name' => $registration->parent_name,
                'registration_number' => $registration->registration_number,
                'student_name' => $registration->full_name,
                'status' => $registration->status->label(),
            ], 'ppdb_registration', $registration->id);
        } finally {
            Tenant::forgetCurrent();
        }
    }
}
