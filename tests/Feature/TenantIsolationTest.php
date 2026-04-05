<?php

namespace Tests\Feature;

use App\Enums\StudentStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_data_is_isolated(): void
    {
        $tenant1 = Tenant::factory()->create([
            'name' => 'Sekolah A',
            'slug' => 'sekolah-a',
            'status' => TenantStatus::ACTIVE,
        ]);

        $tenant2 = Tenant::factory()->create([
            'name' => 'Sekolah B',
            'slug' => 'sekolah-b',
            'status' => TenantStatus::ACTIVE,
        ]);

        // Create students for tenant 1
        Student::factory()->count(3)->create([
            'tenant_id' => $tenant1->id,
            'status' => StudentStatus::ACTIVE,
        ]);

        // Create students for tenant 2
        Student::factory()->count(5)->create([
            'tenant_id' => $tenant2->id,
            'status' => StudentStatus::ACTIVE,
        ]);

        // Set tenant 1 as current and verify isolation
        Tenant::setCurrent($tenant1);
        $this->assertCount(3, Student::all());

        // Set tenant 2 as current and verify isolation
        Tenant::setCurrent($tenant2);
        $this->assertCount(5, Student::all());

        Tenant::forgetCurrent();
    }

    public function test_super_admin_can_see_all_tenants(): void
    {
        $superAdmin = User::factory()->create([
            'type' => UserType::SUPER_ADMIN,
            'is_active' => true,
        ]);

        Tenant::factory()->count(3)->create([
            'status' => TenantStatus::ACTIVE,
        ]);

        // Without tenant scope, all tenants should be visible
        Tenant::forgetCurrent();
        $this->assertCount(3, Tenant::all());
        $this->assertTrue($superAdmin->isSuperAdmin());
    }
}
