<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Enums\StudentStatus;
use App\Jobs\SendWhatsAppNotification;
use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\Tenant;
use App\Support\Demo;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

// WhatsApp broadcast to parents (all, per grade or per class) or teachers.
// Messages are queued one by one with a small delay to respect the gateway
// rate limit; placeholders are personalised per recipient. Disabled on the
// public demo so real numbers are never messaged.
class WhatsAppBroadcast extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.school-admin.pages.whatsapp-broadcast';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('Communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('WhatsApp Broadcast');
    }

    public function getTitle(): string
    {
        return __('WhatsApp Broadcast');
    }

    public function mount(): void
    {
        $this->form->fill(['audience' => 'parents']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('audience')
                            ->label(__('Recipients'))
                            ->options([
                                'parents' => __('All parents'),
                                'grade' => __('Parents of a grade'),
                                'classroom' => __('Parents of a class'),
                                'teachers' => __('All teachers'),
                            ])
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('grade_id')
                            ->label(__('Grade'))
                            ->options(fn () => GradeLevel::orderBy('level')->pluck('name', 'id'))
                            ->visible(fn (Forms\Get $get) => $get('audience') === 'grade')
                            ->required(fn (Forms\Get $get) => $get('audience') === 'grade')
                            ->live(),
                        Forms\Components\Select::make('classroom_id')
                            ->label(__('Classroom'))
                            ->options(fn () => Classroom::orderBy('name')->pluck('name', 'id'))
                            ->visible(fn (Forms\Get $get) => $get('audience') === 'classroom')
                            ->required(fn (Forms\Get $get) => $get('audience') === 'classroom')
                            ->live(),
                        Forms\Components\Textarea::make('message')
                            ->label(__('Message'))
                            ->helperText(__('Placeholders: {name} recipient name, {student_name} student name, {school} school name.'))
                            ->rows(6)
                            ->maxLength(1000)
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    // [phone => [name, student_name]] with numbers normalised to 62xxxxxxxxx.
    public function getRecipients(): Collection
    {
        $audience = $this->data['audience'] ?? null;

        if ($audience === 'teachers') {
            return Teacher::whereNotNull('phone')->get()
                ->mapWithKeys(fn (Teacher $t) => [self::normalize($t->phone) => ['name' => $t->full_name, 'student_name' => '']])
                ->filter(fn ($v, $phone) => strlen($phone) >= 10);
        }

        return StudentParent::query()
            ->where('is_whatsapp_active', true)
            ->whereNotNull('phone')
            ->whereHas('student', function ($q) use ($audience) {
                $q->where('status', StudentStatus::ACTIVE);

                if ($audience === 'grade') {
                    $q->whereHas('classroom', fn ($c) => $c->where('grade_id', $this->data['grade_id'] ?? 0));
                } elseif ($audience === 'classroom') {
                    $q->where('classroom_id', $this->data['classroom_id'] ?? 0);
                }
            })
            ->with('student:id,full_name')
            ->get()
            ->mapWithKeys(fn (StudentParent $p) => [self::normalize($p->phone) => ['name' => $p->name, 'student_name' => $p->student?->full_name]])
            ->filter(fn ($v, $phone) => strlen($phone) >= 10);
    }

    public function send(): void
    {
        if (Demo::enabled()) {
            Demo::deny(__('WhatsApp broadcast is disabled on the public demo.'));

            return;
        }

        $data = $this->form->getState();
        $tenant = Tenant::current();
        $recipients = $this->getRecipients();

        $i = 0;
        foreach ($recipients as $phone => $vars) {
            $message = strtr($data['message'], [
                '{name}' => $vars['name'],
                '{student_name}' => $vars['student_name'],
                '{school}' => $tenant->name,
            ]);

            SendWhatsAppNotification::dispatch((string) $phone, $message, 'broadcast', null, $tenant->id)
                ->delay(now()->addSeconds($i++ * 2));
        }

        Notification::make()
            ->success()
            ->title(__(':count messages queued for sending.', ['count' => $recipients->count()]))
            ->body(__('Delivery results appear in the WhatsApp log.'))
            ->send();
    }

    public static function normalize(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        return str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits;
    }
}
