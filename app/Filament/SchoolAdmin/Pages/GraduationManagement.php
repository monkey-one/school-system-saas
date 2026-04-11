<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Enums\StudentStatus;
use App\Models\AlumniProfile;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

// Allows school admins to graduate students in bulk. Selecting a classroom
// and graduation year changes the status of each selected student to ALUMNI
// and creates an AlumniProfile record for post-graduation tracking.
class GraduationManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.school-admin.pages.graduation-management';

    public ?int $classroom_id = null;
    public ?int $graduation_year = null;
    public array $selected_students = [];
    public Collection $students;

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Graduation');
    }

    public function getTitle(): string
    {
        return __('Graduation Management');
    }

    public function getSubheading(): ?string
    {
        return __('Graduate students and create alumni records');
    }

    public function mount(): void
    {
        $this->graduation_year = (int) date('Y');
        $this->students = collect();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Select Students to Graduate'))
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('classroom_id')
                            ->label(__('Classroom'))
                            ->options(Classroom::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->loadStudents()),
                        Forms\Components\TextInput::make('graduation_year')
                            ->label(__('Graduation Year'))
                            ->numeric()
                            ->default(date('Y'))
                            ->required()
                            ->minValue(2000)
                            ->maxValue((int) date('Y') + 1),
                    ]),
            ]);
    }

    public function loadStudents(): void
    {
        if (! $this->classroom_id) {
            $this->students = collect();
            return;
        }

        $this->students = Student::where('classroom_id', $this->classroom_id)
            ->where('status', StudentStatus::ACTIVE)
            ->orderBy('full_name')
            ->get(['id', 'nis', 'nisn', 'full_name']);

        $this->selected_students = $this->students->pluck('id')->toArray();
    }

    public function graduate(): void
    {
        if (empty($this->selected_students)) {
            Notification::make()
                ->title(__('No students selected'))
                ->warning()
                ->send();
            return;
        }

        $tenantId = Tenant::current()?->id;
        $year = $this->graduation_year ?? (int) date('Y');
        $count = 0;

        foreach ($this->selected_students as $studentId) {
            $student = Student::find($studentId);
            if (! $student || $student->status !== StudentStatus::ACTIVE) {
                continue;
            }

            $student->update([
                'status' => StudentStatus::ALUMNI,
                'graduation_year' => $year,
            ]);

            $alumniNumber = 'ALM-' . $year . '-' . str_pad($studentId, 5, '0', STR_PAD_LEFT);

            AlumniProfile::create([
                'tenant_id' => $tenantId,
                'student_id' => $student->id,
                'alumni_number' => $alumniNumber,
                'graduated_at' => now(),
            ]);

            $count++;
        }

        $this->students = collect();
        $this->selected_students = [];
        $this->classroom_id = null;

        Notification::make()
            ->title(__(':count students graduated successfully', ['count' => $count]))
            ->success()
            ->send();
    }
}
