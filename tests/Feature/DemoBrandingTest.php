<?php

namespace Tests\Feature;

use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Demo credentials and the developer call-to-action must only appear on the
// public demo, never on a buyer's installation.
class DemoBrandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Tenant::factory()->create(['slug' => 'demo', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'demo']);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_installation_without_demo_mode_hides_credentials_and_cta(): void
    {
        config(['demo.enabled' => false]);

        $this->get('/edusaas-admin/login')->assertOk()
            ->assertDontSee('admin@smpn1demo.id')
            ->assertDontSee(__('Want this system for your school?'));

        $this->get('/')->assertOk()
            ->assertDontSee('superadmin@edusaas.id')
            ->assertDontSee('href="#demo"', false);
    }

    public function test_demo_mode_lists_accounts_and_shows_cta(): void
    {
        config(['demo.enabled' => true]);

        $this->get('/edusaas-admin/login')->assertOk()
            ->assertSee('admin@smpn1demo.id')
            ->assertSee('ortu@smpn1demo.id')
            ->assertSee('https://numintek.com', false);

        $this->get('/')->assertOk()
            ->assertSee('siswa@smpn1demo.id')
            ->assertSee('href="#demo"', false)
            ->assertSee('https://numintek.com', false);
    }

    public function test_developer_credit_is_configurable(): void
    {
        config(['demo.enabled' => false, 'demo.author.name' => '']);

        $this->get(route('website.home'))->assertOk()->assertDontSee(__('Developed by'));
    }
}
