<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use App\Models\SppBill;
use App\Models\SppDiscount;
use App\Models\SppType;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

// Creates the monthly SPP bills of one school for a billing period. Only SPP
// types with a "monthly" frequency are billed; active discounts (per student
// or per grade) are applied, and students who already have a bill for the
// period and type are skipped, so the job is safe to run more than once.
class GenerateMonthlyBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public int $tenantId,
        public string $period,
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::find($this->tenantId);

        if (! $tenant) {
            return;
        }

        // Dispatched per school from the scheduler: scope every query to this
        // school and restore whatever tenant was active before.
        $previous = Tenant::current();
        Tenant::setCurrent($tenant);

        try {
            $this->generate($tenant);
        } finally {
            Tenant::setCurrent($previous);
        }
    }

    private function generate(Tenant $tenant): void
    {
        $period = Carbon::createFromFormat('Y-m-d', $this->period . '-01')->startOfMonth();
        $dueDay = min(28, max(1, (int) ($tenant->settings['spp_due_day'] ?? 10)));
        $dueDate = $period->copy()->day($dueDay);

        $sppTypes = SppType::where('frequency', 'monthly')->get();
        $students = Student::where('status', StudentStatus::ACTIVE)->with('classroom')->get();
        $discounts = SppDiscount::query()
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', $period->copy()->endOfMonth()))
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', $period))
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($students as $student) {
            foreach ($sppTypes as $sppType) {
                $exists = SppBill::where('student_id', $student->id)
                    ->where('spp_type_id', $sppType->id)
                    ->where('period', $this->period)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $amount = (float) $sppType->amount;
                $discount = min($amount, $this->discountFor($student, $amount, $discounts));

                SppBill::create([
                    'tenant_id' => $tenant->id,
                    'student_id' => $student->id,
                    'spp_type_id' => $sppType->id,
                    'period' => $this->period,
                    'amount' => $amount,
                    'discount_amount' => $discount,
                    'final_amount' => $amount - $discount,
                    'due_date' => $dueDate,
                    'status' => $amount - $discount <= 0 ? PaymentStatus::WAIVED : PaymentStatus::UNPAID,
                ]);

                $created++;
            }
        }

        Log::info("GenerateMonthlyBills: Tenant #{$tenant->id} period {$this->period} — created {$created}, skipped {$skipped}");
    }

    // Sum of the discounts that apply to the student (own or grade-wide).
    private function discountFor(Student $student, float $amount, $discounts): float
    {
        return (float) $discounts
            ->filter(fn (SppDiscount $d) => $d->student_id === $student->id
                || ($d->student_id === null && $d->grade_id !== null && $d->grade_id === $student->classroom?->grade_id))
            ->sum(fn (SppDiscount $d) => $d->type === 'percentage' ? $amount * (float) $d->value / 100 : (float) $d->value);
    }
}
