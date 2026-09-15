<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\Semester;
use App\Models\StudentGrade;
use Illuminate\Database\Eloquent\Model;

// Copies graded assignment submissions and exam attempts into the gradebook
// (StudentGrade), creating one linked Assessment per assignment/exam so the
// scores flow into report cards. Running it again updates existing grades.
class GradebookSync
{
    public function syncAssignment(Assignment $assignment, int $userId): int
    {
        $assessment = $this->assessmentFor($assignment, 'TGS', $assignment->due_at);
        $count = 0;

        foreach ($assignment->submissions()->whereNotNull('score')->get() as $submission) {
            $this->grade($assessment, $submission->student_id, (float) $submission->score / max(1, $assignment->max_score) * 100, $userId);
            $count++;
        }

        return $count;
    }

    public function syncExam(Exam $exam, int $userId): int
    {
        $assessment = $this->assessmentFor($exam, 'UH', $exam->starts_at);
        $count = 0;

        foreach ($exam->attempts()->whereNotNull('submitted_at')->get() as $attempt) {
            $this->grade($assessment, $attempt->student_id, (float) $attempt->score, $userId);
            $count++;
        }

        return $count;
    }

    private function assessmentFor(Assignment | Exam $source, string $typeCode, $date): Assessment
    {
        if ($source->assessment) {
            return $source->assessment;
        }

        $type = AssessmentType::where('code', $typeCode)->first()
            ?? AssessmentType::create(['code' => $typeCode, 'name' => $typeCode === 'TGS' ? 'Tugas' : 'Ulangan Harian', 'default_weight' => 25, 'count_for_final' => true]);

        $assessment = Assessment::create([
            'classroom_subject_id' => $source->classroom_subject_id,
            'assessment_type_id' => $type->id,
            'semester_id' => $source->classroomSubject?->semester_id ?? Semester::where('is_active', true)->value('id'),
            'name' => $source->title,
            'date' => $date,
            'max_score' => 100,
        ]);

        $source->forceFill(['assessment_id' => $assessment->id])->save();

        return $assessment;
    }

    private function grade(Assessment $assessment, int $studentId, float $score, int $userId): Model
    {
        return StudentGrade::updateOrCreate(
            ['assessment_id' => $assessment->id, 'student_id' => $studentId],
            ['tenant_id' => $assessment->tenant_id, 'score' => round(min(100, max(0, $score)), 2), 'graded_by' => $userId],
        );
    }
}
