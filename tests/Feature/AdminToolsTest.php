<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Filament\SchoolAdmin\Pages\ClassPromotion;
use App\Filament\SchoolAdmin\Pages\FinanceReport;
use App\Filament\SchoolAdmin\Pages\WhatsAppBroadcast;
use App\Filament\SchoolAdmin\Widgets\StatsOverview;
use App\Filament\Teacher\Pages\MyCheckIn;
use App\Jobs\SendWhatsAppNotification;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Payment;
use App\Models\SppBill;
use App\Models\SppType;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

// School admin tools: class promotion, WhatsApp broadcast, finance report,
// teacher GPS check-in and the lobby TV display.
class AdminToolsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $admin;

    private Classroom $from;

    private Classroom $to;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['slug' => 'tools', 'status' => TenantStatus::ACTIVE, 'settings' => ['school_lat' => -6.1754, 'school_lng' => 106.8272, 'checkin_radius_m' => 200]]);
        config(['app.default_tenant_slug' => 'tools', 'demo.enabled' => false]);
        Tenant::setCurrent($this->tenant);

        $this->admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::SCHOOL_ADMIN, 'is_active' => true]);
        $grade = GradeLevel::create(['name' => 'Kelas 7', 'level' => 7, 'sort_order' => 7]);
        $year = AcademicYear::create(['name' => '2026/2027', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(10), 'is_active' => true]);
        $next = AcademicYear::create(['name' => '2027/2028', 'starts_at' => now()->addYear(), 'ends_at' => now()->addYears(2), 'is_active' => false]);
        $this->from = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7A', 'capacity' => 32]);
        $this->to = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $next->id, 'name' => '8A', 'capacity' => 32]);

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('school-admin'));
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_class_promotion_moves_only_selected_students(): void
    {
        [$a, $b] = Student::factory()->count(2)->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $this->from->id]);

        Livewire::test(ClassPromotion::class)
            ->set('data.source_classroom_id', $this->from->id)
            ->set('data.target_classroom_id', $this->to->id)
            ->set('selected', [(string) $a->id])
            ->call('promote')
            ->assertHasNoErrors();

        $this->assertSame($this->to->id, $a->fresh()->classroom_id);
        $this->assertSame($this->to->academic_year_id, $a->fresh()->academic_year_id);
        $this->assertSame($this->from->id, $b->fresh()->classroom_id);
    }

    public function test_broadcast_queues_one_message_per_number_and_is_blocked_in_demo(): void
    {
        Queue::fake();
        $student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $this->from->id]);
        StudentParent::create(['student_id' => $student->id, 'relation' => 'ayah', 'name' => 'Ayah', 'phone' => '0812-3456-7890', 'is_whatsapp_active' => true]);
        StudentParent::create(['student_id' => $student->id, 'relation' => 'ibu', 'name' => 'Ibu', 'phone' => '081234567890', 'is_whatsapp_active' => true]);

        Livewire::test(WhatsAppBroadcast::class)
            ->set('data.audience', 'classroom')
            ->set('data.classroom_id', $this->from->id)
            ->set('data.message', 'Halo {name}, info untuk {student_name}.')
            ->call('send');

        Queue::assertPushed(SendWhatsAppNotification::class, 1);
        Queue::assertPushed(SendWhatsAppNotification::class, fn ($job) => $job->phone === '6281234567890' && $job->tenantId === $this->tenant->id);

        config(['demo.enabled' => true]);
        Livewire::test(WhatsAppBroadcast::class)
            ->set('data.audience', 'parents')
            ->set('data.message', 'x')
            ->call('send');

        Queue::assertPushed(SendWhatsAppNotification::class, 1);
    }

    public function test_finance_report_counts_only_settled_payments_and_exports_csv(): void
    {
        $student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'classroom_id' => $this->from->id]);
        $type = SppType::create(['name' => 'SPP', 'code' => 'SPP', 'amount' => 300000, 'frequency' => 'monthly']);
        SppBill::create(['student_id' => $student->id, 'spp_type_id' => $type->id, 'period' => now()->format('Y-m'), 'amount' => 300000, 'discount_amount' => 0, 'final_amount' => 300000, 'due_date' => now(), 'status' => PaymentStatus::UNPAID]);
        Payment::create(['student_id' => $student->id, 'reference_number' => 'CASH-1', 'amount' => 100000, 'payment_date' => now(), 'method' => PaymentMethod::CASH]);
        Payment::create(['student_id' => $student->id, 'reference_number' => 'MID-1', 'amount' => 999000, 'payment_date' => now(), 'method' => PaymentMethod::MIDTRANS, 'gateway_status' => 'pending']);

        $page = Livewire::test(FinanceReport::class);
        $report = $page->instance()->getReport();

        $this->assertSame(100000.0, $report['collected']);
        $this->assertSame(300000.0, $report['outstanding']);

        $page->call('export')->assertFileDownloaded('laporan-keuangan-' . now()->format('Y-m') . '.csv');
    }

    public function test_dashboard_revenue_ignores_pending_gateway_payments(): void
    {
        $student = Student::factory()->create(['tenant_id' => $this->tenant->id]);
        Payment::create(['student_id' => $student->id, 'reference_number' => 'CASH-2', 'amount' => 250000, 'payment_date' => now(), 'method' => PaymentMethod::CASH]);
        Payment::create(['student_id' => $student->id, 'reference_number' => 'MID-2', 'amount' => 500000, 'payment_date' => now(), 'method' => PaymentMethod::MIDTRANS, 'gateway_status' => 'pending']);

        Livewire::test(StatsOverview::class)
            ->assertSee(\App\Helpers\CurrencyHelper::format(250000))
            ->assertDontSee(\App\Helpers\CurrencyHelper::format(750000));
    }

    public function test_teacher_check_in_respects_radius(): void
    {
        $teacherUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $teacher = Teacher::create(['user_id' => $teacherUser->id, 'full_name' => 'Guru', 'gender' => 'L']);
        $this->actingAs($teacherUser);
        Filament::setCurrentPanel(Filament::getPanel('teacher'));

        Livewire::test(MyCheckIn::class)->call('checkIn', -6.2500, 106.9000);
        $this->assertDatabaseCount('teacher_attendances', 0);

        Livewire::test(MyCheckIn::class)->call('checkIn', -6.1755, 106.8273);
        $attendance = TeacherAttendance::where('teacher_id', $teacher->id)->first();
        $this->assertNotNull($attendance);
        $this->assertContains($attendance->status, [AttendanceStatus::HADIR, AttendanceStatus::TERLAMBAT]);
        $this->assertSame('gps', $attendance->method);
    }

    public function test_lobby_display_is_public(): void
    {
        Tenant::forgetCurrent();
        auth()->logout();

        $this->get(route('display'))->assertOk()->assertSee($this->tenant->name);
    }
}
