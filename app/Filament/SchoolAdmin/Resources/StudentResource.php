<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\Gender;
use App\Enums\Religion;
use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\StudentResource\Pages;
use App\Filament\SchoolAdmin\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages student profiles and enrollment data
class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pribadi')
                    ->description('Informasi pribadi siswa')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label(__('NIS'))
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nisn')
                            ->label(__('NISN'))
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('full_name')
                            ->label(__('Full Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nickname')
                            ->label('Nama Panggilan')
                            ->maxLength(100),
                        Forms\Components\Select::make('gender')
                            ->label(__('Gender'))
                            ->options(Gender::class)
                            ->required(),
                        Forms\Components\TextInput::make('birth_place')
                            ->label(__('Place of Birth'))
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label(__('Date of Birth'))
                            ->required(),
                        Forms\Components\Select::make('religion')
                            ->label(__('Religion'))
                            ->options(Religion::class),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Kewarganegaraan')
                            ->default('Indonesia')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('blood_type')
                            ->label('Golongan Darah')
                            ->maxLength(5),
                        Forms\Components\TextInput::make('child_order')
                            ->label('Anak Ke')
                            ->numeric(),
                        Forms\Components\TextInput::make('num_siblings')
                            ->label('Jumlah Saudara')
                            ->numeric(),
                        Forms\Components\TextInput::make('height')
                            ->label('Tinggi Badan (cm)')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('weight')
                            ->label('Berat Badan (kg)')
                            ->maxLength(10),
                        Forms\Components\TextInput::make('disabilities')
                            ->label('Disabilitas')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hobbies')
                            ->label('Hobi')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make(__('Address'))
                    ->description('Informasi alamat siswa')
                    ->icon('heroicon-o-map-pin')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label(__('Address'))
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('rt')
                            ->label('RT')
                            ->maxLength(5),
                        Forms\Components\TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(5),
                        Forms\Components\TextInput::make('village')
                            ->label('Kelurahan/Desa')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('district')
                            ->label('Kecamatan')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('city')
                            ->label(__('City/District'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('province')
                            ->label(__('Province'))
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->maxLength(10),
                    ]),

                Forms\Components\Section::make('Data Akademik')
                    ->description('Informasi akademik siswa')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible()
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('classroom_id')
                            ->label(__('Classroom'))
                            ->relationship('classroom', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
                            ->relationship('academicYear', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(StudentStatus::class)
                            ->default(StudentStatus::ACTIVE)
                            ->required(),
                        Forms\Components\TextInput::make('previous_school')
                            ->label('Sekolah Asal')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('entry_year')
                            ->label('Tahun Masuk')
                            ->numeric(),
                        Forms\Components\TextInput::make('graduation_year')
                            ->label('Tahun Lulus')
                            ->numeric(),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Photo'))
                    ->description('Foto siswa')
                    ->icon('heroicon-o-camera')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label(__('Photo'))
                            ->image()
                            ->imageEditor()
                            ->directory('students/photos')
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
                Tables\Columns\TextColumn::make('nis')
                    ->label(__('NIS'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('Full Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label(__('Classroom'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('JK')
                    ->formatStateUsing(fn (Gender $state) => $state->label())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (StudentStatus $state) => $state->label())
                    ->color(fn (StudentStatus $state) => $state->color())
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->label(__('Classroom'))
                    ->relationship('classroom', 'name'),
                Tables\Filters\SelectFilter::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(StudentStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Ekspor'),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->label('Impor Excel'),
                Tables\Actions\ExportAction::make()
                    ->label('Ekspor'),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ParentsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Students');
    }

    public static function getModelLabel(): string
    {
        return __('Student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Students');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
