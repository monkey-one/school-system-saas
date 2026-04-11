<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\AlumniResource\Pages;
use App\Models\AlumniProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages alumni data including post-graduation tracking
class AlumniResource extends Resource
{
    protected static ?string $model = AlumniProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Alumni');
    }

    public static function getModelLabel(): string
    {
        return __('Alumni');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Alumni');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Student Data'))
                    ->description(__('Core student information (read-only)'))
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name', fn ($query) => $query->where('status', StudentStatus::ALUMNI))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull()
                            ->disabledOn('edit'),
                    ]),

                Forms\Components\Section::make(__('Graduation Information'))
                    ->description(__('Certificate and graduation details'))
                    ->icon('heroicon-o-academic-cap')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('alumni_number')
                            ->label(__('Alumni Number'))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('certificate_number')
                            ->label(__('Certificate Number'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('final_grade_average')
                            ->label(__('Final Grade Average'))
                            ->maxLength(10),
                        Forms\Components\DatePicker::make('graduated_at')
                            ->label(__('Graduation Date')),
                    ]),

                Forms\Components\Section::make(__('Post-Graduation'))
                    ->description(__('Track where alumni go after graduation'))
                    ->icon('heroicon-o-briefcase')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('higher_education')
                            ->label(__('Higher Education'))
                            ->placeholder(__('University / College name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('major')
                            ->label(__('Major'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('current_occupation')
                            ->label(__('Current Occupation'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('current_company')
                            ->label(__('Current Company'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('current_city')
                            ->label(__('Current City'))
                            ->maxLength(100),
                    ]),

                Forms\Components\Section::make(__('Contact & Verification'))
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
                        Forms\Components\Textarea::make('testimonial')
                            ->label(__('Testimonial'))
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label(__('Verified'))
                            ->helperText(__('Mark alumni data as verified')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alumni_number')
                    ->label(__('Alumni Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.nis')
                    ->label(__('NIS'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.graduation_year')
                    ->label(__('Graduation Year'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.classroom.name')
                    ->label(__('Last Class')),
                Tables\Columns\TextColumn::make('higher_education')
                    ->label(__('Higher Education'))
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_occupation')
                    ->label(__('Current Occupation'))
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label(__('Verified'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('graduated_at')
                    ->label(__('Graduation Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('graduation_year')
                    ->label(__('Graduation Year'))
                    ->options(fn () => AlumniProfile::query()
                        ->join('students', 'alumni_profiles.student_id', '=', 'students.id')
                        ->whereNotNull('students.graduation_year')
                        ->distinct()
                        ->pluck('students.graduation_year', 'students.graduation_year')
                        ->sortDesc()
                        ->toArray()
                    )
                    ->query(fn ($query, $data) => $data['value']
                        ? $query->whereHas('student', fn ($q) => $q->where('graduation_year', $data['value']))
                        : $query
                    ),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label(__('Verified')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlumni::route('/'),
            'create' => Pages\CreateAlumni::route('/create'),
            'edit' => Pages\EditAlumni::route('/{record}/edit'),
        ];
    }
}
