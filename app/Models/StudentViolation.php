<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// A recorded rule violation. Points are copied from the violation type at the
// time of recording so later catalogue changes do not rewrite history.
class StudentViolation extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'violation_type_id',
        'occurred_at',
        'points',
        'description',
        'action_taken',
        'reported_by',
        'visible_to_parent',
    ];

    protected $casts = [
        'occurred_at' => 'date',
        'points' => 'integer',
        'visible_to_parent' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (StudentViolation $violation) {
            $violation->reported_by ??= auth()->id();

            if ($violation->points === null && $violation->violation_type_id) {
                $violation->points = (int) ViolationType::whereKey($violation->violation_type_id)->value('points');
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function violationType(): BelongsTo
    {
        return $this->belongsTo(ViolationType::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
