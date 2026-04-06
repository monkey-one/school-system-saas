<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\ReportCardSubject;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentGrade;
use App\Models\ClassroomSubject;
use App\Models\CurriculumSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

// Generates student report cards by aggregating grades, computing weighted
// averages based on curriculum settings, and producing a ReportCard record
// with per-subject breakdowns and an attendance summary.
class RaporService
{
    public function generateForStudent(Student $student, int $semesterId): ReportCard
    {
        $classroom = $student->classroom;
        $tenant = $student->tenant;

        $reportCard = ReportCard::updateOrCreate([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'semester_id' => $semesterId,
        ], [
            'classroom_id' => $classroom->id,
            'status' => 'draft',
        ]);

        $classroomSubjects = ClassroomSubject::where('classroom_id', $classroom->id)
            ->where('semester_id', $semesterId)
            ->with('subject')
            ->get();

        $curriculum = CurriculumSetting::where('academic_year_id', $student->academic_year_id)->first();
        $weights = $curriculum?->assessment_weights ?? [
            'TGS' => 30,
            'UH' => 30,
            'PTS' => 20,
            'PAS' => 20,
        ];

        foreach ($classroomSubjects as $cs) {
            $finalScore = $this->calculateFinalScore($student->id, $cs->id, $weights);
            $predicate = $this->getPredicate($finalScore);
            $letterGrade = $this->getLetterGrade($finalScore);

            $attendance = $this->getAttendanceSummary($student->id, $cs->id);

            ReportCardSubject::updateOrCreate([
                'tenant_id' => $tenant->id,
                'report_card_id' => $reportCard->id,
                'subject_id' => $cs->subject_id,
            ], [
                'final_score' => round($finalScore, 2),
                'letter_grade' => $letterGrade,
                'predicate' => $predicate,
                'description' => $this->getDescription($cs->subject->name, $predicate),
                'hadir' => $attendance['hadir'],
                'sakit' => $attendance['sakit'],
                'izin' => $attendance['izin'],
                'alfa' => $attendance['alfa'],
            ]);
        }

        return $reportCard->load('reportCardSubjects.subject');
    }

    protected function calculateFinalScore(int $studentId, int $classroomSubjectId, array $weights): float
    {
        $grades = StudentGrade::whereHas('assessment', function ($q) use ($classroomSubjectId) {
            $q->where('classroom_subject_id', $classroomSubjectId);
        })->where('student_id', $studentId)
          ->with('assessment.assessmentType')
          ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $grouped = $grades->groupBy(fn ($g) => $g->assessment->assessmentType->code);
        $totalWeight = array_sum($weights);
        $finalScore = 0;

        foreach ($weights as $code => $weight) {
            $codeGrades = $grouped->get($code);
            if ($codeGrades && $codeGrades->count() > 0) {
                $avg = $codeGrades->avg(fn ($g) => $g->is_remedial && $g->remedial_score ? $g->remedial_score : $g->score);
                $finalScore += ($avg * $weight / $totalWeight);
            }
        }

        return $finalScore;
    }

    protected function getPredicate(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            default => 'D',
        };
    }

    protected function getLetterGrade(float $score): string
    {
        return match (true) {
            $score >= 93 => 'A',
            $score >= 90 => 'A-',
            $score >= 87 => 'B+',
            $score >= 83 => 'B',
            $score >= 80 => 'B-',
            $score >= 77 => 'C+',
            $score >= 73 => 'C',
            $score >= 70 => 'C-',
            $score >= 67 => 'D+',
            default => 'D',
        };
    }

    protected function getDescription(string $subjectName, string $predicate): string
    {
        return match ($predicate) {
            'A' => "Menunjukkan pemahaman yang sangat baik dalam {$subjectName}.",
            'B' => "Menunjukkan pemahaman yang baik dalam {$subjectName}.",
            'C' => "Menunjukkan pemahaman yang cukup dalam {$subjectName}. Perlu peningkatan.",
            'D' => "Perlu bimbingan lebih lanjut dalam {$subjectName}.",
        };
    }

    protected function getAttendanceSummary(int $studentId, int $classroomSubjectId): array
    {
        $attendances = StudentAttendance::where('student_id', $studentId)
            ->whereHas('attendanceSession', fn ($q) => $q->where('classroom_subject_id', $classroomSubjectId))
            ->get();

        return [
            'hadir' => $attendances->where('status', 'hadir')->count() + $attendances->where('status', 'terlambat')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'alfa' => $attendances->where('status', 'alfa')->count(),
        ];
    }

    public function generatePdf(ReportCard $reportCard): string
    {
        $reportCard->load([
            'student.classroom.grade',
            'student.tenant',
            'semester.academicYear',
            'reportCardSubjects.subject',
            'classroom.homeroomTeacher',
        ]);

        $pdf = Pdf::loadView('pdf.rapor', [
            'reportCard' => $reportCard,
            'student' => $reportCard->student,
            'tenant' => $reportCard->student->tenant,
            'semester' => $reportCard->semester,
        ])->setPaper('a4', 'portrait');

        $path = 'rapor/' . $reportCard->student->nis . '_' . $reportCard->semester_id . '.pdf';
        $fullPath = storage_path('app/public/' . $path);

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $pdf->save($fullPath);

        return $path;
    }

    public function batchGenerate(int $classroomId, int $semesterId): int
    {
        $students = Student::where('classroom_id', $classroomId)->get();
        $count = 0;

        foreach ($students as $student) {
            try {
                $this->generateForStudent($student, $semesterId);
                $count++;
            } catch (\Throwable $e) {
                Log::error("Rapor generation failed for student {$student->id}: " . $e->getMessage());
            }
        }

        return $count;
    }
}
