<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Student portal: the signed-in student sees their own schedule, attendance,
// grades, report cards, bills, library loans, announcements and messages.
class StudentPortalController extends PortalController
{
    protected function portal(): string
    {
        return 'student';
    }

    protected function contextStudent(): Student
    {
        $student = auth()->user()->student;

        abort_unless($student, 403, __('This account is not linked to a student record.'));

        return $student;
    }

    protected function authorizeStudent(?Student $student): void
    {
        abort_unless($student && $student->id === $this->contextStudent()->id, 403);
    }

    public function dashboard(): View
    {
        return $this->showDashboard($this->contextStudent());
    }

    public function schedule(): View
    {
        return $this->showSchedule($this->contextStudent());
    }

    public function attendance(Request $request): View
    {
        return $this->showAttendance($request, $this->contextStudent());
    }

    public function grades(Request $request): View
    {
        return $this->showGrades($request, $this->contextStudent());
    }

    public function reportCards(): View
    {
        return $this->showReportCards($this->contextStudent());
    }

    public function bills(): View
    {
        return $this->showBills($this->contextStudent());
    }

    public function activities(): View
    {
        return $this->showActivities($this->contextStudent());
    }

    public function leaveRequests(): View
    {
        return $this->showLeaveRequests($this->contextStudent());
    }

    public function savings(): View
    {
        return $this->showSavings($this->contextStudent());
    }

    public function discipline(): View
    {
        return $this->showDiscipline($this->contextStudent());
    }

    public function assignments(): View
    {
        return $this->showAssignments($this->contextStudent());
    }

    public function assignment(Assignment $assignment): View
    {
        return $this->showAssignment($this->contextStudent(), $assignment);
    }

    public function exams(): View
    {
        return $this->showExams($this->contextStudent());
    }
}
