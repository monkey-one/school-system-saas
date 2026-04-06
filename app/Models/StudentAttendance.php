<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Records a student attendance entry for a specific attendance session
class StudentAttendance extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'attendance_session_id',
        'student_id',
        'status',
        'check_in_time',
        'method',
        'notes',
        'notified_parent_at',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
        'check_in_time' => 'datetime',
        'notified_parent_at' => 'datetime',
    ];

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
