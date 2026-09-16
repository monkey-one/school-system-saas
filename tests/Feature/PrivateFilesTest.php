<?php

namespace Tests\Feature;

use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\StudentParent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Student papers must never be reachable by URL guessing: they are stored on
// the private disk and served only to staff, the student and their parents.
class PrivateFilesTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Student $student;

    private StudentDocument $document;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        $this->tenant = Tenant::factory()->create(['slug' => 'berkas', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'berkas', 'demo.enabled' => false]);
        Tenant::setCurrent($this->tenant);

        $studentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        $this->student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'user_id' => $studentUser->id]);

        Storage::disk('local')->put('students/documents/kk.pdf', 'rahasia');
        $this->document = StudentDocument::create([
            'student_id' => $this->student->id,
            'type' => 'kartu_keluarga',
            'file_path' => 'students/documents/kk.pdf',
            'file_name' => 'Kartu Keluarga',
        ]);

        Tenant::forgetCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_staff_student_and_own_parent_can_download(): void
    {
        $admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::SCHOOL_ADMIN, 'is_active' => true]);
        $this->actingAs($admin)->get(route('students.documents.attachment', $this->document))->assertOk();
        Tenant::forgetCurrent();

        $this->actingAs($this->student->user)->get(route('students.documents.attachment', $this->document))->assertOk();
        Tenant::forgetCurrent();

        $parent = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true, 'email' => 'ayah@berkas.test']);
        Tenant::setCurrent($this->tenant);
        StudentParent::create(['student_id' => $this->student->id, 'relation' => 'ayah', 'name' => 'Ayah', 'email' => 'ayah@berkas.test']);
        Tenant::forgetCurrent();

        $this->actingAs($parent)->get(route('students.documents.attachment', $this->document))->assertOk();
    }

    public function test_other_parents_and_teachers_are_refused(): void
    {
        $stranger = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true, 'email' => 'lain@berkas.test']);
        $this->actingAs($stranger)->get(route('students.documents.attachment', $this->document))->assertForbidden();
        Tenant::forgetCurrent();

        $teacher = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $this->actingAs($teacher)->get(route('students.documents.attachment', $this->document))->assertForbidden();
        Tenant::forgetCurrent();

        $this->get(route('students.documents.attachment', $this->document))->assertRedirect();
    }

    public function test_missing_file_returns_not_found(): void
    {
        Tenant::setCurrent($this->tenant);
        Storage::disk('local')->delete('students/documents/kk.pdf');
        $admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::SCHOOL_ADMIN, 'is_active' => true]);
        Tenant::forgetCurrent();

        $this->actingAs($admin)->get(route('students.documents.attachment', $this->document))->assertNotFound();
    }

    // The upload fields themselves must target the private disk, otherwise the
    // files would be written into the web-readable storage folder again.
    public function test_sensitive_upload_fields_use_the_private_disk(): void
    {
        $sources = [
            'app/Filament/SchoolAdmin/Resources/StudentResource/RelationManagers/DocumentsRelationManager.php' => "students/documents",
            'app/Filament/SchoolAdmin/Resources/PaymentResource.php' => "payments/receipts",
            'app/Filament/SchoolAdmin/Resources/AnnouncementResource.php' => "announcements/attachments",
        ];

        foreach ($sources as $file => $directory) {
            $code = file_get_contents(base_path($file));
            $position = strpos($code, "->directory('{$directory}')");

            $this->assertNotFalse($position, "{$file} no longer uploads into {$directory}");
            $this->assertStringContainsString("->disk('local')", substr($code, max(0, $position - 400), 800), "{$file} must upload {$directory} to the private disk");
        }
    }
}
