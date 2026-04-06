<?php

namespace App\Jobs;

use App\Models\Student;
use App\Services\RaporService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Batch-generates report card data for every student in a classroom.
// Failures for individual students are logged and do not stop the loop.
class GenerateReportCards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $classroomId,
        public int $semesterId,
    ) {}

    public function handle(RaporService $raporService): void
    {
        $students = Student::where('classroom_id', $this->classroomId)->get();

        Log::info("GenerateReportCards: Starting batch for classroom #{$this->classroomId}, semester #{$this->semesterId} — {$students->count()} students");

        foreach ($students as $student) {
            try {
                $raporService->generateForStudent($student, $this->semesterId);
            } catch (\Throwable $e) {
                Log::error("GenerateReportCards: Failed for student #{$student->id}: {$e->getMessage()}");
            }
        }

        Log::info("GenerateReportCards: Completed batch for classroom #{$this->classroomId}");
    }
}
