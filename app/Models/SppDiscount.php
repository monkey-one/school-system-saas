<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Represents a discount applied to student tuition fees
class SppDiscount extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'grade_id',
        'name',
        'type',
        'value',
        'valid_from',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class, 'grade_id');
    }
}
