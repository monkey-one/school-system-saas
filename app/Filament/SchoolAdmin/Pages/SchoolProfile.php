<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Helpers\CurrencyHelper;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

// Settings page where school admins manage the school identity and the
// content of the public website (hero, principal's greeting, history,
// contact channels, social links). Website fields live in tenants.settings.
class SchoolProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.school-admin.pages.school-profile';

    public ?array $data = [];

    // Keys of tenants.settings edited on this page; other keys are preserved.
    private const WEBSITE_SETTINGS = ['hero_title', 'hero_subtitle', 'hero_image', 'principal_greeting', 'principal_photo', 'principal_nip', 'history', 'whatsapp', 'office_hours',
        'school_lat', 'school_lng', 'checkin_radius_m', 'teacher_checkin_time', 'late_threshold_minutes'];

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getNavigationLabel(): string
    {
        return __('School Profile');
    }

    public function getTitle(): string
    {
        return __('School Profile');
    }

    public function getSubheading(): ?string
    {
        return __('Manage your public school profile website');
    }

    public function mount(): void
    {
        $tenant = Tenant::current();

        $this->form->fill([
            'name' => $tenant->name,
            'logo' => $tenant->logo,
            'description' => $tenant->description,
            'vision' => $tenant->vision,
            'mission' => $tenant->mission,
            'principal_name' => $tenant->principal_name,
            'npsn' => $tenant->npsn,
            'accreditation' => $tenant->accreditation,
            'founded_year' => $tenant->founded_year,
            'address' => $tenant->address,
            'city' => $tenant->city,
            'province' => $tenant->province,
            'phone' => $tenant->phone,
            'email' => $tenant->email,
            'website' => $tenant->website,
            'social_links' => $tenant->social_links ?? [],
            'currency' => $tenant->currency ?? 'IDR',
            'settings' => collect($tenant->settings ?? [])->only(self::WEBSITE_SETTINGS)->all(),
        ]);
    }

    public function form(Form $form): Form
    {
        $image = fn (string $name, string $label) => Forms\Components\FileUpload::make($name)
            ->label($label)
            ->image()
            ->directory('website/profile')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(2048);

        return $form
            ->schema([
                Forms\Components\Section::make(__('Basic Information'))
                    ->description(__('General school information displayed on the profile'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('School Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('principal_name')
                            ->label(__('Principal'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('npsn')
                            ->label(__('NPSN'))
                            ->maxLength(20),
                        Forms\Components\TextInput::make('settings.principal_nip')
                            ->label(__('Principal NIP'))
                            ->maxLength(30),
                        Forms\Components\TextInput::make('accreditation')
                            ->label(__('Accreditation'))
                            ->placeholder('A / B / C')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('founded_year')
                            ->label(__('Founded'))
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        Forms\Components\Select::make('currency')
                            ->label(__('Currency'))
                            ->options(CurrencyHelper::options())
                            ->required()
                            ->searchable()
                            ->helperText(__('Currency used for tuition and payment display')),
                        Forms\Components\TextInput::make('website')
                            ->label(__('Website'))
                            ->url()
                            ->maxLength(255),
                        $image('logo', __('School logo'))->directory('tenants/logos')->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Vision & Mission'))
                    ->description(__('Define the school vision and mission statement'))
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('Short Description'))
                            ->helperText(__('Brief description shown on the hero section of the profile page'))
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\Textarea::make('vision')
                            ->label(__('Vision'))
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\Textarea::make('mission')
                            ->label(__('Mission'))
                            ->helperText(__('Each line will be displayed as a separate item'))
                            ->rows(5)
                            ->maxLength(2000),
                    ]),

                Forms\Components\Section::make(__('Website home page'))
                    ->description(__('Content of the public school website'))
                    ->icon('heroicon-o-home')
                    ->columns(2)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('settings.hero_title')
                            ->label(__('Hero title'))
                            ->maxLength(120),
                        Forms\Components\TextInput::make('settings.hero_subtitle')
                            ->label(__('Hero subtitle'))
                            ->maxLength(250),
                        $image('settings.hero_image', __('Hero background image'))->columnSpanFull(),
                        Forms\Components\Textarea::make('settings.principal_greeting')
                            ->label(__('Principal\'s greeting'))
                            ->rows(5)
                            ->maxLength(3000),
                        $image('settings.principal_photo', __('Principal photo')),
                        Forms\Components\Textarea::make('settings.history')
                            ->label(__('School history'))
                            ->rows(6)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Contact Information'))
                    ->description(__('How visitors can reach the school'))
                    ->icon('heroicon-o-phone')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('settings.whatsapp')
                            ->label(__('WhatsApp number'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('settings.office_hours')
                            ->label(__('Office hours'))
                            ->maxLength(100),
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label(__('City'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('province')
                            ->label(__('Province'))
                            ->maxLength(100),
                    ]),

                Forms\Components\Section::make(__('Teacher attendance'))
                    ->description(__('GPS check-in: teachers must be within the radius of the school coordinates. Leave the coordinates empty to disable the location check.'))
                    ->icon('heroicon-o-map-pin')
                    ->columns(3)
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('settings.school_lat')
                            ->label(__('Latitude'))
                            ->numeric()
                            ->minValue(-90)
                            ->maxValue(90),
                        Forms\Components\TextInput::make('settings.school_lng')
                            ->label(__('Longitude'))
                            ->numeric()
                            ->minValue(-180)
                            ->maxValue(180),
                        Forms\Components\TextInput::make('settings.checkin_radius_m')
                            ->label(__('Check-in radius (m)'))
                            ->integer()
                            ->minValue(20)
                            ->maxValue(5000)
                            ->default(200),
                        Forms\Components\TimePicker::make('settings.teacher_checkin_time')
                            ->label(__('Start time'))
                            ->seconds(false)
                            ->default('07:00'),
                        Forms\Components\TextInput::make('settings.late_threshold_minutes')
                            ->label(__('Late tolerance (minutes)'))
                            ->integer()
                            ->minValue(0)
                            ->maxValue(180)
                            ->default(15),
                    ]),

                Forms\Components\Section::make(__('Social Media'))
                    ->description(__('Links to school social media accounts'))
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema(collect(['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'])
                        ->map(fn (string $label, string $key) => Forms\Components\TextInput::make("social_links.{$key}")
                            ->label($label)
                            ->url()
                            ->startsWith(['https://'])
                            ->maxLength(255))
                        ->values()
                        ->all()),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $tenant = Tenant::current();

        $data['settings'] = array_merge($tenant->settings ?? [], collect($data['settings'] ?? [])->only(self::WEBSITE_SETTINGS)->all());

        $tenant->update($data);

        Notification::make()
            ->title(__('School profile updated successfully'))
            ->success()
            ->send();
    }
}
