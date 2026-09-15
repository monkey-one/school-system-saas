<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Online multiple-choice exam (CBT) for one class and subject, open within a
// time window with a per-student duration.
class Exam extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'classroom_subject_id',
        'teacher_id',
        'assessment_id',
        'title',
        'instructions',
        'starts_at',
        'ends_at',
        'duration_minutes',
        'shuffle_questions',
        'show_result',
        'is_published',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'duration_minutes' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_result' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function classroomSubject(): BelongsTo
    {
        return $this->belongsTo(ClassroomSubject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForClassroom(Builder $query, ?int $classroomId): Builder
    {
        return $query->whereHas('classroomSubject', fn (Builder $q) => $q->where('classroom_id', $classroomId ?? 0));
    }

    public function isOpen(): bool
    {
        return $this->is_published && now()->between($this->starts_at, $this->ends_at);
    }

    public function totalPoints(): int
    {
        return (int) $this->questions->sum('points');
    }
}
