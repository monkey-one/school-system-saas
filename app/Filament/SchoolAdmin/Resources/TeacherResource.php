<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\Religion;
use App\Filament\Exports\TeacherExporter;
use App\Filament\Imports\TeacherImporter;
use App\Filament\SchoolAdmin\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages teacher profiles and employment data
class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pribadi')
                    ->description('Informasi pribadi guru')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label(__('NIP'))
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nuptk')
                            ->label('NUPTK')
                            ->maxLength(30),
                        Forms\Components\TextInput::make('full_name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label(__('Gender'))
                            ->options(Gender::class)
                            ->required(),
                        Forms\Components\TextInput::make('birth_place')
                            ->label(__('Place of Birth'))
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('Date of Birth')),
                        Forms\Components\Select::make('religion')
                            ->label(__('Religion'))
                            ->options(Religion::class),
                    ]),

                Forms\Components\Section::make('Data Kepegawaian')
                    ->description('Informasi kepegawaian')
                    ->icon('heroicon-o-briefcase')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options(EmploymentStatus::class)
                            ->required(),
                        Forms\Components\TextInput::make('grade_group')
                            ->label('Golongan')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('position')
                            ->label(__('Position'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('education')
                            ->label(__('Last Education'))
                            ->maxLength(50),
                        Forms\Components\TextInput::make('major')
                            ->label('Jurusan')
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('joined_at')
                            ->label('Tanggal Bergabung'),
                        Forms\Components\Toggle::make('is_homeroom_teacher')
                            ->label(__('Homeroom Teacher')),
                        Forms\Components\Select::make('homeroom_classroom_id')
                            ->label('Kelas Wali')
                            ->relationship('homeroomClassroom', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('is_homeroom_teacher')),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Contact'))
                    ->description('Informasi kontak')
                    ->icon('heroicon-o-phone')
                    ->collapsible()
                    ->collapsed()
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
                    ]),

                Forms\Components\Section::make(__('Photo'))
                    ->description('Foto guru')
                    ->icon('heroicon-o-camera')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label(__('Photo'))
                            ->image()
                            ->imageEditor()
                            ->directory('teachers/photos')
                            ->maxSize(2048),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label(__('Photo'))
                    ->circular()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label(__('NIP'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employment_status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (EmploymentStatus $state) => $state->label())
                    ->color(fn (EmploymentStatus $state) => match ($state) {
                        EmploymentStatus::PNS => 'success',
                        EmploymentStatus::GTY => 'info',
                        EmploymentStatus::GTT => 'warning',
                        EmploymentStatus::HONORER => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('position')
                    ->label(__('Position'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->options(EmploymentStatus::class),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class),
                Tables\Filters\TernaryFilter::make('is_homeroom_teacher')
                    ->label(__('Homeroom Teacher')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->exporter(TeacherExporter::class)
                        ->label('Ekspor'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(TeacherImporter::class)
                    ->label('Impor Excel'),
                Tables\Actions\ExportAction::make()
                    ->exporter(TeacherExporter::class)
                    ->label('Ekspor'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Staff Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Teachers');
    }

    public static function getModelLabel(): string
    {
        return __('Teacher');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Teachers');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
