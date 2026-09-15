<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Filament\Pages\Auth\Login;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\GradeLevel;
use App\Models\Message;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\SppType;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Student & parent portals: login redirects, every page renders, and a user
// can never reach another student's data.
class PortalTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private Student $student;
    private Student $otherStudent;
    private User $studentUser;
    private User $parentUser;
    private User $teacherUser;
    private ReportCard $reportCard;
    private SppBill $bill;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['slug' => 'portal-school', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'portal-school', 'services.midtrans.server_key' => '', 'services.midtrans.client_key' => '']);
        Tenant::setCurrent($this->tenant);

        $year = AcademicYear::create(['name' => '2026/2027', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(10), 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(4), 'is_active' => true]);
        $grade = GradeLevel::create(['name' => 'Kelas 7', 'level' => 7, 'sort_order' => 7]);

        $this->teacherUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $teacher = Teacher::create(['user_id' => $this->teacherUser->id, 'full_name' => 'Guru Wali', 'gender' => 'L']);

        $classroom = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7A', 'capacity' => 32, 'homeroom_teacher_id' => $teacher->id]);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK']);
        ClassroomSubject::create(['classroom_id' => $classroom->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id, 'hours_per_week' => 4]);

        $this->studentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        $this->student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'user_id' => $this->studentUser->id, 'classroom_id' => $classroom->id, 'academic_year_id' => $year->id]);
        $this->otherStudent = Student::factory()->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $classroom->id, 'academic_year_id' => $year->id]);

        $this->parentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true, 'email' => 'parent@portal.test']);
        StudentParent::create(['student_id' => $this->student->id, 'relation' => 'ayah', 'name' => 'Ayah', 'email' => 'parent@portal.test']);

        $type = SppType::create(['name' => 'SPP Bulanan', 'code' => 'SPP', 'amount' => 300000, 'frequency' => 'monthly']);
        $this->bill = SppBill::create(['student_id' => $this->student->id, 'spp_type_id' => $type->id, 'period' => now()->format('Y-m'), 'amount' => 300000, 'discount_amount' => 0, 'final_amount' => 300000, 'due_date' => now()->addDays(5), 'status' => PaymentStatus::UNPAID]);

        $this->reportCard = ReportCard::create(['student_id' => $this->otherStudent->id, 'semester_id' => $semester->id, 'classroom_id' => $classroom->id, 'status' => 'published', 'published_at' => now()]);

        Tenant::forgetCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_student_and_parent_are_redirected_to_their_portal_after_login(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('school-admin'));

        Livewire::test(Login::class)
            ->set('data.email', $this->studentUser->email)
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertRedirect(route('student.dashboard'));

        auth()->logout();

        Livewire::test(Login::class)
            ->set('data.email', $this->parentUser->email)
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertRedirect(route('parent.dashboard'));
    }

    public function test_every_student_portal_page_renders(): void
    {
        foreach (['dashboard', 'schedule', 'attendance', 'grades', 'report-cards', 'bills', 'activities', 'announcements', 'messages', 'profile'] as $page) {
            Tenant::forgetCurrent();
            $this->actingAs($this->studentUser)->get(route('student.' . $page))->assertOk();
        }
    }

    public function test_every_parent_portal_page_renders_for_own_child(): void
    {
        $this->actingAs($this->parentUser)->get(route('parent.dashboard'))->assertOk()->assertSee($this->student->full_name);

        foreach (['child', 'schedule', 'attendance', 'grades', 'report-cards', 'bills', 'activities'] as $page) {
            Tenant::forgetCurrent();
            $this->actingAs($this->parentUser)->get(route('parent.' . $page, $this->student))->assertOk();
        }
    }

    public function test_parent_cannot_open_a_child_that_is_not_theirs(): void
    {
        $this->actingAs($this->parentUser)->get(route('parent.grades', $this->otherStudent))->assertForbidden();
    }

    public function test_student_cannot_download_another_students_report_card(): void
    {
        $this->actingAs($this->studentUser)->get(route('student.report-cards.pdf', $this->reportCard))->assertForbidden();
    }

    public function test_other_user_types_cannot_open_the_portals(): void
    {
        $this->actingAs($this->teacherUser)->get(route('student.dashboard'))->assertForbidden();
        Tenant::forgetCurrent();
        $this->actingAs($this->studentUser)->get(route('parent.dashboard'))->assertForbidden();
    }

    public function test_paying_without_a_configured_gateway_explains_the_offline_option(): void
    {
        $this->actingAs($this->studentUser)
            ->from(route('student.bills'))
            ->post(route('student.bills.pay', $this->bill))
            ->assertRedirect(route('student.bills'))
            ->assertSessionHas('error');
    }

    public function test_student_can_message_homeroom_teacher_but_not_arbitrary_users(): void
    {
        $this->actingAs($this->studentUser)->post(route('student.messages.send'), [
            'recipient_id' => $this->teacherUser->id,
            'subject' => 'Izin',
            'content' => 'Saya izin sakit hari ini.',
        ])->assertRedirect();

        $this->assertDatabaseHas('messages', ['sender_id' => $this->studentUser->id, 'recipient_id' => $this->teacherUser->id]);

        Tenant::forgetCurrent();
        $this->actingAs($this->studentUser)->post(route('student.messages.send'), [
            'recipient_id' => $this->parentUser->id,
            'subject' => 'x',
            'content' => 'y',
        ])->assertSessionHasErrors('recipient_id');
    }

    public function test_message_threads_are_private(): void
    {
        $thread = Message::withoutGlobalScopes()->create([
            'tenant_id' => $this->tenant->id,
            'thread_id' => 'private-thread',
            'sender_id' => $this->parentUser->id,
            'recipient_id' => $this->teacherUser->id,
            'subject' => 'Rahasia',
            'content' => 'Hanya untuk wali kelas.',
        ]);

        $this->actingAs($this->studentUser)->get(route('student.messages.thread', $thread->thread_id))->assertNotFound();
    }
}
