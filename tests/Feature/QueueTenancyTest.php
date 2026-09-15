<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Queued jobs (Filament imports/exports, notifications, WhatsApp) must run
// inside the school that dispatched them and must not change the tenant of
// the request when executed synchronously.
class QueueTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_job_runs_in_dispatching_tenant_and_request_tenant_is_restored(): void
    {
        $schoolA = Tenant::factory()->create();
        $schoolB = Tenant::factory()->create();
        Student::factory()->count(2)->create(['tenant_id' => $schoolA->id]);
        Student::factory()->count(5)->create(['tenant_id' => $schoolB->id]);

        Tenant::setCurrent($schoolA);
        RecordTenantJob::dispatch();

        $this->assertSame($schoolA->id, RecordTenantJob::$tenantId);
        $this->assertSame(2, RecordTenantJob::$students);
        $this->assertSame($schoolA->id, Tenant::current()?->id);
    }

    public function test_job_dispatched_without_tenant_stays_unscoped(): void
    {
        Tenant::factory()->create();
        Tenant::forgetCurrent();

        RecordTenantJob::dispatch();

        $this->assertNull(RecordTenantJob::$tenantId);
        $this->assertNull(Tenant::current());
    }
}

class RecordTenantJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public static ?int $tenantId = null;

    public static int $students = 0;

    public function handle(): void
    {
        self::$tenantId = Tenant::current()?->id;
        self::$students = Student::count();
    }
}
