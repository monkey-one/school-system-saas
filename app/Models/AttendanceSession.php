<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSession extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'classroom_subject_id',
        'teacher_id',
        'date',
        'start_time',
        'end_time',
        'topic',
        'qr_token',
        'qr_expires_at',
        'qr_generated_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'qr_expires_at' => 'datetime',
        'qr_generated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'open',
    ];

    public function classroomSubject(): BelongsTo
    {
        return $this->belongsTo(ClassroomSubject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function studentAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }
}
