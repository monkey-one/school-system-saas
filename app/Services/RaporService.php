<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\ReportCardSubject;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentGrade;
use App\Models\ClassroomSubject;
use App\Models\CurriculumSetting;
use App\Models\Semester;
use App\Models\StudentExtracurricular;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $weightedSum = 0;
        $usedWeight = 0;

        // Only assessment types that already have grades count, so a
        // mid-semester report (e.g. before PAS) is not dragged down by
        // the weight of assessments that have not taken place yet.
        foreach ($weights as $code => $weight) {
            $codeGrades = $grouped->get($code);
            if ($codeGrades && $codeGrades->count() > 0) {
                $avg = $codeGrades->avg(fn ($g) => $g->is_remedial && $g->remedial_score ? $g->remedial_score : $g->score);
                $weightedSum += $avg * $weight;
                $usedWeight += $weight;
            }
        }

        return $usedWeight > 0 ? $weightedSum / $usedWeight : 0;
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

    // Everything resources/views/pdf/rapor.blade.php expects.
    public function pdfData(ReportCard $reportCard): array
    {
        $reportCard->loadMissing([
            'student.tenant',
            'student.classroom',
            'semester.academicYear',
            'reportCardSubjects.subject',
            'classroom.homeroomTeacher',
        ]);

        $student = $reportCard->student;
        $semester = $reportCard->semester;
        $classroom = $reportCard->classroom ?? $student->classroom;

        $grades = $reportCard->reportCardSubjects
            ->sortBy(fn (ReportCardSubject $row) => $row->subject?->name)
            ->values()
            ->map(fn (ReportCardSubject $row) => [
                'subject_name' => $row->subject?->name,
                'score' => number_format((float) $row->final_score, 0),
                'predicate' => $row->predicate,
                'description' => $row->description,
            ])
            ->all();

        $extracurriculars = StudentExtracurricular::with('extracurricular')
            ->where('student_id', $student->id)
            ->when($semester?->academic_year_id, fn ($q, $yearId) => $q->where('academic_year_id', $yearId))
            ->get()
            ->map(fn (StudentExtracurricular $row) => [
                'name' => $row->extracurricular?->name,
                'predicate' => $row->score,
            ])
            ->all();

        return [
            'school' => $student->tenant,
            'student' => $student,
            'classroom' => $classroom,
            'semester' => $semester?->name,
            'academicYear' => $semester?->academicYear,
            'grades' => $grades,
            'attendance' => $this->semesterAttendance($student, $semester),
            'extracurriculars' => $extracurriculars,
            'homeroomComment' => $reportCard->homeroom_comment,
            'principalComment' => $reportCard->principal_comment,
            'homeroomTeacher' => $classroom?->homeroomTeacher,
            'principalNip' => $student->tenant->settings['principal_nip'] ?? null,
            'reportDate' => ($reportCard->published_at ?? now())->translatedFormat('d F Y'),
        ];
    }

    public function pdf(ReportCard $reportCard): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.rapor', $this->pdfData($reportCard))->setPaper('a4', 'portrait');
    }

    public function download(ReportCard $reportCard)
    {
        $reportCard->loadMissing('student', 'semester');
        $name = Str::slug('rapor-' . $reportCard->student->nis . '-' . $reportCard->student->full_name . '-' . $reportCard->semester?->name);

        return $this->pdf($reportCard)->download($name . '.pdf');
    }

    public function generatePdf(ReportCard $reportCard): string
    {
        $path = 'rapor/' . $reportCard->student->nis . '_' . $reportCard->semester_id . '.pdf';

        Storage::disk('local')->put($path, $this->pdf($reportCard)->output());

        return $path;
    }

    // Days per attendance status within the semester. A day counts once per
    // status even when the student attended several sessions that day.
    protected function semesterAttendance(Student $student, ?Semester $semester): array
    {
        $rows = StudentAttendance::query()
            ->join('attendance_sessions', 'attendance_sessions.id', '=', 'student_attendances.attendance_session_id')
            ->where('student_attendances.student_id', $student->id)
            ->when($semester, fn ($q) => $q->whereBetween('attendance_sessions.date', [$semester->starts_at, $semester->ends_at]))
            ->selectRaw('student_attendances.status, COUNT(DISTINCT attendance_sessions.date) as days')
            ->groupBy('student_attendances.status')
            ->pluck('days', 'status');

        return [
            'hadir' => (int) ($rows['hadir'] ?? 0) + (int) ($rows['terlambat'] ?? 0),
            'sakit' => (int) ($rows['sakit'] ?? 0),
            'izin' => (int) ($rows['izin'] ?? 0),
            'alfa' => (int) ($rows['alfa'] ?? 0),
        ];
    }

    public function batchGenerate(int $classroomId, int $semesterId): int
    {
        $students = Student::where('classroom_id', $classroomId)
            ->where('status', \App\Enums\StudentStatus::ACTIVE)
            ->get();
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
