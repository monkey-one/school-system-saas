<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Religion;
use App\Enums\StudentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents a student enrolled in the school
class Student extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'nis',
        'nisn',
        'classroom_id',
        'academic_year_id',
        'full_name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'nationality',
        'child_order',
        'num_siblings',
        'address',
        'rt',
        'rw',
        'village',
        'district',
        'city',
        'postal_code',
        'province',
        'phone',
        'email',
        'photo',
        'blood_type',
        'height',
        'weight',
        'disabilities',
        'hobbies',
        'previous_school',
        'status',
        'entry_year',
        'graduation_year',
        'notes',
    ];

    protected $casts = [
        'gender' => Gender::class,
        'religion' => Religion::class,
        'status' => StudentStatus::class,
        'birth_date' => 'date',
        'child_order' => 'integer',
        'num_siblings' => 'integer',
        'entry_year' => 'integer',
        'graduation_year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function parents(): HasMany
    {
        return $this->hasMany(StudentParent::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function sppBills(): HasMany
    {
        return $this->hasMany(SppBill::class);
    }

    public function alumniProfile(): HasOne
    {
        return $this->hasOne(AlumniProfile::class);
    }
}
