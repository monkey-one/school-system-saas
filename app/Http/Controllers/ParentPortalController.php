<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentGrade;
use App\Models\StudentParent;
use App\Models\Tenant;
use App\Services\MidtransService;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Serves the parent-facing portal. A parent can have multiple children, so
// many actions receive a Student model parameter. The authorizeParent()
// method verifies that the authenticated user is indeed a registered parent
// of the given student within the current tenant.
class ParentPortalController extends Controller
{
    // Dashboard: lists all children linked to the parent's email.
    public function dashboard()
    {
        $user = Auth::user();
        $tenant = Tenant::current();

        $children = Student::where('tenant_id', $tenant->id)
            ->whereHas('parents', fn ($q) => $q->where('email', $user->email))
            ->with('classroom')
            ->get();

        abort_if($children->isEmpty(), 403, 'Tidak ada data anak terdaftar.');

        return view('parent-portal.dashboard', compact('children', 'tenant'));
    }

    // Attendance records for a specific child.
    public function attendance(Student $student)
    {
        $this->authorizeParent($student);

        $attendances = StudentAttendance::where('student_id', $student->id)
            ->with('attendanceSession.classroomSubject.subject')
            ->orderByDesc('check_in_time')
            ->paginate(25);

        return view('parent-portal.attendance', compact('attendances', 'student'));
    }

    // Grades for a specific child, grouped by subject.
    public function grades(Student $student)
    {
        $this->authorizeParent($student);

        $grades = StudentGrade::where('student_id', $student->id)
            ->with('assessment.classroomSubject.subject', 'assessment.assessmentType')
            ->get()
            ->groupBy(fn ($g) => $g->assessment->classroomSubject->subject->name ?? 'Unknown');

        return view('parent-portal.grades', compact('grades', 'student'));
    }

    // Download report card PDF for a child's semester.
    public function rapor(Student $student, Semester $semester)
    {
        $this->authorizeParent($student);

        $raporService = app(RaporService::class);
        $reportCard = $raporService->generateForStudent($student, $semester->id);

        $tenant = Tenant::current();

        $pdf = Pdf::loadView('student-portal.rapor-pdf', compact('reportCard', 'student', 'semester', 'tenant'));

        return $pdf->download('rapor-' . $student->nis . '-' . $semester->name . '.pdf');
    }

    // SPP bills for a specific child.
    public function spp(Student $student)
    {
        $this->authorizeParent($student);

        $bills = SppBill::where('student_id', $student->id)
            ->with('sppType')
            ->orderByDesc('due_date')
            ->paginate(25);

        return view('parent-portal.spp', compact('bills', 'student'));
    }

    // Create a Midtrans payment session for an unpaid bill.
    public function pay(SppBill $bill)
    {
        $student = $bill->student;
        $this->authorizeParent($student);
        abort_if(in_array($bill->status->value, ['paid', 'waived']), 400);

        $midtransService = app(MidtransService::class);

        // Build a unique order ID for the payment gateway. Using uniqid()
        // instead of time() avoids collisions on sub-second requests.
        $orderId = 'SPP-' . $bill->id . '-' . uniqid();
        $snapToken = $midtransService->createSnapToken(
            $orderId,
            (int) $bill->final_amount,
            [
                'name' => $student->full_name,
                'email' => $student->email ?? Auth::user()->email,
                'phone' => $student->phone ?? '',
            ],
            [
                [
                    'id' => 'SPP-' . $bill->id,
                    'price' => (int) $bill->final_amount,
                    'quantity' => 1,
                    'name' => $bill->sppType->name ?? 'SPP ' . $bill->period,
                ],
            ]
        );

        if (! $snapToken) {
            return back()->with('error', 'Gagal membuat transaksi pembayaran.');
        }

        Payment::create([
            'tenant_id' => Tenant::current()->id,
            'student_id' => $student->id,
            'reference_number' => $orderId,
            'amount' => $bill->final_amount,
            'payment_date' => now(),
            'method' => PaymentMethod::MIDTRANS,
            'gateway_transaction_id' => null,
            'gateway_status' => 'pending',
        ]);

        return view('parent-portal.payment', compact('snapToken', 'bill'));
    }

    // Show sent and received messages for the current user.
    public function messages()
    {
        $user = Auth::user();
        $tenant = Tenant::current();

        // Fetch messages the user sent or was a recipient of. The recipients
        // column stores a JSON array of user IDs.
        $messages = Message::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhereRaw("JSON_CONTAINS(recipients, ?)", [json_encode($user->id)]);
            })
            ->with('sender')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('parent-portal.messages', compact('messages'));
    }

    // Ensures the logged-in user is a registered parent of the given student
    // and that both belong to the current tenant.
    protected function authorizeParent(Student $student): void
    {
        $user = Auth::user();
        $tenant = Tenant::current();

        $isParent = StudentParent::where('student_id', $student->id)
            ->where('email', $user->email)
            ->exists();

        abort_unless($isParent && $student->tenant_id === $tenant->id, 403);
    }
}
