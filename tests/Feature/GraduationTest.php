<?php

namespace Tests\Feature;

use App\Enums\StudentStatus;
use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Filament\SchoolAdmin\Pages\GraduationManagement;
use App\Models\AcademicYear;
use App\Models\AlumniProfile;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Graduation turns selected active students into alumni exactly once and
// shows them in the public alumni directory.
class GraduationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private Classroom $classroom;

    private Student $first;

    private Student $second;

    private Student $third;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['slug' => 'lulus', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'lulus', 'demo.enabled' => false]);
        Tenant::setCurrent($this->tenant);

        $grade = GradeLevel::create(['name' => 'Kelas 9', 'level' => 9, 'sort_order' => 9]);
        $year = AcademicYear::create(['name' => '2026/2027', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(10), 'is_active' => true]);
        $this->classroom = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '9A', 'capacity' => 32]);

        [$this->first, $this->second, $this->third] = collect(range(1, 3))->map(fn () => Student::factory()->create([
            'tenant_id' => $this->tenant->id,
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $year->id,
            'status' => StudentStatus::ACTIVE,
        ]))->all();

        $admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::SCHOOL_ADMIN, 'is_active' => true]);
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('school-admin'));
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_selected_students_become_alumni_once(): void
    {
        Livewire::test(GraduationManagement::class)
            ->set('classroom_id', $this->classroom->id)
            ->call('loadStudents')
            ->set('graduation_year', (int) date('Y'))
            ->set('selected_students', [$this->first->id, $this->second->id])
            ->call('graduate')
            ->assertHasNoErrors();

        $this->assertSame(StudentStatus::ALUMNI, $this->first->fresh()->status);
        $this->assertSame((int) date('Y'), (int) $this->first->fresh()->graduation_year);
        $this->assertSame(StudentStatus::ACTIVE, $this->third->fresh()->status);
        $this->assertSame(2, AlumniProfile::count());

        // Running again with an already graduated student does not duplicate it.
        Livewire::test(GraduationManagement::class)
            ->set('graduation_year', (int) date('Y'))
            ->set('selected_students', [$this->first->id, $this->third->id])
            ->call('graduate');

        $this->assertSame(3, AlumniProfile::count());
        $this->assertSame(1, AlumniProfile::where('student_id', $this->first->id)->count());

        Tenant::forgetCurrent();
        $this->get(route('alumni.index'))->assertOk();
    }

    public function test_invalid_graduation_year_is_rejected(): void
    {
        Livewire::test(GraduationManagement::class)
            ->set('graduation_year', 1990)
            ->set('selected_students', [$this->first->id])
            ->call('graduate')
            ->assertHasErrors('graduation_year');

        $this->assertSame(StudentStatus::ACTIVE, $this->first->fresh()->status);
        $this->assertSame(0, AlumniProfile::count());
    }
}
