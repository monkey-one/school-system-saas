<?php

namespace Tests\Feature;

use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\AcademicYear;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\GradeLevel;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Services\QRCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Regression tests for the security hardening: tenant pinning, webhook
// verification, API scoping, QR attendance ownership and security headers.
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_school_user_stays_in_own_school_whatever_the_url_says(): void
    {
        $schoolA = Tenant::factory()->create(['slug' => 'school-a']);
        $schoolB = Tenant::factory()->create(['slug' => 'school-b']);
        config(['app.default_tenant_slug' => 'school-a']);

        $adminB = User::factory()->create([
            'tenant_id' => $schoolB->id,
            'type' => UserType::SCHOOL_ADMIN,
            'is_active' => true,
        ]);

        $this->actingAs($adminB)->get('/edusaas-admin?tenant=school-a')->assertOk();

        $this->assertSame($schoolB->id, Tenant::current()?->id);
    }

    public function test_midtrans_webhook_rejects_forged_signature_when_server_key_is_empty(): void
    {
        config(['services.midtrans.server_key' => '']);
        app()->forgetInstance(\App\Services\MidtransService::class);

        $payload = ['order_id' => 'SPP-1-x', 'status_code' => '200', 'gross_amount' => '100000.00', 'transaction_status' => 'settlement'];
        $payload['signature_key'] = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount']);

        $this->postJson('/webhooks/midtrans', $payload)->assertStatus(403);
    }

    public function test_xendit_webhook_rejects_empty_token(): void
    {
        config(['services.xendit.webhook_token' => '']);

        $this->postJson('/webhooks/xendit', ['external_id' => 'x', 'status' => 'PAID'])->assertStatus(403);
    }

    public function test_api_cannot_read_student_of_another_school(): void
    {
        $schoolA = Tenant::factory()->create();
        $schoolB = Tenant::factory()->create();

        $teacherA = User::factory()->create(['tenant_id' => $schoolA->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $studentB = Student::factory()->create(['tenant_id' => $schoolB->id]);

        $token = $teacherA->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/students/' . $studentB->id)
            ->assertNotFound();
    }

    public function test_students_cannot_use_staff_api_endpoints(): void
    {
        $school = Tenant::factory()->create();
        $studentUser = User::factory()->create(['tenant_id' => $school->id, 'type' => UserType::STUDENT, 'is_active' => true]);

        $this->withHeader('Authorization', 'Bearer ' . $studentUser->createToken('t')->plainTextToken)
            ->getJson('/api/v1/students')
            ->assertForbidden();
    }

    public function test_student_cannot_check_in_to_another_class_session(): void
    {
        $school = Tenant::factory()->create(['status' => TenantStatus::ACTIVE]);
        Tenant::setCurrent($school);

        $year = AcademicYear::create(['tenant_id' => $school->id, 'name' => '2025/2026', 'starts_at' => '2025-07-14', 'ends_at' => '2026-06-20', 'is_active' => true]);
        $semester = Semester::create(['tenant_id' => $school->id, 'academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_at' => '2025-07-14', 'ends_at' => '2025-12-20', 'is_active' => true]);
        $grade = GradeLevel::create(['tenant_id' => $school->id, 'name' => 'Kelas 7', 'level' => 7, 'sort_order' => 7]);
        $classA = Classroom::create(['tenant_id' => $school->id, 'grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7A', 'capacity' => 32]);
        $classB = Classroom::create(['tenant_id' => $school->id, 'grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7B', 'capacity' => 32]);
        $subject = Subject::create(['tenant_id' => $school->id, 'name' => 'Matematika', 'code' => 'MTK']);
        $teacherUser = User::factory()->create(['tenant_id' => $school->id, 'type' => UserType::TEACHER]);
        $teacher = Teacher::create(['tenant_id' => $school->id, 'user_id' => $teacherUser->id, 'full_name' => 'Guru', 'gender' => 'L']);
        $csB = ClassroomSubject::create(['tenant_id' => $school->id, 'classroom_id' => $classB->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id, 'hours_per_week' => 4]);
        $session = AttendanceSession::create(['tenant_id' => $school->id, 'classroom_subject_id' => $csB->id, 'teacher_id' => $teacher->id, 'date' => today(), 'start_time' => '07:00', 'end_time' => '08:00', 'status' => 'open']);

        $studentUser = User::factory()->create(['tenant_id' => $school->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        Student::factory()->create(['tenant_id' => $school->id, 'user_id' => $studentUser->id, 'classroom_id' => $classA->id, 'academic_year_id' => $year->id]);

        $token = app(QRCodeService::class)->generateToken($teacher->id, $session->id);
        Tenant::forgetCurrent();

        $this->actingAs($studentUser)
            ->post('/attendance/confirm', ['token' => $token])
            ->assertOk()
            ->assertSee(__('This attendance session is not for your class.'));

        $this->assertDatabaseCount('student_attendances', 0);
    }

    public function test_responses_include_security_headers(): void
    {
        $this->get('/up')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }
}
