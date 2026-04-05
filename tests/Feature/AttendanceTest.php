<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\TenantStatus;
use App\Models\AcademicYear;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\GradeLevel;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private AttendanceSession $session;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create([
            'slug' => 'test-school',
            'status' => TenantStatus::ACTIVE,
        ]);

        Tenant::setCurrent($this->tenant);

        $academicYear = AcademicYear::create([
            'tenant_id' => $this->tenant->id,
            'name' => '2025/2026',
            'starts_at' => '2025-07-14',
            'ends_at' => '2026-06-20',
            'is_active' => true,
        ]);

        $semester = Semester::create([
            'tenant_id' => $this->tenant->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Ganjil',
            'starts_at' => '2025-07-14',
            'ends_at' => '2025-12-20',
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

        $subject = Subject::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Matematika',
            'code' => 'MTK',
        ]);

        $teacherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $teacher = Teacher::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $teacherUser->id,
            'full_name' => 'Guru Test',
            'gender' => 'L',
        ]);

        $classroomSubject = ClassroomSubject::create([
            'tenant_id' => $this->tenant->id,
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $academicYear->id,
            'semester_id' => $semester->id,
            'hours_per_week' => 5,
        ]);

        $this->session = AttendanceSession::create([
            'tenant_id' => $this->tenant->id,
            'classroom_subject_id' => $classroomSubject->id,
            'teacher_id' => $teacher->id,
            'date' => today(),
            'start_time' => '07:00',
            'end_time' => '08:30',
            'qr_token' => Str::uuid()->toString(),
            'qr_expires_at' => now()->addMinutes(90),
            'qr_generated_at' => now(),
            'status' => 'open',
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

    public function test_qr_token_generation(): void
    {
        $this->assertNotNull($this->session->qr_token);
        $this->assertTrue(Str::isUuid($this->session->qr_token));
        $this->assertNotNull($this->session->qr_expires_at);
        $this->assertTrue($this->session->qr_expires_at->isFuture());
    }

    public function test_attendance_scan_with_valid_token(): void
    {
        $attendance = StudentAttendance::create([
            'tenant_id' => $this->tenant->id,
            'attendance_session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => AttendanceStatus::HADIR,
            'check_in_time' => now(),
            'method' => 'qr_scan',
        ]);

        $this->assertDatabaseHas('student_attendances', [
            'attendance_session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => AttendanceStatus::HADIR->value,
        ]);
    }

    public function test_attendance_scan_with_expired_token(): void
    {
        // Expire the token
        $this->session->update([
            'qr_expires_at' => now()->subMinutes(5),
        ]);

        $this->assertTrue($this->session->fresh()->qr_expires_at->isPast());
    }

    public function test_late_detection(): void
    {
        // Simulate scanning 20 minutes after session start (07:00)
        $checkInTime = Carbon::parse($this->session->date->format('Y-m-d') . ' 07:20');

        $startTime = Carbon::parse($this->session->date->format('Y-m-d') . ' ' . $this->session->start_time);
        $diffMinutes = $startTime->diffInMinutes($checkInTime);
        $lateThreshold = 15;

        $status = $diffMinutes > $lateThreshold ? AttendanceStatus::TERLAMBAT : AttendanceStatus::HADIR;

        $attendance = StudentAttendance::create([
            'tenant_id' => $this->tenant->id,
            'attendance_session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => $status,
            'check_in_time' => $checkInTime,
            'method' => 'qr_scan',
        ]);

        $this->assertEquals(AttendanceStatus::TERLAMBAT, $attendance->status);
    }
}
