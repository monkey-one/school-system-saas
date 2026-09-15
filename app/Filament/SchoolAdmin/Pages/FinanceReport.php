<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Enums\PaymentStatus;
use App\Helpers\CurrencyHelper;
use App\Models\Payment;
use App\Models\SavingsTransaction;
use App\Models\SppBill;
use App\Services\StudentOverview;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Monthly finance report: billed vs collected, collection by payment method,
// outstanding per class, top arrears and total student savings, with a CSV
// export for the finance office. Only settled payments count as collected.
class FinanceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.school-admin.pages.finance-report';

    public string $month = '';

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Finance Report');
    }

    public function getTitle(): string
    {
        return __('Finance Report');
    }

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function period(): Carbon
    {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $this->month)
            ? Carbon::createFromFormat('Y-m-d', $this->month . '-01')->startOfMonth()
            : now()->startOfMonth();
    }

    public function getReport(): array
    {
        $start = $this->period();
        $end = $start->copy()->endOfMonth();

        $payments = Payment::query()
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->where(fn ($q) => $q->whereNull('gateway_status')->orWhereIn('gateway_status', StudentOverview::SETTLED_GATEWAY_STATUSES))
            ->get();

        $billed = (float) SppBill::whereBetween('due_date', [$start->toDateString(), $end->toDateString()])->sum('final_amount');

        $settled = implode("','", StudentOverview::SETTLED_GATEWAY_STATUSES);
        $outstandingBills = SppBill::query()
            ->whereNotIn('status', [PaymentStatus::PAID->value, PaymentStatus::WAIVED->value])
            ->with('student.classroom')
            ->select('spp_bills.*')
            ->selectRaw("spp_bills.final_amount - COALESCE((SELECT SUM(a.amount) FROM payment_bill_allocations a JOIN payments p ON p.id = a.payment_id WHERE a.spp_bill_id = spp_bills.id AND a.deleted_at IS NULL AND p.deleted_at IS NULL AND (p.gateway_status IS NULL OR p.gateway_status IN ('{$settled}'))), 0) AS outstanding")
            ->get()
            ->filter(fn (SppBill $bill) => (float) $bill->outstanding > 0);

        $byClassroom = $outstandingBills
            ->groupBy(fn (SppBill $bill) => $bill->student?->classroom?->name ?? __('No class'))
            ->map(fn (Collection $bills, string $class) => [
                'class' => $class,
                'students' => $bills->pluck('student_id')->unique()->count(),
                'bills' => $bills->count(),
                'outstanding' => (float) $bills->sum('outstanding'),
            ])
            ->sortBy('class')
            ->values();

        $arrears = $outstandingBills
            ->groupBy('student_id')
            ->map(fn (Collection $bills) => [
                'name' => $bills->first()->student?->full_name,
                'nis' => $bills->first()->student?->nis,
                'class' => $bills->first()->student?->classroom?->name,
                'bills' => $bills->count(),
                'outstanding' => (float) $bills->sum('outstanding'),
            ])
            ->sortByDesc('outstanding')
            ->take(10)
            ->values();

        $savings = (float) SavingsTransaction::query()
            ->whereIn('id', SavingsTransaction::query()->selectRaw('MAX(id)')->groupBy('student_id'))
            ->sum('balance_after');

        return [
            'billed' => $billed,
            'collected' => (float) $payments->sum('amount'),
            'transactions' => $payments->count(),
            'outstanding' => (float) $outstandingBills->sum('outstanding'),
            'byMethod' => $payments->groupBy(fn (Payment $p) => $p->method?->label() ?? '-')->map(fn ($g) => (float) $g->sum('amount'))->sortDesc(),
            'byClassroom' => $byClassroom,
            'arrears' => $arrears,
            'savings' => $savings,
        ];
    }

    public function export(): StreamedResponse
    {
        $report = $this->getReport();
        $period = $this->period();

        return response()->streamDownload(function () use ($report, $period) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [__('Finance Report'), $period->translatedFormat('F Y')]);
            fputcsv($out, [__('Billed this month'), $report['billed']]);
            fputcsv($out, [__('Collected this month'), $report['collected']]);
            fputcsv($out, [__('Total outstanding'), $report['outstanding']]);
            fputcsv($out, [__('Total student savings'), $report['savings']]);
            fputcsv($out, []);
            fputcsv($out, [__('Class'), __('Students'), __('Bills'), __('Outstanding')]);
            foreach ($report['byClassroom'] as $row) {
                fputcsv($out, [$row['class'], $row['students'], $row['bills'], $row['outstanding']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['NIS', __('Student'), __('Class'), __('Bills'), __('Outstanding')]);
            foreach ($report['arrears'] as $row) {
                fputcsv($out, [$row['nis'], $row['name'], $row['class'], $row['bills'], $row['outstanding']]);
            }
            fclose($out);
        }, 'laporan-keuangan-' . $period->format('Y-m') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function money(float $value): string
    {
        return CurrencyHelper::format($value);
    }
}
