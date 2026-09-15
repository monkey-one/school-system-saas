<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Achievement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CounselingNote;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\LeaveRequest;
use App\Models\Message;
use App\Models\Payment;
use App\Models\PaymentBillAllocation;
use App\Models\ReportCard;
use App\Models\SavingsTransaction;
use App\Models\SppBill;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\Tenant;
use App\Services\ExamService;
use App\Services\LeaveRequestService;
use App\Services\MidtransService;
use App\Services\RaporService;
use App\Services\SavingsService;
use App\Services\StudentOverview;
use App\Support\Demo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    protected function showLeaveRequests(Student $student): View
    {
        return $this->page('leave-requests', $student, [
            'requests' => LeaveRequest::where('student_id', $student->id)->with('reviewer')->latest()->get(),
        ]);
    }

    // Parents and students submit sick/permission requests; the homeroom
    // teacher is notified and approval updates attendance automatically.
    public function storeLeaveRequest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(LeaveRequest::TYPES)],
            'start_date' => ['required', 'date', 'after_or_equal:' . today()->subDays(7)->toDateString()],
            'end_date' => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:' . today()->addDays(30)->toDateString()],
            'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $student = Student::find($data['student_id']);
        $this->authorizeStudent($student);

        $leave = LeaveRequest::create([
            'student_id' => $student->id,
            'requested_by' => auth()->id(),
            'type' => $data['type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason'],
            'attachment' => $request->file('attachment')?->store('leave-requests', 'local'),
            'status' => 'pending',
        ]);

        app(LeaveRequestService::class)->notifyHomeroomTeacher($leave);

        return redirect()
            ->route($this->portal() . '.leave-requests', $this->portal() === 'parent' ? ['student' => $student->id] : [])
            ->with('status', __('Leave request submitted. The homeroom teacher will review it.'));
    }

    protected function showSavings(Student $student): View
    {
        $thisMonth = fn () => SavingsTransaction::where('student_id', $student->id)->where('transacted_at', '>=', now()->startOfMonth());

        return $this->page('savings', $student, [
            'balance' => app(SavingsService::class)->balance($student),
            'monthIn' => (float) $thisMonth()->where('type', 'deposit')->sum('amount'),
            'monthOut' => (float) $thisMonth()->where('type', '!=', 'deposit')->sum('amount'),
            'transactions' => SavingsTransaction::where('student_id', $student->id)->latest('id')->paginate(20),
        ]);
    }

    // Discipline points, achievements and counseling notes the school chose
    // to share (hidden violations and confidential notes are excluded).
    protected function showDiscipline(Student $student): View
    {
        $since = $this->overview->activeSemester()?->academicYear?->starts_at ?? now()->startOfYear();

        $violations = StudentViolation::where('student_id', $student->id)
            ->where('visible_to_parent', true)
            ->whereDate('occurred_at', '>=', $since)
            ->with('violationType')
            ->latest('occurred_at')
            ->get();

        return $this->page('discipline', $student, [
            'violations' => $violations,
            'totalPoints' => (int) $violations->sum('points'),
            'achievements' => Achievement::published()->where('student_id', $student->id)->latest('achieved_at')->get(),
            'counseling' => CounselingNote::where('student_id', $student->id)->where('is_confidential', false)->latest('session_date')->get(),
        ]);
    }

    // ----- E-learning: assignments -----

    protected function showAssignments(Student $student): View
    {
        $assignments = Assignment::published()
            ->forClassroom($student->classroom_id)
            ->with(['classroomSubject.subject', 'teacher', 'submissions' => fn ($q) => $q->where('student_id', $student->id)])
            ->latest('due_at')
            ->get();

        return $this->page('assignments', $student, ['assignments' => $assignments]);
    }

    protected function showAssignment(Student $student, Assignment $assignment): View
    {
        $this->authorizeClassroomItem($student, $assignment->classroomSubject?->classroom_id, $assignment->is_published);

        return $this->page('assignment', $student, [
            'assignment' => $assignment->load('classroomSubject.subject', 'teacher'),
            'submission' => AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_id', $student->id)->first(),
        ]);
    }

    // Students submit (or resubmit before grading) a text answer and/or a file.
    public function submitAssignment(Request $request, Assignment $assignment): RedirectResponse
    {
        abort_unless($this->portal() === 'student', 403);
        $student = $this->contextStudent();
        $this->authorizeClassroomItem($student, $assignment->classroomSubject?->classroom_id, $assignment->is_published);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_id', $student->id)->first();

        if (! $assignment->acceptsSubmissions() || $existing?->score !== null) {
            return back()->with('error', __('Submissions for this assignment are closed.'));
        }

        $data = $request->validate([
            'content' => ['nullable', 'string', 'max:10000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,ppt,pptx,xls,xlsx,zip', 'max:10240'],
        ]);

        // The public demo accepts text answers only, so visitors cannot store files on the server.
        if (Demo::enabled() && $request->hasFile('attachment')) {
            return back()->withInput()->with('error', __('File uploads are disabled on the public demo. Please type your answer instead.'));
        }

        if ($existing?->attachment && $request->hasFile('attachment')) {
            Storage::disk('local')->delete($existing->attachment);
        }

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            [
                'tenant_id' => $assignment->tenant_id,
                'content' => $data['content'] ?? null,
                'attachment' => $request->file('attachment')?->store('submissions', 'local') ?? $existing?->attachment,
                'submitted_at' => now(),
                'is_late' => $assignment->due_at->isPast(),
            ],
        );

        return redirect()->route('student.assignments.show', $assignment)->with('status', __('Your work has been submitted.'));
    }

    // ----- E-learning: online exams -----

    protected function showExams(Student $student): View
    {
        $exams = Exam::published()
            ->forClassroom($student->classroom_id)
            ->with(['classroomSubject.subject', 'attempts' => fn ($q) => $q->where('student_id', $student->id)])
            ->withCount('questions')
            ->orderByDesc('starts_at')
            ->get();

        return $this->page('exams', $student, ['exams' => $exams]);
    }

    public function takeExam(Exam $exam): View|RedirectResponse
    {
        abort_unless($this->portal() === 'student', 403);
        $student = $this->contextStudent();
        $this->authorizeClassroomItem($student, $exam->classroomSubject?->classroom_id, $exam->is_published);

        $attempt = ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->first();

        if ($attempt?->isSubmitted()) {
            return redirect()->route('student.exams.result', $exam);
        }

        if (! $attempt && ! $exam->isOpen()) {
            return redirect()->route('student.exams')->with('error', __('This exam is not open.'));
        }

        $attempt ??= app(ExamService::class)->start($exam, $student);
        $questions = $exam->questions()->get()->keyBy('id');
        $ordered = collect($attempt->question_order ?: $questions->keys())->map(fn ($id) => $questions->get($id))->filter()->values();

        return $this->page('exam-take', $student, [
            'exam' => $exam->load('classroomSubject.subject'),
            'attempt' => $attempt,
            'questions' => $ordered,
            'secondsLeft' => max(0, now()->diffInSeconds($attempt->deadline(), false)),
        ]);
    }

    public function saveExam(Request $request, Exam $exam)
    {
        $attempt = $this->studentAttempt($exam);
        app(ExamService::class)->saveProgress($attempt, (array) $request->input('answers', []));

        return response()->json(['saved' => true]);
    }

    public function submitExam(Request $request, Exam $exam): RedirectResponse
    {
        $attempt = $this->studentAttempt($exam);

        if (! $attempt->isSubmitted()) {
            app(ExamService::class)->submit($attempt, (array) $request->input('answers', []));
        }

        return redirect()->route('student.exams.result', $exam)->with('status', __('Your exam has been submitted.'));
    }

    public function examResult(Exam $exam): View
    {
        $attempt = $this->studentAttempt($exam);
        abort_unless($attempt->isSubmitted(), 404);

        return $this->page('exam-result', $this->contextStudent(), [
            'exam' => $exam->load('classroomSubject.subject'),
            'attempt' => $attempt,
            'total' => $exam->questions()->count(),
        ]);
    }

    private function studentAttempt(Exam $exam): ExamAttempt
    {
        abort_unless($this->portal() === 'student', 403);
        $student = $this->contextStudent();
        $this->authorizeClassroomItem($student, $exam->classroomSubject?->classroom_id, $exam->is_published);

        return ExamAttempt::where('exam_id', $exam->id)->where('student_id', $student->id)->firstOrFail();
    }

    // Assignments/exams belong to a class; only published items of the
    // student's own class may be opened.
    private function authorizeClassroomItem(Student $student, ?int $classroomId, bool $published): void
    {
        abort_unless($published && $classroomId !== null && (int) $classroomId === (int) $student->classroom_id, 404);
    }

    private function month(Request $request): Carbon
    {
        $value = (string) $request->query('month', '');

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value)
            ? Carbon::createFromFormat('Y-m-d', $value . '-01')->startOfMonth()
            : now()->startOfMonth();
    }
}
