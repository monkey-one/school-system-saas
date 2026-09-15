<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// One movement in a student's savings / cashless wallet. Rows are immutable:
// corrections are made with a new opposite transaction, and balance_after
// always holds the running balance (see SavingsService).
class SavingsTransaction extends Model
{
    use BelongsToTenant;

    public const TYPES = ['deposit', 'withdrawal', 'purchase'];

    protected $fillable = [
        'tenant_id',
        'student_id',
        'type',
        'amount',
        'balance_after',
        'reference',
        'description',
        'merchant',
        'recorded_by',
        'transacted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transacted_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isCredit(): bool
    {
        return $this->type === 'deposit';
    }

    public static function typeLabels(): array
    {
        return [
            'deposit' => __('Deposit'),
            'withdrawal' => __('Withdrawal'),
            'purchase' => __('Cashless purchase'),
        ];
    }
}
