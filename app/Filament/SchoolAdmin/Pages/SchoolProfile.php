<?php

namespace App\Filament\SchoolAdmin\Pages;

use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

// Settings page where school admins manage their public profile website content
// including vision, mission, description, social links, and gallery images.
class SchoolProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.school-admin.pages.school-profile';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
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
        ]);
    }

    public function form(Form $form): Form
    {
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
                        Forms\Components\TextInput::make('accreditation')
                            ->label(__('Accreditation'))
                            ->placeholder('A / B / C')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('founded_year')
                            ->label(__('Founded'))
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        Forms\Components\TextInput::make('website')
                            ->label(__('Website'))
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255),
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

                Forms\Components\Section::make(__('Social Media'))
                    ->description(__('Links to school social media accounts'))
                    ->icon('heroicon-o-share')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('social_links.facebook')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('social_links.instagram')
                            ->label('Instagram')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('social_links.youtube')
                            ->label('YouTube')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('social_links.tiktok')
                            ->label('TikTok')
                            ->url()
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $tenant = Tenant::current();
        $tenant->update($data);

        Notification::make()
            ->title(__('School profile updated successfully'))
            ->success()
            ->send();
    }
}
