<?php

namespace App\Filament\Teacher\Resources;

use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\StudentViolationResource;
use App\Filament\Teacher\Resources\MyStudentViolationResource\Pages;
use App\Models\ClassroomSubject;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\Teacher;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Teachers report rule violations of students in the classes they teach or
// guide; they only see the reports they created.
class MyStudentViolationResource extends Resource
{
    protected static ?string $model = StudentViolation::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('reported_by', auth()->id())->with('student.classroom', 'violationType');
    }

    // Classes the teacher teaches or is homeroom teacher of.
    private static function studentQuery(): Builder
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        $classroomIds = ClassroomSubject::where('teacher_id', $teacher?->id ?? 0)->pluck('classroom_id')
            ->push($teacher?->homeroom_classroom_id)
            ->filter()
            ->unique();

        return Student::where('status', StudentStatus::ACTIVE)->whereIn('classroom_id', $classroomIds);
    }

    public static function form(Form $form): Form
    {
        return $form->schema(StudentViolationResource::formSchema(fn () => self::studentQuery()));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->label(__('Date'))->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')->label(__('Student'))->searchable()->description(fn (StudentViolation $record) => $record->student?->classroom?->name),
                Tables\Columns\TextColumn::make('violationType.name')->label(__('Violation'))->wrap(),
                Tables\Columns\TextColumn::make('points')->label(__('Points'))->badge(),
                Tables\Columns\TextColumn::make('action_taken')->label(__('Action taken'))->limit(30),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function getNavigationLabel(): string
    {
        return __('Discipline Reports');
    }

    public static function getModelLabel(): string
    {
        return __('Violation record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Discipline Reports');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyStudentViolations::route('/'),
            'create' => Pages\CreateMyStudentViolation::route('/create'),
            'edit' => Pages\EditMyStudentViolation::route('/{record}/edit'),
        ];
    }
}
