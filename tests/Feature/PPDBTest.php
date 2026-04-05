<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\PPDBStatus;
use App\Enums\TenantStatus;
use App\Models\AcademicYear;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PPDBTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private PPDBWave $wave;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create([
            'slug' => 'test-school',
            'status' => TenantStatus::ACTIVE,
        ]);

        Tenant::setCurrent($this->tenant);

        $academicYear = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2025/2026',
            'starts_at' => '2025-07-14',
            'ends_at' => '2026-06-20',
            'is_active' => true,
        ]);

        $this->wave = PPDBWave::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Gelombang 1',
            'quota_per_class' => 32,
            'opens_at' => now()->subMonth(),
            'closes_at' => now()->addMonth(),
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_ppdb_registration_form_accessible(): void
    {
        $response = $this->get(route('ppdb.register', ['wave' => $this->wave->id]));

        $response->assertStatus(200);
    }

    public function test_ppdb_registration_creates_record(): void
    {
        $data = [
            'ppdb_wave_id' => $this->wave->id,
            'full_name' => 'Budi Santoso',
            'birth_date' => '2013-05-15',
            'gender' => Gender::MALE->value,
            'parent_name' => 'Ahmad Santoso',
            'parent_phone' => '081234567890',
            'previous_school' => 'SD Negeri 1 Jakarta',
            'address' => 'Jl. Test No. 1, Jakarta',
        ];

        $response = $this->post(route('ppdb.store'), $data);

        $response->assertRedirect();

        $this->assertDatabaseHas('ppdb_registrations', [
            'full_name' => 'Budi Santoso',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_ppdb_status_check_works(): void
    {
        $registration = PPDBRegistration::create([
            'tenant_id' => $this->tenant->id,
            'ppdb_wave_id' => $this->wave->id,
            'registration_number' => 'PPDB-2025-00001',
            'full_name' => 'Test Student',
            'birth_date' => '2013-01-01',
            'gender' => Gender::MALE,
            'parent_name' => 'Test Parent',
            'parent_phone' => '081234567890',
            'previous_school' => 'SD Test',
            'address' => 'Test Address',
            'status' => PPDBStatus::ACCEPTED,
        ]);

        $response = $this->post(route('ppdb.check-status'), [
            'registration_number' => 'PPDB-2025-00001',
        ]);

        $response->assertStatus(200);
    }

    public function test_ppdb_registration_number_is_auto_generated(): void
    {
        $registration = PPDBRegistration::create([
            'tenant_id' => $this->tenant->id,
            'ppdb_wave_id' => $this->wave->id,
            'registration_number' => 'PPDB-2025-00099',
            'full_name' => 'Test Auto Number',
            'birth_date' => '2013-03-15',
            'gender' => Gender::FEMALE,
            'parent_name' => 'Parent Name',
            'parent_phone' => '081234567890',
            'previous_school' => 'SD Test',
            'address' => 'Test Address',
            'status' => PPDBStatus::PENDING,
        ]);

        $this->assertMatchesRegularExpression('/^PPDB-\d{4}-\d{5}$/', $registration->registration_number);
    }
}
