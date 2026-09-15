<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\StudentViolationResource\Pages;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\ViolationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Discipline records (pelanggaran & poin) managed by BK / student affairs.
class StudentViolationResource extends Resource
{
    protected static ?string $model = StudentViolation::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form->schema(self::formSchema(fn () => Student::where('status', StudentStatus::ACTIVE)));
    }

    // Shared with the teacher panel, which limits the student list.
    public static function formSchema(\Closure $studentQuery): array
    {
        return [
            Forms\Components\Section::make(__('Violation record'))
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('student_id')
                        ->label(__('Student'))
                        ->options(fn () => $studentQuery()->with('classroom')->orderBy('full_name')->get()->mapWithKeys(fn (Student $s) => [$s->id => "{$s->full_name} ({$s->classroom?->name})"]))
                        ->searchable()
                        ->required(),
                    Forms\Components\DatePicker::make('occurred_at')->label(__('Date'))->default(today())->maxDate(today())->required(),
                    Forms\Components\Select::make('violation_type_id')
                        ->label(__('Violation'))
                        ->options(fn () => ViolationType::orderBy('points')->get()->mapWithKeys(fn (ViolationType $t) => [$t->id => "{$t->name} ({$t->points} " . __('points') . ')']))
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (?string $state, Forms\Set $set) => $set('points', $state ? ViolationType::find($state)?->points : null))
                        ->required(),
                    Forms\Components\TextInput::make('points')->label(__('Points'))->numeric()->minValue(0)->maxValue(100)->required(),
                    Forms\Components\Textarea::make('description')->label(__('Chronology'))->rows(3)->maxLength(2000)->columnSpanFull(),
                    Forms\Components\TextInput::make('action_taken')->label(__('Action taken'))->maxLength(255),
                    Forms\Components\Toggle::make('visible_to_parent')->label(__('Visible in parent portal'))->default(true)->inline(false),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')->label(__('Date'))->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->description(fn (StudentViolation $record) => $record->student?->classroom?->name),
                Tables\Columns\TextColumn::make('violationType.name')->label(__('Violation'))->wrap()->limit(50),
                Tables\Columns\TextColumn::make('points')
                    ->label(__('Points'))
                    ->badge()
                    ->color(fn (int $state) => $state >= 25 ? 'danger' : ($state >= 10 ? 'warning' : 'gray'))
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label(__('Total'))),
                Tables\Columns\TextColumn::make('action_taken')->label(__('Action taken'))->limit(30)->toggleable(),
                Tables\Columns\IconColumn::make('visible_to_parent')->label(__('Parent'))->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('reporter.name')->label(__('Reported by'))->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('violation_type_id')->label(__('Violation'))->relationship('violationType', 'name'),
                Tables\Filters\SelectFilter::make('student_id')->label(__('Student'))->relationship('student', 'full_name')->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Discipline Records');
    }

    public static function getModelLabel(): string
    {
        return __('Violation record');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Discipline Records');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentViolations::route('/'),
            'create' => Pages\CreateStudentViolation::route('/create'),
            'edit' => Pages\EditStudentViolation::route('/{record}/edit'),
        ];
    }
}
