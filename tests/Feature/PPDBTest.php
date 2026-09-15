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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

        // Public PPDB pages resolve the school from the default slug.
        config(['app.default_tenant_slug' => 'test-school']);
        Storage::fake('local');

        Tenant::setCurrent($this->tenant);

        $academicYear = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2027/2028',
            'starts_at' => now()->addMonths(10),
            'ends_at' => now()->addMonths(22),
            'is_active' => false,
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

        Tenant::forgetCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_ppdb_registration_form_accessible(): void
    {
        $this->get(route('ppdb.register', ['wave' => $this->wave->id]))->assertOk();
    }

    public function test_closed_wave_cannot_be_opened(): void
    {
        $this->wave->forceFill(['closes_at' => now()->subDay()])->saveQuietly();

        $this->get(route('ppdb.register', ['wave' => $this->wave->id]))->assertNotFound();
    }

    public function test_ppdb_registration_creates_record_with_generated_number(): void
    {
        $response = $this->post(route('ppdb.store'), [
            'ppdb_wave_id' => $this->wave->id,
            'full_name' => 'Budi Santoso',
            'birth_date' => '2013-05-15',
            'gender' => Gender::MALE->value,
            'parent_name' => 'Ahmad Santoso',
            'parent_phone' => '081234567890',
            'previous_school' => 'SD Negeri 1 Jakarta',
            'address' => 'Jl. Test No. 1, Jakarta',
            'agreement' => '1',
            'documents' => [
                'birth_certificate' => UploadedFile::fake()->create('akta.pdf', 100, 'application/pdf'),
                'family_card' => UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'),
                'photo' => UploadedFile::fake()->image('foto.jpg'),
            ],
        ]);

        $response->assertRedirect();

        $registration = PPDBRegistration::withoutGlobalScopes()->where('full_name', 'Budi Santoso')->firstOrFail();

        $this->assertSame($this->tenant->id, $registration->tenant_id);
        $this->assertMatchesRegularExpression('/^PPDB-\d{4}-\d+-\d{5}$/', $registration->registration_number);
        $this->assertSame(PPDBStatus::PENDING, $registration->status);
    }

    public function test_ppdb_status_check_requires_matching_birth_date(): void
    {
        Tenant::setCurrent($this->tenant);
        PPDBRegistration::create([
            'ppdb_wave_id' => $this->wave->id,
            'registration_number' => 'PPDB-2026-1-00001',
            'full_name' => 'Test Student',
            'birth_date' => '2013-01-01',
            'gender' => Gender::MALE,
            'parent_name' => 'Test Parent',
            'parent_phone' => '081234567890',
            'address' => 'Test Address',
            'status' => PPDBStatus::ACCEPTED,
        ]);
        Tenant::forgetCurrent();

        $this->post(route('ppdb.check-status'), ['registration_number' => 'PPDB-2026-1-00001', 'birth_date' => '2013-01-01'])
            ->assertOk()
            ->assertSee('Test Student');

        Tenant::forgetCurrent();
        $this->post(route('ppdb.check-status'), ['registration_number' => 'PPDB-2026-1-00001', 'birth_date' => '2012-12-31'])
            ->assertOk()
            ->assertDontSee('Test Student');
    }
}
