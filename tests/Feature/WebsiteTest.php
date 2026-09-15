<?php

namespace Tests\Feature;

use App\Enums\PPDBStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\GalleryAlbum;
use App\Models\Post;
use App\Models\PPDBRegistration;
use App\Models\PPDBWave;
use App\Models\SchoolEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

// Public school website and PPDB flow.
class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private PPDBWave $wave;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['slug' => 'web-school', 'status' => TenantStatus::ACTIVE, 'description' => 'Sekolah contoh']);
        config(['app.default_tenant_slug' => 'web-school']);
        Tenant::setCurrent($this->tenant);

        $year = AcademicYear::create(['name' => '2027/2028', 'starts_at' => now()->addYear(), 'ends_at' => now()->addYears(2), 'is_active' => false]);
        $this->wave = PPDBWave::create(['academic_year_id' => $year->id, 'name' => 'Gelombang 1', 'quota_per_class' => 32, 'opens_at' => now()->subDay(), 'closes_at' => now()->addMonth(), 'is_active' => true, 'requirements' => ['Akta kelahiran']]);

        Post::create(['title' => 'Juara Olimpiade', 'content' => '<p>Selamat!</p><script>alert(1)</script><p onclick="x()">Bangga</p>', 'is_published' => true, 'published_at' => now()->subHour()]);
        Post::create(['title' => 'Draf Rahasia', 'content' => 'belum terbit', 'is_published' => false]);
        SchoolEvent::create(['title' => 'Rapat Komite', 'starts_at' => now()->addDays(3)]);
        Achievement::create(['title' => 'Olimpiade Sains', 'participant' => 'Budi', 'level' => 'national', 'category' => 'academic', 'achieved_at' => now()->subMonth()]);
        GalleryAlbum::create(['title' => 'Class Meeting', 'event_date' => now()->subWeek()]);

        Tenant::forgetCurrent();
        Storage::fake('local');
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_every_public_page_renders(): void
    {
        foreach (['website.home', 'website.about', 'website.news', 'website.agenda', 'website.achievements', 'website.gallery', 'website.teachers', 'website.contact', 'alumni.index', 'ppdb.index', 'ppdb.status'] as $route) {
            Tenant::forgetCurrent();
            $this->get(route($route))->assertOk();
        }

        Tenant::forgetCurrent();
        $this->get(route('website.sitemap'))->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_published_news_is_sanitized_and_drafts_are_hidden(): void
    {
        $response = $this->get(route('website.news.show', 'juara-olimpiade'))->assertOk();
        $response->assertSee('Selamat!', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertDontSee('onclick', false);

        Tenant::forgetCurrent();
        $this->get(route('website.news.show', 'draf-rahasia'))->assertNotFound();
    }

    public function test_ppdb_registration_with_documents_and_status_check(): void
    {
        $this->post(route('ppdb.store'), [
            'ppdb_wave_id' => $this->wave->id,
            'full_name' => 'Siti Aminah',
            'birth_date' => '2014-02-03',
            'gender' => 'P',
            'address' => 'Jl. Mawar 1',
            'parent_name' => 'Ahmad',
            'parent_phone' => '081234567890',
            'agreement' => '1',
            'documents' => [
                'birth_certificate' => UploadedFile::fake()->create('akta.pdf', 120, 'application/pdf'),
                'family_card' => UploadedFile::fake()->image('kk.jpg'),
                'photo' => UploadedFile::fake()->image('foto.png'),
            ],
        ])->assertRedirect();

        $registration = PPDBRegistration::withoutGlobalScopes()->where('full_name', 'Siti Aminah')->firstOrFail();
        $this->assertCount(3, $registration->documents);
        Storage::disk('local')->assertExists($registration->documents['photo']);

        Tenant::forgetCurrent();
        $this->post(route('ppdb.check-status'), ['registration_number' => $registration->registration_number, 'birth_date' => '2014-02-03'])
            ->assertOk()->assertSee('Siti Aminah');

        Tenant::forgetCurrent();
        $this->post(route('ppdb.check-status'), ['registration_number' => $registration->registration_number, 'birth_date' => '2010-01-01'])
            ->assertOk()->assertDontSee('Siti Aminah');
    }

    public function test_ppdb_rejects_executable_uploads(): void
    {
        $this->post(route('ppdb.store'), [
            'ppdb_wave_id' => $this->wave->id,
            'full_name' => 'Hacker',
            'birth_date' => '2014-02-03',
            'gender' => 'L',
            'address' => 'x',
            'parent_name' => 'x',
            'parent_phone' => '081234567890',
            'agreement' => '1',
            'documents' => [
                'birth_certificate' => UploadedFile::fake()->createWithContent('shell.php', '<?php system($_GET["c"]);'),
                'family_card' => UploadedFile::fake()->image('kk.jpg'),
                'photo' => UploadedFile::fake()->image('foto.png'),
            ],
        ])->assertSessionHasErrors('documents.birth_certificate');

        $this->assertDatabaseMissing('ppdb_registrations', ['full_name' => 'Hacker']);
    }

    public function test_acceptance_letter_requires_signature_and_documents_require_staff(): void
    {
        Tenant::setCurrent($this->tenant);
        $registration = PPDBRegistration::create(['ppdb_wave_id' => $this->wave->id, 'registration_number' => 'PPDB-TEST-1', 'full_name' => 'Diterima', 'birth_date' => '2014-01-01', 'gender' => 'L', 'parent_name' => 'x', 'parent_phone' => '0812', 'status' => PPDBStatus::ACCEPTED, 'documents' => ['photo' => 'ppdb/x.png']]);
        Tenant::forgetCurrent();

        $this->get(route('ppdb.acceptance-letter', $registration))->assertForbidden();

        Tenant::forgetCurrent();
        $this->get(URL::temporarySignedRoute('ppdb.acceptance-letter', now()->addHour(), ['registration' => $registration->id]))->assertOk();

        Tenant::forgetCurrent();
        $student = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        $this->actingAs($student)->get(route('ppdb.documents.show', [$registration, 'photo']))->assertForbidden();
    }
}
