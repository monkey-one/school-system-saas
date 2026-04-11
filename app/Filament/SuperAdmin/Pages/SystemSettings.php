<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

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
            'app_name' => config('app.name', 'EduSaaS'),
            'default_locale' => config('app.locale', 'id'),
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
            'trial_days' => Cache::get('system.trial_days', 14),
            'max_students_free' => Cache::get('system.max_students_free', 50),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'allow_registration' => Cache::get('system.allow_registration', true),
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
                            ->maxLength(255),
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
                            ])
                            ->required(),
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
        $data = $this->form->getState();

        Cache::forever('system.trial_days', $data['trial_days'] ?? 14);
        Cache::forever('system.max_students_free', $data['max_students_free'] ?? 50);
        Cache::forever('system.allow_registration', $data['allow_registration'] ?? true);

        Notification::make()
            ->title(__('Settings saved successfully'))
            ->success()
            ->send();
    }
}
