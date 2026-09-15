<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

// One student's attempt at an exam. Each student gets a single attempt; the
// deadline is the earlier of start + duration and the exam window end.
class ExamAttempt extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'student_id',
        'started_at',
        'submitted_at',
        'answers',
        'question_order',
        'correct_count',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
        'question_order' => 'array',
        'correct_count' => 'integer',
        'score' => 'decimal:2',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function deadline(): Carbon
    {
        return $this->started_at->copy()->addMinutes($this->exam->duration_minutes)->min($this->exam->ends_at);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }
}
