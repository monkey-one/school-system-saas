<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Helpers\CurrencyHelper;
use App\Support\Demo;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

// Platform-wide settings stored in the cache store under "system.*" keys.
// Name, language and timezone are applied on every web request by
// AppServiceProvider::applySystemSettings().
class SystemSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.super-admin.pages.system-settings';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('System');
    }

    public static function getNavigationLabel(): string
    {
        return __('System Settings');
    }

    public function getTitle(): string
    {
        return __('System Settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'app_name' => Cache::get('system.app_name', config('app.name', 'EduSaaS')),
            'default_locale' => Cache::get('system.default_locale', config('app.locale', 'id')),
            'timezone' => Cache::get('system.timezone', config('app.timezone', 'Asia/Jakarta')),
            'trial_days' => Cache::get('system.trial_days', 14),
            'max_students_free' => Cache::get('system.max_students_free', 50),
            'allow_registration' => Cache::get('system.allow_registration', true),
            'default_currency' => Cache::get('system.default_currency', 'IDR'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('General Settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label(__('Application Name'))
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Select::make('default_locale')
                            ->label(__('Default Language'))
                            ->options([
                                'id' => 'Bahasa Indonesia',
                                'en' => 'English',
                            ])
                            ->required(),
                        Forms\Components\Select::make('timezone')
                            ->label(__('Timezone'))
                            ->options([
                                'Asia/Jakarta' => 'WIB (Asia/Jakarta)',
                                'Asia/Makassar' => 'WITA (Asia/Makassar)',
                                'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
                                'Asia/Kuala_Lumpur' => 'Asia/Kuala_Lumpur',
                                'Asia/Singapore' => 'Asia/Singapore',
                                'UTC' => 'UTC',
                            ])
                            ->required(),
                        Forms\Components\Select::make('default_currency')
                            ->label(__('Default Currency'))
                            ->options(CurrencyHelper::options())
                            ->required()
                            ->searchable()
                            ->helperText(__('Default currency for all schools. Schools can override this in their settings.')),
                    ]),
                Forms\Components\Section::make(__('Registration & Trial'))
                    ->icon('heroicon-o-user-plus')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('allow_registration')
                            ->label(__('Allow New Registration'))
                            ->helperText(__('Enable or disable school registration')),
                        Forms\Components\TextInput::make('trial_days')
                            ->label(__('Trial Period (days)'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(365),
                        Forms\Components\TextInput::make('max_students_free')
                            ->label(__('Max Students (Free Plan)'))
                            ->numeric()
                            ->minValue(0),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        if (Demo::enabled()) {
            Demo::deny(__('System settings are read-only on the public demo.'));

            return;
        }

        $data = $this->form->getState();

        Cache::forever('system.app_name', $data['app_name']);
        Cache::forever('system.default_locale', $data['default_locale']);
        Cache::forever('system.timezone', $data['timezone']);
        Cache::forever('system.trial_days', (int) ($data['trial_days'] ?? 14));
        Cache::forever('system.max_students_free', (int) ($data['max_students_free'] ?? 50));
        Cache::forever('system.allow_registration', (bool) ($data['allow_registration'] ?? true));
        Cache::forever('system.default_currency', $data['default_currency'] ?? 'IDR');

        Notification::make()
            ->title(__('Settings saved successfully'))
            ->success()
            ->send();
    }
}
