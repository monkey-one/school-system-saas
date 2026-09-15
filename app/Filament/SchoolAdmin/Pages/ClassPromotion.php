<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Enums\StudentStatus;
use App\Models\Classroom;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

// Class promotion (kenaikan kelas): move selected students from one class to
// another, usually into a class of the next academic year. Students left
// unselected stay in their class (tinggal kelas). Final-year students are
// graduated on the Graduation page instead.
class ClassPromotion extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.school-admin.pages.class-promotion';

    public ?array $data = [];

    public array $selected = [];

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Class Promotion');
    }

    public function getTitle(): string
    {
        return __('Class Promotion');
    }

    public function getSubheading(): ?string
    {
        return __('Move students to their next class in bulk');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $classrooms = fn () => Classroom::with('academicYear')->orderBy('name')->get()
            ->mapWithKeys(fn (Classroom $c) => [$c->id => "{$c->name} · {$c->academicYear?->name}"]);

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('source_classroom_id')
                            ->label(__('From class'))
                            ->options($classrooms)
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn () => $this->selected = $this->getStudents()->pluck('id')->map(fn ($id) => (string) $id)->all())
                            ->required(),
                        Forms\Components\Select::make('target_classroom_id')
                            ->label(__('To class'))
                            ->options($classrooms)
                            ->searchable()
                            ->different('source_classroom_id')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function getStudents(): Collection
    {
        $sourceId = $this->data['source_classroom_id'] ?? null;

        return $sourceId
            ? Student::where('classroom_id', $sourceId)->where('status', StudentStatus::ACTIVE)->orderBy('full_name')->get(['id', 'nis', 'full_name'])
            : collect();
    }

    public function selectAll(): void
    {
        $this->selected = $this->getStudents()->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function promote(): void
    {
        $data = $this->form->getState();
        $target = Classroom::findOrFail($data['target_classroom_id']);

        $ids = $this->getStudents()->pluck('id')->intersect(array_map('intval', $this->selected))->values();

        if ($ids->isEmpty()) {
            Notification::make()->warning()->title(__('No students selected'))->send();

            return;
        }

        $count = Student::whereKey($ids)->update([
            'classroom_id' => $target->id,
            'academic_year_id' => $target->academic_year_id,
        ]);

        $this->selected = [];
        $this->form->fill(['source_classroom_id' => null, 'target_classroom_id' => null]);

        Notification::make()
            ->success()
            ->title(__(':count students moved to :class', ['count' => $count, 'class' => $target->name]))
            ->send();
    }
}
