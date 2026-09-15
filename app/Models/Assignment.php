<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Homework / task given by a teacher to one class for one subject.
class Assignment extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'classroom_subject_id',
        'teacher_id',
        'assessment_id',
        'title',
        'instructions',
        'attachment',
        'due_at',
        'max_score',
        'allow_late',
        'is_published',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'max_score' => 'integer',
        'allow_late' => 'boolean',
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

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForClassroom(Builder $query, ?int $classroomId): Builder
    {
        return $query->whereHas('classroomSubject', fn (Builder $q) => $q->where('classroom_id', $classroomId ?? 0));
    }

    public function acceptsSubmissions(): bool
    {
        return $this->is_published && ($this->allow_late || $this->due_at->isFuture());
    }
}
