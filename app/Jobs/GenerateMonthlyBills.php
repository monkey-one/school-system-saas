<?php

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Enums\StudentStatus;
use App\Models\SppBill;
use App\Models\SppType;
use App\Models\Student;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Creates SPP bills for every active student in a tenant for the specified
// billing period. Skips students who already have a bill for that period
// and SPP type combination. Designed to run once per month via a scheduler.
class GenerateMonthlyBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tenantId,
        public string $period,
    ) {}

    public function handle(): void
    {
        $tenant = Tenant::findOrFail($this->tenantId);
        $periodDate = Carbon::createFromFormat('Y-m', $this->period);
        $dueDate = $periodDate->copy()->endOfMonth();

        $students = Student::where('tenant_id', $this->tenantId)
            ->where('status', StudentStatus::ACTIVE)
            ->get();

        $sppTypes = SppType::where('tenant_id', $this->tenantId)->get();

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

                SppBill::create([
                    'tenant_id' => $this->tenantId,
                    'student_id' => $student->id,
                    'spp_type_id' => $sppType->id,
                    'period' => $this->period,
                    'amount' => $sppType->amount,
                    'discount_amount' => 0,
                    'final_amount' => $sppType->amount,
                    'due_date' => $dueDate,
                    'status' => PaymentStatus::UNPAID,
                ]);

                $created++;
            }
        }

        Log::info("GenerateMonthlyBills: Tenant #{$this->tenantId} period {$this->period} — created {$created}, skipped {$skipped}");
    }
}
