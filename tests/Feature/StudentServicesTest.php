<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\AcademicYear;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\CounselingNote;
use App\Models\GradeLevel;
use App\Models\LeaveRequest;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentViolation;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ViolationType;
use App\Services\LeaveRequestService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

// Leave requests, savings wallet and discipline/counseling visibility.
class StudentServicesTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Student $student;

    private Student $otherStudent;

    private User $parentUser;

    private User $teacherUser;

    private AttendanceSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->tenant = Tenant::factory()->create(['slug' => 'services', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'services']);
        Tenant::setCurrent($this->tenant);

        $year = AcademicYear::create(['name' => '2026/2027', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(10), 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(4), 'is_active' => true]);
        $grade = GradeLevel::create(['name' => 'Kelas 7', 'level' => 7, 'sort_order' => 7]);

        $this->teacherUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $teacher = Teacher::create(['user_id' => $this->teacherUser->id, 'full_name' => 'Wali Kelas', 'gender' => 'P']);
        $classroom = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7A', 'capacity' => 32, 'homeroom_teacher_id' => $teacher->id]);
        $subject = Subject::create(['name' => 'IPA', 'code' => 'IPA']);
        $cs = ClassroomSubject::create(['classroom_id' => $classroom->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id, 'hours_per_week' => 4]);

        $this->student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $classroom->id, 'academic_year_id' => $year->id]);
        $this->otherStudent = Student::factory()->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $classroom->id, 'academic_year_id' => $year->id]);
        $this->session = AttendanceSession::create(['classroom_subject_id' => $cs->id, 'teacher_id' => $teacher->id, 'date' => today()->addDay(), 'start_time' => '07:00', 'end_time' => '08:20', 'status' => 'open']);

        $this->parentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true, 'email' => 'parent@services.test']);
        StudentParent::create(['student_id' => $this->student->id, 'relation' => 'ibu', 'name' => 'Ibu', 'email' => 'parent@services.test']);

        Tenant::forgetCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_parent_submits_leave_request_and_approval_marks_attendance(): void
    {
        $this->actingAs($this->parentUser)->post(route('parent.leave-requests.store'), [
            'student_id' => $this->student->id,
            'type' => 'sakit',
            'start_date' => today()->addDay()->toDateString(),
            'end_date' => today()->addDay()->toDateString(),
            'reason' => 'Demam',
            'attachment' => UploadedFile::fake()->create('surat.pdf', 50, 'application/pdf'),
        ])->assertRedirect();

        Tenant::setCurrent($this->tenant);
        $request = LeaveRequest::firstOrFail();
        $this->assertSame('pending', $request->status);
        Storage::disk('local')->assertExists($request->attachment);

        $updated = app(LeaveRequestService::class)->approve($request, $this->teacherUser);

        $this->assertSame(1, $updated);
        $this->assertDatabaseHas('student_attendances', [
            'attendance_session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => AttendanceStatus::SAKIT->value,
        ]);
    }

    public function test_parent_cannot_request_leave_for_another_child(): void
    {
        $this->actingAs($this->parentUser)->post(route('parent.leave-requests.store'), [
            'student_id' => $this->otherStudent->id,
            'type' => 'izin',
            'start_date' => today()->toDateString(),
            'end_date' => today()->toDateString(),
            'reason' => 'x',
        ])->assertForbidden();
    }

    public function test_attachment_is_private_to_staff_homeroom_and_requester(): void
    {
        Tenant::setCurrent($this->tenant);
        Storage::disk('local')->put('leave-requests/a.pdf', 'pdf');
        $request = LeaveRequest::create(['student_id' => $this->student->id, 'requested_by' => $this->parentUser->id, 'type' => 'izin', 'start_date' => today(), 'end_date' => today(), 'reason' => 'x', 'attachment' => 'leave-requests/a.pdf']);
        $stranger = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true]);
        Tenant::forgetCurrent();

        $this->actingAs($this->teacherUser)->get(route('leave-requests.attachment', $request))->assertOk();
        Tenant::forgetCurrent();
        $this->actingAs($stranger)->get(route('leave-requests.attachment', $request))->assertForbidden();
    }

    public function test_savings_balance_is_running_and_cannot_go_negative(): void
    {
        Tenant::setCurrent($this->tenant);
        $service = app(SavingsService::class);

        $service->record($this->student, 'deposit', 50000, userId: $this->teacherUser->id);
        $service->record($this->student, 'purchase', 12000, merchant: 'Kantin', userId: $this->teacherUser->id);

        $this->assertSame(38000.0, $service->balance($this->student));

        $this->expectException(ValidationException::class);
        $service->record($this->student, 'withdrawal', 40000, userId: $this->teacherUser->id);
    }

    public function test_parent_portal_hides_confidential_and_hidden_records(): void
    {
        Tenant::setCurrent($this->tenant);
        $type = ViolationType::create(['name' => 'Terlambat', 'severity' => 'light', 'points' => 5]);
        StudentViolation::create(['student_id' => $this->student->id, 'violation_type_id' => $type->id, 'occurred_at' => today(), 'points' => 5, 'description' => 'Terlambat 20 menit', 'visible_to_parent' => true]);
        StudentViolation::create(['student_id' => $this->student->id, 'violation_type_id' => $type->id, 'occurred_at' => today(), 'points' => 5, 'description' => 'Catatan internal', 'visible_to_parent' => false]);
        CounselingNote::create(['student_id' => $this->student->id, 'session_date' => today(), 'category' => 'academic', 'summary' => 'Motivasi belajar dibagikan', 'is_confidential' => false]);
        CounselingNote::create(['student_id' => $this->student->id, 'session_date' => today(), 'category' => 'personal', 'summary' => 'Rahasia keluarga', 'is_confidential' => true]);
        Tenant::forgetCurrent();

        $this->actingAs($this->parentUser)->get(route('parent.discipline', $this->student))
            ->assertOk()
            ->assertSee('Terlambat 20 menit')
            ->assertSee('Motivasi belajar dibagikan')
            ->assertDontSee('Catatan internal')
            ->assertDontSee('Rahasia keluarga');
    }

    public function test_new_portal_pages_render(): void
    {
        foreach (['leave-requests', 'savings', 'discipline'] as $page) {
            Tenant::forgetCurrent();
            $this->actingAs($this->parentUser)->get(route('parent.' . $page, $this->student))->assertOk();
        }
    }
}
