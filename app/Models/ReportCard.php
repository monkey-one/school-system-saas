<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportCard extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'semester_id',
        'classroom_id',
        'homeroom_comment',
        'principal_comment',
        'status',
        'published_at',
        'downloaded_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function reportCardSubjects(): HasMany
    {
        return $this->hasMany(ReportCardSubject::class);
    }
}
