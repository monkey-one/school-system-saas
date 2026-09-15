<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Online absence request (sick / permission) submitted by a parent or
// student and reviewed by the homeroom teacher or school staff.
class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    public const TYPES = ['sakit', 'izin'];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    protected $fillable = [
        'tenant_id',
        'student_id',
        'requested_by',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApprovedOn(Builder $query, int $studentId, string $date): Builder
    {
        return $query->where('student_id', $studentId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }

    public function days(): int
    {
        return (int) $this->start_date->diffInDays($this->end_date) + 1;
    }

    public static function typeLabels(): array
    {
        return ['sakit' => __('Sick'), 'izin' => __('Permission')];
    }

    public static function statusLabels(): array
    {
        return ['pending' => __('Pending'), 'approved' => __('Approved'), 'rejected' => __('Rejected')];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }
}
