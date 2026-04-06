<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\Religion;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents a teacher or staff member employed by the school
class Teacher extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'nip',
        'nuptk',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'employment_status',
        'grade_group',
        'position',
        'education',
        'major',
        'phone',
        'email',
        'photo',
        'joined_at',
        'is_homeroom_teacher',
        'homeroom_classroom_id',
        'notes',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'religion' => Religion::class,
        'employment_status' => EmploymentStatus::class,
        'birth_date' => 'date',
        'joined_at' => 'date',
        'is_homeroom_teacher' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'homeroom_classroom_id');
    }

    public function teachingSchedules(): HasMany
    {
        return $this->hasMany(TeachingSchedule::class);
    }

    public function classroomSubjects(): HasMany
    {
        return $this->hasMany(ClassroomSubject::class);
    }
}
