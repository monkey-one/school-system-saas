<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Announcement;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\SppBill;
use App\Models\StudentAttendance;
use App\Models\StudentGrade;
use App\Models\TeachingSchedule;
use App\Models\Tenant;
use App\Services\MidtransService;
use App\Services\RaporService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $student = $user->student;
        $tenant = Tenant::current();

        abort_unless($student, 403, 'Anda bukan siswa.');

        $activeSemester = Semester::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->first();

        $schedule = TeachingSchedule::where('tenant_id', $tenant->id)
            ->whereHas('classroomSubject', fn ($q) => $q->where('classroom_id', $student->classroom_id))
            ->where('semester_id', $activeSemester?->id)
            ->where('is_active', true)
            ->with('classroomSubject.subject', 'teacher')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $recentGrades = StudentGrade::where('student_id', $student->id)
            ->with('assessment.classroomSubject.subject')
            ->latest()
            ->take(5)
            ->get();

        $attendanceSummary = StudentAttendance::where('student_id', $student->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $unpaidBills = SppBill::where('student_id', $student->id)
            ->whereNotIn('status', ['paid', 'waived'])
            ->orderBy('due_date')
            ->take(3)
            ->get();

        $announcements = Announcement::where('tenant_id', $tenant->id)
            ->where('published_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        return view('student-portal.dashboard', compact(
            'student', 'schedule', 'recentGrades', 'attendanceSummary',
            'unpaidBills', 'announcements', 'activeSemester'
        ));
    }

    public function attendance()
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $attendances = StudentAttendance::where('student_id', $student->id)
            ->with('attendanceSession.classroomSubject.subject')
            ->orderByDesc('check_in_time')
            ->paginate(25);

        return view('student-portal.attendance', compact('attendances', 'student'));
    }

    public function grades()
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $tenant = Tenant::current();

        $semesters = Semester::where('tenant_id', $tenant->id)
            ->orderByDesc('starts_at')
            ->get();

        $grades = StudentGrade::where('student_id', $student->id)
            ->with('assessment.classroomSubject.subject', 'assessment.assessmentType')
            ->get()
            ->groupBy(fn ($g) => $g->assessment->classroomSubject->subject->name ?? 'Unknown');

        return view('student-portal.grades', compact('grades', 'semesters', 'student'));
    }

    public function rapor(Semester $semester)
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $raporService = app(RaporService::class);
        $reportCard = $raporService->generateForStudent($student, $semester->id);

        $tenant = Tenant::current();

        $pdf = Pdf::loadView('student-portal.rapor-pdf', compact('reportCard', 'student', 'semester', 'tenant'));

        return $pdf->download('rapor-' . $student->nis . '-' . $semester->name . '.pdf');
    }

    public function spp()
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $bills = SppBill::where('student_id', $student->id)
            ->with('sppType')
            ->orderByDesc('due_date')
            ->paginate(25);

        return view('student-portal.spp', compact('bills', 'student'));
    }

    public function pay(SppBill $bill)
    {
        $student = Auth::user()->student;
        abort_unless($student && $bill->student_id === $student->id, 403);
        abort_if(in_array($bill->status->value, ['paid', 'waived']), 400);

        $midtransService = app(MidtransService::class);

        $orderId = 'SPP-' . $bill->id . '-' . time();
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
            return back()->with('error', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');
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

        return view('student-portal.payment', compact('snapToken', 'bill'));
    }

    public function announcements()
    {
        $tenant = Tenant::current();

        $announcements = Announcement::where('tenant_id', $tenant->id)
            ->where('published_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(25);

        return view('student-portal.announcements', compact('announcements'));
    }
}
