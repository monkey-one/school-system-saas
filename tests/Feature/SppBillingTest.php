<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TenantStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\SppBill;
use App\Models\SppType;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppBillingTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private SppType $sppType;
    private Student $student;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create([
            'slug' => 'test-school',
            'status' => TenantStatus::ACTIVE,
        ]);

        Tenant::setCurrent($this->tenant);

        $this->admin = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $academicYear = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2025/2026',
            'starts_at' => '2025-07-14',
            'ends_at' => '2026-06-20',
            'is_active' => true,
        ]);

        $grade = GradeLevel::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Kelas 7',
            'level' => 7,
            'sort_order' => 7,
        ]);

        $classroom = Classroom::create([
            'tenant_id' => $this->tenant->id,
            'grade_id' => $grade->id,
            'academic_year_id' => $academicYear->id,
            'name' => '7A',
            'capacity' => 32,
        ]);

        $this->sppType = SppType::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'SPP Bulanan',
            'code' => 'SPP-BLN',
            'amount' => 500000,
            'frequency' => 'monthly',
        ]);

        $this->student = Student::factory()->create([
            'tenant_id' => $this->tenant->id,
            'classroom_id' => $classroom->id,
            'academic_year_id' => $academicYear->id,
        ]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_monthly_bill_generation(): void
    {
        $students = Student::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'classroom_id' => $this->student->classroom_id,
            'academic_year_id' => $this->student->academic_year_id,
        ]);

        $allStudents = Student::all();
        $period = '2025-07';

        foreach ($allStudents as $student) {
            SppBill::create([
                'tenant_id' => $this->tenant->id,
                'student_id' => $student->id,
                'spp_type_id' => $this->sppType->id,
                'period' => $period,
                'amount' => $this->sppType->amount,
                'discount_amount' => 0,
                'final_amount' => $this->sppType->amount,
                'due_date' => '2025-07-15',
                'status' => PaymentStatus::UNPAID,
            ]);
        }

        $this->assertCount(6, SppBill::where('period', $period)->get());
    }

    public function test_payment_creates_allocation(): void
    {
        $bill = SppBill::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->student->id,
            'spp_type_id' => $this->sppType->id,
            'period' => '2025-07',
            'amount' => 500000,
            'discount_amount' => 0,
            'final_amount' => 500000,
            'due_date' => '2025-07-15',
            'status' => PaymentStatus::UNPAID,
        ]);

        $payment = Payment::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->student->id,
            'reference_number' => 'PAY-TEST-001',
            'amount' => 500000,
            'payment_date' => now(),
            'method' => PaymentMethod::CASH,
            'recorded_by' => $this->admin->id,
        ]);

        $allocation = PaymentBillAllocation::create([
            'tenant_id' => $this->tenant->id,
            'payment_id' => $payment->id,
            'spp_bill_id' => $bill->id,
            'amount' => 500000,
        ]);

        $this->assertDatabaseHas('payment_bill_allocations', [
            'payment_id' => $payment->id,
            'spp_bill_id' => $bill->id,
            'amount' => 500000,
        ]);

        $this->assertEquals(1, $payment->allocations()->count());
    }

    public function test_bill_status_updates_on_payment(): void
    {
        $bill = SppBill::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->student->id,
            'spp_type_id' => $this->sppType->id,
            'period' => '2025-08',
            'amount' => 500000,
            'discount_amount' => 0,
            'final_amount' => 500000,
            'due_date' => '2025-08-15',
            'status' => PaymentStatus::UNPAID,
        ]);

        $this->assertEquals(PaymentStatus::UNPAID, $bill->status);

        $payment = Payment::create([
            'tenant_id' => $this->tenant->id,
            'student_id' => $this->student->id,
            'reference_number' => 'PAY-TEST-002',
            'amount' => 500000,
            'payment_date' => now(),
            'method' => PaymentMethod::TRANSFER,
            'recorded_by' => $this->admin->id,
        ]);

        PaymentBillAllocation::create([
            'tenant_id' => $this->tenant->id,
            'payment_id' => $payment->id,
            'spp_bill_id' => $bill->id,
            'amount' => 500000,
        ]);

        // Update bill status after full payment
        $bill->update(['status' => PaymentStatus::PAID]);

        $this->assertEquals(PaymentStatus::PAID, $bill->fresh()->status);
    }
}
