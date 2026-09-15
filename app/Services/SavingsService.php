<?php

namespace App\Services;

use App\Models\SavingsTransaction;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Student savings & cashless wallet. Every movement is written inside a
// transaction that locks the student's latest row, so concurrent deposits
// and purchases can never corrupt the running balance or go negative.
class SavingsService
{
    public function balance(Student $student): float
    {
        return (float) (SavingsTransaction::where('student_id', $student->id)->latest('id')->value('balance_after') ?? 0);
    }

    public function record(Student $student, string $type, float $amount, ?string $description = null, ?string $merchant = null, ?int $userId = null): SavingsTransaction
    {
        if (! in_array($type, SavingsTransaction::TYPES, true)) {
            throw ValidationException::withMessages(['type' => __('Invalid transaction type.')]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => __('The amount must be greater than zero.')]);
        }

        return DB::transaction(function () use ($student, $type, $amount, $description, $merchant, $userId) {
            // Lock the student row: it exists even before the first
            // transaction, so concurrent writers always queue up here.
            Student::whereKey($student->id)->lockForUpdate()->first();

            $current = (float) (SavingsTransaction::where('student_id', $student->id)
                ->latest('id')
                ->value('balance_after') ?? 0);

            $newBalance = $type === 'deposit' ? $current + $amount : $current - $amount;

            if ($newBalance < 0) {
                throw ValidationException::withMessages([
                    'amount' => __('Insufficient balance. Current balance: :balance', ['balance' => \App\Helpers\CurrencyHelper::format($current)]),
                ]);
            }

            return SavingsTransaction::create([
                'tenant_id' => $student->tenant_id,
                'student_id' => $student->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'TAB-' . now()->format('ymd') . '-' . Str::upper(Str::random(6)),
                'description' => $description,
                'merchant' => $type === 'purchase' ? $merchant : null,
                'recorded_by' => $userId ?? auth()->id(),
                'transacted_at' => now(),
            ]);
        });
    }
}
