<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Guidance & counseling (BK) session record. Confidential notes are never
// shown in the student or parent portal.
class CounselingNote extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'counselor_id',
        'session_date',
        'category',
        'summary',
        'follow_up',
        'is_confidential',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_confidential' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(fn (CounselingNote $note) => $note->counselor_id ??= auth()->id());
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public static function categoryLabels(): array
    {
        return [
            'academic' => __('Academic'),
            'personal' => __('Personal'),
            'social' => __('Social'),
            'career' => __('Career'),
            'discipline' => __('Discipline'),
        ];
    }
}
