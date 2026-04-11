<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Stores additional information about graduated students (alumni). Linked to
// the Student model which holds the core personal data. This table captures
// post-graduation details such as higher education, occupation, and contact.
class AlumniProfile extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'alumni_number',
        'certificate_number',
        'final_grade_average',
        'higher_education',
        'major',
        'current_occupation',
        'current_company',
        'current_city',
        'phone',
        'email',
        'testimonial',
        'is_verified',
        'graduated_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'graduated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
