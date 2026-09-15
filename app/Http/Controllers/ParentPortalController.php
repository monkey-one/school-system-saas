<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

// Parent portal: a parent is linked to their children through the email on
// the student's parent/guardian record (student_parents.email). One account
// can follow several children; the last opened child becomes the context for
// announcements, messages and the profile page.
class ParentPortalController extends PortalController
{
    private ?Collection $childrenCache = null;

    protected function portal(): string
    {
        return 'parent';
    }

    protected function children(): Collection
    {
        return $this->childrenCache ??= Student::query()
            ->whereHas('parents', fn ($query) => $query->where('email', auth()->user()->email))
            ->with('classroom')
            ->orderBy('full_name')
            ->get();
    }

    protected function contextStudent(): Student
    {
        $children = $this->children();

        abort_if($children->isEmpty(), 403, __('No children are linked to this account yet. Please contact the school.'));

        return $children->firstWhere('id', (int) session('parent_child_id')) ?? $children->first();
    }

    protected function authorizeStudent(?Student $student): void
    {
        abort_unless($student && $this->children()->contains('id', $student->id), 403);

        session(['parent_child_id' => $student->id]);
    }

    // Overview cards for every child.
    public function dashboard(): View
    {
        $cards = $this->children()->map(function (Student $child) {
            $bills = $this->overview->bills($child);

            return [
                'student' => $child,
                'attendance' => $this->overview->attendanceSummary($child, now()),
                'bills' => $this->overview->billSummary($bills),
                'reportCards' => $this->overview->publishedReportCards($child)->count(),
            ];
        });

        return $this->page('parent-home', $this->contextStudent(), [
            'cards' => $cards,
            'announcements' => $this->overview->announcements('parents', $this->contextStudent(), 4),
        ]);
    }

    public function child(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showDashboard($student);
    }

    public function schedule(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showSchedule($student);
    }

    public function attendance(Request $request, Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showAttendance($request, $student);
    }

    public function grades(Request $request, Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showGrades($request, $student);
    }

    public function reportCards(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showReportCards($student);
    }

    public function bills(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showBills($student);
    }

    public function activities(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showActivities($student);
    }

    public function leaveRequests(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showLeaveRequests($student);
    }

    public function savings(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showSavings($student);
    }

    public function discipline(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showDiscipline($student);
    }

    public function assignments(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showAssignments($student);
    }

    public function assignment(Student $student, Assignment $assignment): View
    {
        $this->authorizeStudent($student);

        return $this->showAssignment($student, $assignment);
    }

    public function exams(Student $student): View
    {
        $this->authorizeStudent($student);

        return $this->showExams($student);
    }
}
