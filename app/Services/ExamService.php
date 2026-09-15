<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// Exam attempts: a student starts once inside the exam window; answers are
// accepted until the personal deadline (plus a short network grace period)
// and graded automatically against the correct options.
class ExamService
{
    private const GRACE_SECONDS = 60;

    public function start(Exam $exam, Student $student): ExamAttempt
    {
        $existing = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->first();

        if ($existing) {
            return $existing;
        }

        if (! $exam->isOpen()) {
            throw ValidationException::withMessages(['exam' => __('This exam is not open.')]);
        }

        $order = $exam->questions()->pluck('id');

        return ExamAttempt::create([
            'tenant_id' => $exam->tenant_id,
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'answers' => [],
            'question_order' => ($exam->shuffle_questions ? $order->shuffle() : $order)->values()->all(),
        ]);
    }

    // Autosave while the exam is running, so answers given before the
    // deadline still count if the browser closes or time runs out.
    public function saveProgress(ExamAttempt $attempt, array $answers): void
    {
        if ($attempt->isSubmitted() || now()->greaterThan($attempt->deadline())) {
            return;
        }

        $valid = $attempt->exam->questions()->pluck('id')->all();

        $attempt->update([
            'answers' => collect($answers)
                ->only($valid)
                ->map(fn ($answer) => strtoupper((string) $answer))
                ->filter(fn ($answer) => in_array($answer, \App\Models\ExamQuestion::LETTERS, true))
                ->all(),
        ]);
    }

    // Stores the answers and grades the attempt. Only answers to the exam's
    // own questions with a valid letter are kept.
    public function submit(ExamAttempt $attempt, array $answers): ExamAttempt
    {
        return DB::transaction(function () use ($attempt, $answers) {
            $attempt = ExamAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            $exam = $attempt->exam()->with('questions')->first();

            if ($attempt->isSubmitted()) {
                throw ValidationException::withMessages(['exam' => __('This exam has already been submitted.')]);
            }

            if (now()->greaterThan($attempt->deadline()->addSeconds(self::GRACE_SECONDS))) {
                // Time is up: grade what was saved before the deadline.
                $answers = $attempt->answers ?? [];
            }

            $clean = [];
            $correct = 0;
            $earned = 0;

            foreach ($exam->questions as $question) {
                $answer = strtoupper((string) ($answers[$question->id] ?? ''));

                if (array_key_exists($answer, $question->choices())) {
                    $clean[$question->id] = $answer;

                    if ($answer === $question->correct_option) {
                        $correct++;
                        $earned += $question->points;
                    }
                }
            }

            $total = max(1, $exam->totalPoints());

            $attempt->update([
                'answers' => $clean,
                'submitted_at' => now(),
                'correct_count' => $correct,
                'score' => round($earned / $total * 100, 2),
            ]);

            return $attempt;
        });
    }
}
