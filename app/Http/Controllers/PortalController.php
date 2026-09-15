<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Message;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\ReportCard;
use App\Models\SppBill;
use App\Models\Student;
use App\Models\Tenant;
use App\Services\MidtransService;
use App\Services\RaporService;
use App\Services\StudentOverview;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

// Shared behaviour of the student portal and the parent portal. Both show the
// same pages for one student; they only differ in how that student is chosen
// and authorized (the signed-in student vs. one of the parent's children).
abstract class PortalController extends Controller
{
    public function __construct(protected StudentOverview $overview) {}

    // "student" or "parent": the route name prefix of the portal.
    abstract protected function portal(): string;

    // The student shown on pages that have no {student} parameter.
    abstract protected function contextStudent(): Student;

    // Aborts with 403 unless the signed-in user may see this student.
    abstract protected function authorizeStudent(?Student $student): void;

    protected function children(): Collection
    {
        return collect();
    }

    protected function audience(): string
    {
        return $this->portal() === 'parent' ? 'parents' : 'students';
    }

    protected function page(string $view, Student $student, array $data = []): View
    {
        return view('portal.' . $view, $data + [
            'portal' => $this->portal(),
            'student' => $student->loadMissing('classroom.homeroomTeacher'),
            'childParam' => $this->portal() === 'parent' ? ['student' => $student->id] : [],
            'children' => $this->children(),
            'tenant' => Tenant::current(),
            'unreadMessages' => Message::where('recipient_id', auth()->id())->whereNull('read_at')->count(),
        ]);
    }

    protected function showDashboard(Student $student): View
    {
        $bills = $this->overview->bills($student);

        return $this->page('dashboard', $student, [
            'todaySchedule' => $this->overview->todaySchedule($student),
            'attendance' => $this->overview->attendanceSummary($student, now()),
            'recentGrades' => $this->overview->recentGrades($student),
            'billSummary' => $this->overview->billSummary($bills),
            'unpaidBills' => $bills->filter(fn (SppBill $bill) => $this->overview->outstanding($bill) > 0)->sortBy('due_date')->take(3),
            'announcements' => $this->overview->announcements($this->audience(), $student, 4),
            'reportCards' => $this->overview->publishedReportCards($student)->take(2),
            'activeLoans' => $this->overview->loans($student)->whereNull('return_date')->count(),
        ]);
    }

    protected function showSchedule(Student $student): View
    {
        return $this->page('schedule', $student, [
            'schedule' => $this->overview->weeklySchedule($student),
        ]);
    }

    protected function showAttendance(Request $request, Student $student): View
    {
        $month = $this->month($request);

        return $this->page('attendance', $student, [
            'month' => $month,
            'summary' => $this->overview->attendanceSummary($student, $month),
            'overall' => $this->overview->attendanceSummary($student),
            'records' => $this->overview->attendanceRecords($student, $month),
        ]);
    }

    protected function showGrades(Request $request, Student $student): View
    {
        $semesters = $this->overview->semesters();
        $semesterId = (int) $request->query('semester') ?: $this->overview->activeSemester()?->id;

        abort_if($semesterId && ! $semesters->contains('id', $semesterId), 404);

        return $this->page('grades', $student, [
            'semesters' => $semesters,
            'semesterId' => $semesterId,
            'grades' => $this->overview->gradesBySubject($student, $semesterId),
        ]);
    }

    protected function showReportCards(Student $student): View
    {
        return $this->page('report-cards', $student, [
            'reportCards' => $this->overview->publishedReportCards($student),
        ]);
    }

    protected function showBills(Student $student): View
    {
        $bills = $this->overview->bills($student);

        return $this->page('bills', $student, [
            'bills' => $bills,
            'summary' => $this->overview->billSummary($bills),
            'payments' => $this->overview->payments($student),
            'gatewayReady' => app(MidtransService::class)->isConfigured(),
            'overview' => $this->overview,
        ]);
    }

    protected function showActivities(Student $student): View
    {
        return $this->page('activities', $student, [
            'loans' => $this->overview->loans($student),
            'extracurriculars' => $this->overview->extracurriculars($student),
        ]);
    }

    public function reportCardPdf(ReportCard $reportCard)
    {
        $this->authorizeStudent($reportCard->student);
        abort_unless($reportCard->status === 'published', 404);

        $reportCard->forceFill(['downloaded_at' => now()])->saveQuietly();

        return app(RaporService::class)->download($reportCard);
    }

    // Starts a Midtrans Snap payment for what is still owed on a bill. The
    // pending payment is allocated to the bill, so the webhook can settle it.
    public function pay(SppBill $bill)
    {
        $bill->loadMissing('student', 'sppType', 'allocations.payment');
        $this->authorizeStudent($bill->student);

        $outstanding = $this->overview->outstanding($bill);

        if ($outstanding <= 0) {
            return back()->with('status', __('This bill has already been paid.'));
        }

        $midtrans = app(MidtransService::class);

        if (! $midtrans->isConfigured()) {
            return back()->with('error', __('Online payment is not available yet. Please pay at the school finance office.'));
        }

        $amount = (int) round($outstanding);
        $orderId = 'SPP-' . $bill->id . '-' . Str::upper(Str::random(10));
        $student = $bill->student;

        $snapToken = $midtrans->createSnapToken(
            $orderId,
            $amount,
            [
                'name' => $student->full_name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? '',
            ],
            [[
                'id' => 'BILL-' . $bill->id,
                'price' => $amount,
                'quantity' => 1,
                'name' => Str::limit(($bill->sppType?->name ?? __('Tuition Fee')) . ' ' . $bill->period, 50, ''),
            ]],
        );

        if (! $snapToken) {
            return back()->with('error', __('The payment could not be created. Please try again later.'));
        }

        DB::transaction(function () use ($bill, $student, $orderId, $amount) {
            $payment = Payment::create([
                'tenant_id' => $bill->tenant_id,
                'student_id' => $student->id,
                'reference_number' => $orderId,
                'amount' => $amount,
                'payment_date' => now(),
                'method' => PaymentMethod::MIDTRANS,
                'gateway_status' => 'pending',
            ]);

            PaymentBillAllocation::create([
                'tenant_id' => $bill->tenant_id,
                'payment_id' => $payment->id,
                'spp_bill_id' => $bill->id,
                'amount' => $amount,
            ]);
        });

        return $this->page('payment', $student, [
            'bill' => $bill,
            'amount' => $amount,
            'snapToken' => $snapToken,
            'clientKey' => $midtrans->getClientKey(),
            'isProduction' => $midtrans->isProduction(),
        ]);
    }

    public function receipt(Payment $payment)
    {
        $payment->loadMissing('student.classroom', 'allocations.sppBill.sppType');
        $this->authorizeStudent($payment->student);
        abort_unless($this->overview->isSettled($payment), 404);

        $items = $payment->allocations->map(fn (PaymentBillAllocation $allocation) => [
            'description' => $allocation->sppBill?->sppType?->name ?? __('Tuition Fee'),
            'period' => $allocation->sppBill?->period,
            'amount' => $allocation->amount,
        ])->all();

        return Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'student' => $payment->student,
            'classroom' => $payment->student->classroom,
            'school' => Tenant::current(),
            'items' => $items,
        ])->setPaper('a4')->download('kwitansi-' . Str::slug($payment->reference_number) . '.pdf');
    }

    public function announcements(): View
    {
        $student = $this->contextStudent();

        return $this->page('announcements', $student, [
            'announcements' => $this->overview->announcements($this->audience(), $student),
        ]);
    }

    public function messages(): View
    {
        $userId = auth()->id();
        $student = $this->contextStudent();

        return $this->page('messages', $student, [
            'threads' => Message::involving($userId)->with('sender', 'recipient', 'student')->latest()->get()->unique('thread_id')->values(),
            'unreadByThread' => Message::where('recipient_id', $userId)->whereNull('read_at')
                ->selectRaw('thread_id, COUNT(*) as total')->groupBy('thread_id')->pluck('total', 'thread_id'),
            'recipients' => $this->overview->messageRecipients($student),
        ]);
    }

    public function thread(string $thread): View
    {
        $userId = auth()->id();

        $messages = Message::involving($userId)->where('thread_id', $thread)->with('sender', 'student')->oldest()->get();
        abort_if($messages->isEmpty(), 404);

        Message::where('thread_id', $thread)->where('recipient_id', $userId)->whereNull('read_at')->update(['read_at' => now()]);

        return $this->page('message-thread', $this->contextStudent(), [
            'thread' => $thread,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $student = $this->contextStudent();
        $allowed = $this->overview->messageRecipients($student)->pluck('user_id')->all();

        $data = $request->validate([
            'recipient_id' => ['required', Rule::in($allowed)],
            'subject' => ['required', 'string', 'max:150'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $message = Message::create($data + [
            'thread_id' => (string) Str::uuid(),
            'sender_id' => auth()->id(),
            'student_id' => $student->id,
        ]);

        return redirect()->route($this->portal() . '.messages.thread', $message->thread_id)
            ->with('status', __('Message sent.'));
    }

    public function reply(Request $request, string $thread): RedirectResponse
    {
        $userId = auth()->id();
        $last = Message::involving($userId)->where('thread_id', $thread)->latest('id')->firstOrFail();

        $data = $request->validate(['content' => ['required', 'string', 'max:5000']]);

        Message::create([
            'thread_id' => $thread,
            'sender_id' => $userId,
            'recipient_id' => $last->sender_id === $userId ? $last->recipient_id : $last->sender_id,
            'student_id' => $last->student_id,
            'subject' => $last->subject,
            'content' => $data['content'],
        ]);

        return back()->with('status', __('Message sent.'));
    }

    public function profile(): View
    {
        return $this->page('profile', $this->contextStudent(), [
            'user' => auth()->user(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->password = $data['password'];

        // On the public demo the model guard cancels the save.
        if (! $user->save()) {
            return back()->with('error', __('Login details of demo accounts cannot be changed on the public demo.'));
        }

        return back()->with('status', __('Password updated.'));
    }

    private function month(Request $request): Carbon
    {
        $value = (string) $request->query('month', '');

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)
            ? Carbon::createFromFormat('Y-m-d', $value . '-01')->startOfMonth()
            : now()->startOfMonth();
    }
}
