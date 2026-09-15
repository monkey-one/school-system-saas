<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\Gender;
use App\Enums\PPDBStatus;
use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Filament\SchoolAdmin\Resources\PPDBRegistrationResource\Pages;
use App\Http\Controllers\PPDBController;
use App\Jobs\SendPpdbStatusNotification;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\PPDBRegistration;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

// New student admission (PPDB) registrations: review with uploaded
// documents, accept/reject/waitlist (parents are notified via WhatsApp) and
// enroll accepted applicants as students with portal accounts.
class PPDBRegistrationResource extends Resource
{
    protected static ?string $model = PPDBRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $pending = PPDBRegistration::where('status', PPDBStatus::PENDING)->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Registration data'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('ppdb_wave_id')
                            ->label(__('Wave'))
                            ->relationship('ppdbWave', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('registration_number')
                            ->label(__('Registration Number'))
                            ->default(fn () => sprintf('PPDB-%d-%d-%05d', now()->year, Tenant::current()?->id, PPDBRegistration::withTrashed()->whereYear('created_at', now()->year)->count() + 1))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('full_name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('Date of Birth'))
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label(__('Gender'))
                            ->options(Gender::class)
                            ->required(),
                        Forms\Components\TextInput::make('previous_school')
                            ->label(__('Previous School'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('parent_name')
                            ->label(__('Parent Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('parent_phone')
                            ->label(__('Parent phone'))
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('parent_email')
                            ->label(__('Parent email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Status & Notes'))
                    ->icon('heroicon-o-document-check')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(PPDBStatus::class)
                            ->default(PPDBStatus::PENDING)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->helperText(__('Visible to the applicant on the status page.'))
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')
                    ->label(__('Registration Number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (PPDBRegistration $record) => $record->previous_school),
                Tables\Columns\TextColumn::make('parent_name')
                    ->label(__('Parent Name'))
                    ->searchable()
                    ->description(fn (PPDBRegistration $record) => $record->parent_phone)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('documents')
                    ->label(__('Documents'))
                    ->getStateUsing(fn (PPDBRegistration $record) => count(array_filter($record->documents ?? [])) . '/' . count(PPDBController::documents()))
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (PPDBStatus $state) => $state->label())
                    ->color(fn (PPDBStatus $state) => $state->color())
                    ->sortable(),
                Tables\Columns\IconColumn::make('student_id')
                    ->label(__('Enrolled'))
                    ->boolean()
                    ->getStateUsing(fn (PPDBRegistration $record) => $record->student_id !== null)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Registered'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ppdbWave.name')
                    ->label(__('Wave'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(PPDBStatus::class),
                Tables\Filters\SelectFilter::make('ppdb_wave_id')
                    ->label(__('Wave'))
                    ->relationship('ppdbWave', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('documents')
                    ->label(__('Documents'))
                    ->icon('heroicon-o-paper-clip')
                    ->color('gray')
                    ->modalHeading(fn (PPDBRegistration $record) => __('Documents of :name', ['name' => $record->full_name]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close'))
                    ->modalContent(fn (PPDBRegistration $record) => new HtmlString(
                        collect(PPDBController::documents())->map(function ($doc, $key) use ($record) {
                            $label = e($doc[0]);

                            return empty($record->documents[$key])
                                ? "<li class=\"flex justify-between py-2\"><span>{$label}</span><span class=\"text-gray-400\">—</span></li>"
                                : '<li class="flex justify-between py-2"><span>' . $label . '</span><a class="font-semibold text-primary-600 hover:underline" target="_blank" href="' . e(route('ppdb.documents.show', [$record, $key])) . '">' . e(__('Open')) . '</a></li>';
                        })->prepend('<ul class="divide-y divide-gray-100 text-sm dark:divide-gray-700">')->push('</ul>')->implode('')
                    )),
                Tables\Actions\ActionGroup::make([
                    self::statusAction('accept', __('Accept'), PPDBStatus::ACCEPTED, 'heroicon-o-check-circle', 'success'),
                    self::statusAction('waitlist', __('Waitlist'), PPDBStatus::WAITLIST, 'heroicon-o-clock', 'warning'),
                    self::statusAction('reject', __('Reject'), PPDBStatus::REJECTED, 'heroicon-o-x-circle', 'danger'),
                ])->label(__('Review'))->icon('heroicon-o-scale')->button()->color('primary')
                    ->visible(fn (PPDBRegistration $record) => $record->student_id === null),
                self::enrollAction(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_accept')
                        ->label(__('Accept selected'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(fn (PPDBRegistration $record) => self::setStatus($record, PPDBStatus::ACCEPTED, $record->notes))),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label(__('Reject selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each(fn (PPDBRegistration $record) => self::setStatus($record, PPDBStatus::REJECTED, $record->notes))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    private static function statusAction(string $name, string $label, PPDBStatus $status, string $icon, string $color): Tables\Actions\Action
    {
        return Tables\Actions\Action::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->form([
                Forms\Components\Textarea::make('notes')
                    ->label(__('Note for the applicant'))
                    ->required($status === PPDBStatus::REJECTED)
                    ->rows(3),
            ])
            ->fillForm(fn (PPDBRegistration $record) => ['notes' => $record->notes])
            ->visible(fn (PPDBRegistration $record) => $record->status !== $status)
            ->action(fn (PPDBRegistration $record, array $data) => self::setStatus($record, $status, $data['notes'] ?? null));
    }

    private static function setStatus(PPDBRegistration $record, PPDBStatus $status, ?string $notes): void
    {
        $record->update([
            'status' => $status,
            'notes' => $notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        SendPpdbStatusNotification::dispatch($record->id);
    }

    // Creates the student record, the parent/guardian record and portal
    // accounts, then links them to the registration. Generated passwords are
    // shown once in a persistent notification.
    private static function enrollAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('enroll')
            ->label(__('Enroll as student'))
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->visible(fn (PPDBRegistration $record) => $record->status === PPDBStatus::ACCEPTED && $record->student_id === null)
            ->form([
                Forms\Components\Select::make('classroom_id')
                    ->label(__('Classroom'))
                    ->options(fn () => Classroom::whereHas('academicYear', fn ($q) => $q->where('is_active', true))->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('create_accounts')
                    ->label(__('Create student & parent portal accounts'))
                    ->default(true),
            ])
            ->action(function (PPDBRegistration $record, array $data): void {
                $credentials = DB::transaction(function () use ($record, $data) {
                    $tenant = Tenant::current();
                    $classroom = Classroom::findOrFail($data['classroom_id']);
                    $yearPrefix = (string) (AcademicYear::find($classroom->academic_year_id)?->starts_at?->format('y') ?? now()->format('y'));
                    $sequence = Student::withTrashed()->where('nis', 'like', $yearPrefix . '%')->count() + 1;
                    $nis = $yearPrefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                    while (Student::withTrashed()->where('nis', $nis)->exists()) {
                        $nis = $yearPrefix . str_pad((string) ++$sequence, 4, '0', STR_PAD_LEFT);
                    }

                    $lines = [];
                    $studentUser = null;

                    if ($data['create_accounts']) {
                        $password = Str::password(10, symbols: false);
                        $studentUser = User::create([
                            'tenant_id' => $tenant->id,
                            'name' => $record->full_name,
                            'email' => $nis . '@siswa.' . $tenant->slug . '.sch.id',
                            'password' => Hash::make($password),
                            'type' => UserType::STUDENT,
                            'is_active' => true,
                        ]);
                        $lines[] = __('Student') . ": {$studentUser->email} / {$password}";
                    }

                    $student = Student::create([
                        'user_id' => $studentUser?->id,
                        'nis' => $nis,
                        'classroom_id' => $classroom->id,
                        'academic_year_id' => $classroom->academic_year_id,
                        'full_name' => $record->full_name,
                        'gender' => $record->gender,
                        'birth_date' => $record->birth_date,
                        'address' => $record->address,
                        'previous_school' => $record->previous_school,
                        'status' => StudentStatus::ACTIVE,
                        'entry_year' => (int) now()->format('Y'),
                    ]);

                    StudentParent::create([
                        'student_id' => $student->id,
                        'relation' => 'wali',
                        'name' => $record->parent_name,
                        'phone' => $record->parent_phone,
                        'email' => $record->parent_email,
                        'is_emergency_contact' => true,
                        'is_whatsapp_active' => true,
                    ]);

                    if ($data['create_accounts'] && $record->parent_email && ! User::withoutGlobalScopes()->where('email', $record->parent_email)->exists()) {
                        $password = Str::password(10, symbols: false);
                        User::create([
                            'tenant_id' => $tenant->id,
                            'name' => $record->parent_name,
                            'email' => $record->parent_email,
                            'phone' => $record->parent_phone,
                            'password' => Hash::make($password),
                            'type' => UserType::PARENT,
                            'is_active' => true,
                        ]);
                        $lines[] = __('Parent') . ": {$record->parent_email} / {$password}";
                    }

                    $record->update(['student_id' => $student->id]);

                    return $lines;
                });

                Notification::make()
                    ->success()
                    ->title(__(':name has been enrolled.', ['name' => $record->full_name]))
                    ->body($credentials ? __('Portal accounts (share securely, shown only once):') . "\n" . implode("\n", $credentials) : null)
                    ->persistent()
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('PPDB Registrations');
    }

    public static function getModelLabel(): string
    {
        return __('PPDB Registration');
    }

    public static function getPluralModelLabel(): string
    {
        return __('PPDB Registrations');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPPDBRegistrations::route('/'),
            'create' => Pages\CreatePPDBRegistration::route('/create'),
            'edit' => Pages\EditPPDBRegistration::route('/{record}/edit'),
        ];
    }
}
