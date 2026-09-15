<?php

namespace Tests\Feature;

use App\Enums\TenantStatus;
use App\Enums\UserType;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;
use App\Models\ClassroomSubject;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamQuestion;
use App\Models\GradeLevel;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\StudentParent;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Models\User;
use App\Services\GradebookSync;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Assignments and online exams in the student and parent portals.
class ElearningTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $studentUser;

    private User $outsiderUser;

    private User $parentUser;

    private User $teacherUser;

    private Student $student;

    private Student $outsider;

    private Assignment $assignment;

    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        config(['demo.enabled' => false]);
        $this->tenant = Tenant::factory()->create(['slug' => 'elearn', 'status' => TenantStatus::ACTIVE]);
        config(['app.default_tenant_slug' => 'elearn']);
        Tenant::setCurrent($this->tenant);

        $year = AcademicYear::create(['name' => '2026/2027', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(10), 'is_active' => true]);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_at' => now()->subMonth(), 'ends_at' => now()->addMonths(4), 'is_active' => true]);
        $grade = GradeLevel::create(['name' => 'Kelas 7', 'level' => 7, 'sort_order' => 7]);

        $this->teacherUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::TEACHER, 'is_active' => true]);
        $teacher = Teacher::create(['user_id' => $this->teacherUser->id, 'full_name' => 'Guru MTK', 'gender' => 'L']);
        $classA = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7A', 'capacity' => 32]);
        $classB = Classroom::create(['grade_id' => $grade->id, 'academic_year_id' => $year->id, 'name' => '7B', 'capacity' => 32]);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK']);
        $cs = ClassroomSubject::create(['classroom_id' => $classA->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'academic_year_id' => $year->id, 'semester_id' => $semester->id, 'hours_per_week' => 4]);

        $this->studentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        $this->outsiderUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::STUDENT, 'is_active' => true]);
        $this->student = Student::factory()->create(['tenant_id' => $this->tenant->id, 'user_id' => $this->studentUser->id, 'classroom_id' => $classA->id, 'academic_year_id' => $year->id]);
        $this->outsider = Student::factory()->create(['tenant_id' => $this->tenant->id, 'user_id' => $this->outsiderUser->id, 'classroom_id' => $classB->id, 'academic_year_id' => $year->id]);

        $this->parentUser = User::factory()->create(['tenant_id' => $this->tenant->id, 'type' => UserType::PARENT, 'is_active' => true, 'email' => 'ortu@elearn.test']);
        StudentParent::create(['student_id' => $this->student->id, 'relation' => 'ayah', 'name' => 'Ayah', 'email' => 'ortu@elearn.test']);

        $this->assignment = Assignment::create([
            'classroom_subject_id' => $cs->id, 'teacher_id' => $teacher->id, 'title' => 'Latihan Pecahan',
            'instructions' => '<p>Kerjakan</p><script>alert(1)</script>', 'due_at' => now()->addDays(3), 'max_score' => 50, 'is_published' => true,
        ]);

        $this->exam = Exam::create([
            'classroom_subject_id' => $cs->id, 'teacher_id' => $teacher->id, 'title' => 'UH Pecahan',
            'starts_at' => now()->subHour(), 'ends_at' => now()->addHours(2), 'duration_minutes' => 30, 'shuffle_questions' => false, 'show_result' => true, 'is_published' => true,
        ]);

        foreach ([['1/2 + 1/2 = ...', ['A' => '1', 'B' => '2', 'C' => '0'], 'A'], ['2 x 3 = ...', ['A' => '5', 'B' => '6', 'C' => '9'], 'B']] as $n => [$q, $options, $correct]) {
            ExamQuestion::create(['exam_id' => $this->exam->id, 'question' => $q, 'options' => $options, 'correct_option' => $correct, 'points' => 1, 'sort_order' => $n]);
        }

        Tenant::forgetCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    public function test_student_submits_assignment_and_instructions_are_sanitized(): void
    {
        $this->actingAs($this->studentUser)->get(route('student.assignments'))->assertOk()->assertSee('Latihan Pecahan');
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->get(route('student.assignments.show', $this->assignment))
            ->assertOk()
            ->assertSee('Kerjakan')
            ->assertDontSee('<script>alert(1)</script>', false);
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $this->assignment), [
            'content' => 'Jawaban saya',
            'attachment' => UploadedFile::fake()->create('jawaban.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('student.assignments.show', $this->assignment));

        Tenant::setCurrent($this->tenant);
        $submission = AssignmentSubmission::firstOrFail();
        $this->assertSame($this->student->id, $submission->student_id);
        $this->assertFalse($submission->is_late);
        Storage::disk('local')->assertExists($submission->attachment);
    }

    public function test_graded_submission_cannot_be_replaced(): void
    {
        Tenant::setCurrent($this->tenant);
        AssignmentSubmission::create(['assignment_id' => $this->assignment->id, 'student_id' => $this->student->id, 'content' => 'awal', 'submitted_at' => now(), 'score' => 40]);
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $this->assignment), ['content' => 'ganti'])
            ->assertSessionHas('error');

        Tenant::setCurrent($this->tenant);
        $this->assertSame('awal', AssignmentSubmission::firstOrFail()->content);
    }

    public function test_demo_rejects_submission_files(): void
    {
        config(['demo.enabled' => true]);

        $this->actingAs($this->studentUser)->post(route('student.assignments.submit', $this->assignment), [
            'content' => 'x',
            'attachment' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
        ])->assertSessionHas('error');

        Tenant::setCurrent($this->tenant);
        $this->assertSame(0, AssignmentSubmission::count());
    }

    public function test_students_of_other_classes_cannot_open_or_download(): void
    {
        Tenant::setCurrent($this->tenant);
        Storage::disk('local')->put('submissions/a.pdf', 'pdf');
        $submission = AssignmentSubmission::create(['assignment_id' => $this->assignment->id, 'student_id' => $this->student->id, 'attachment' => 'submissions/a.pdf', 'submitted_at' => now()]);
        Tenant::forgetCurrent();

        $this->actingAs($this->outsiderUser)->get(route('student.assignments.show', $this->assignment))->assertNotFound();
        Tenant::forgetCurrent();
        $this->actingAs($this->outsiderUser)->post(route('student.assignments.submit', $this->assignment), ['content' => 'x'])->assertNotFound();
        Tenant::forgetCurrent();
        $this->actingAs($this->outsiderUser)->get(route('student.exams.take', $this->exam))->assertNotFound();
        Tenant::forgetCurrent();
        $this->actingAs($this->outsiderUser)->get(route('elearning.submissions.attachment', $submission))->assertForbidden();
        Tenant::forgetCurrent();
        $this->actingAs($this->parentUser)->get(route('elearning.submissions.attachment', $submission))->assertOk();
    }

    public function test_exam_flow_autosaves_grades_and_hides_answer_key(): void
    {
        $page = $this->actingAs($this->studentUser)->get(route('student.exams.take', $this->exam))->assertOk()->assertSee('1/2 + 1/2');
        $this->assertStringNotContainsString('correct_option', $page->getContent());
        Tenant::forgetCurrent();

        Tenant::setCurrent($this->tenant);
        $attempt = ExamAttempt::firstOrFail();
        [$q1, $q2] = ExamQuestion::orderBy('sort_order')->pluck('id')->all();
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->postJson(route('student.exams.save', $this->exam), ['answers' => [$q1 => 'A', $q2 => 'Z']])
            ->assertOk()->assertJson(['saved' => true]);
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->post(route('student.exams.submit', $this->exam), ['answers' => [$q1 => 'A', $q2 => 'B']])
            ->assertRedirect(route('student.exams.result', $this->exam));
        Tenant::forgetCurrent();

        Tenant::setCurrent($this->tenant);
        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at);
        $this->assertSame(2, $attempt->correct_count);
        $this->assertEquals(100, (float) $attempt->score);
        Tenant::forgetCurrent();

        // A submitted exam cannot be taken again; the result page is shown instead.
        $this->actingAs($this->studentUser)->get(route('student.exams.take', $this->exam))->assertRedirect(route('student.exams.result', $this->exam));
        Tenant::forgetCurrent();
        $this->actingAs($this->studentUser)->get(route('student.exams.result', $this->exam))->assertOk()->assertSee('100');
    }

    public function test_late_submission_keeps_only_answers_saved_before_the_deadline(): void
    {
        Tenant::setCurrent($this->tenant);
        [$q1, $q2] = ExamQuestion::orderBy('sort_order')->pluck('id')->all();
        $attempt = ExamAttempt::create(['exam_id' => $this->exam->id, 'student_id' => $this->student->id, 'started_at' => now()->subMinutes(45), 'answers' => [$q1 => 'A']]);
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->post(route('student.exams.submit', $this->exam), ['answers' => [$q1 => 'A', $q2 => 'B']])->assertRedirect();

        Tenant::setCurrent($this->tenant);
        $attempt->refresh();
        $this->assertSame(1, $attempt->correct_count);
        $this->assertEquals(50, (float) $attempt->score);
    }

    public function test_closed_exam_cannot_be_started(): void
    {
        Tenant::setCurrent($this->tenant);
        $this->exam->update(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHours(2)]);
        Tenant::forgetCurrent();

        $this->actingAs($this->studentUser)->get(route('student.exams.take', $this->exam))->assertRedirect(route('student.exams'));

        Tenant::setCurrent($this->tenant);
        $this->assertSame(0, ExamAttempt::count());
    }

    public function test_parent_sees_child_work_but_not_other_children(): void
    {
        $this->actingAs($this->parentUser)->get(route('parent.assignments', $this->student))->assertOk()->assertSee('Latihan Pecahan');
        Tenant::forgetCurrent();
        $this->actingAs($this->parentUser)->get(route('parent.exams', $this->student))->assertOk()->assertSee('UH Pecahan');
        Tenant::forgetCurrent();
        $this->actingAs($this->parentUser)->get(route('parent.assignments', $this->outsider))->assertForbidden();
        Tenant::forgetCurrent();
        // Parents cannot submit on behalf of the student.
        $this->actingAs($this->parentUser)->post(route('student.assignments.submit', $this->assignment), ['content' => 'x'])->assertForbidden();
    }

    public function test_scores_are_sent_to_the_gradebook(): void
    {
        Tenant::setCurrent($this->tenant);
        AssignmentSubmission::create(['assignment_id' => $this->assignment->id, 'student_id' => $this->student->id, 'content' => 'x', 'submitted_at' => now(), 'score' => 40]);

        $this->assertSame(1, app(GradebookSync::class)->syncAssignment($this->assignment->fresh(), $this->teacherUser->id));
        $this->assertSame(1, app(GradebookSync::class)->syncAssignment($this->assignment->fresh(), $this->teacherUser->id));

        $grade = StudentGrade::sole();
        $this->assertEquals(80, (float) $grade->score);
        $this->assertNotNull($this->assignment->fresh()->assessment_id);
    }
}
