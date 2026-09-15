<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\CounselingNoteResource\Pages;
use App\Models\CounselingNote;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Guidance & counseling (BK) session notes. Confidential notes stay inside
// the admin panel; shared notes are visible in the parent portal.
class CounselingNoteResource extends Resource
{
    protected static ?string $model = CounselingNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Counseling session'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->options(fn () => Student::where('status', StudentStatus::ACTIVE)->with('classroom')->orderBy('full_name')->get()->mapWithKeys(fn (Student $s) => [$s->id => "{$s->full_name} ({$s->classroom?->name})"]))
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('session_date')->label(__('Session date'))->default(today())->required(),
                        Forms\Components\Select::make('category')->label(__('Category'))->options(CounselingNote::categoryLabels())->default('academic')->required(),
                        Forms\Components\Toggle::make('is_confidential')
                            ->label(__('Confidential'))
                            ->helperText(__('Confidential notes are never shown to parents.'))
                            ->default(true)
                            ->inline(false),
                        Forms\Components\Textarea::make('summary')->label(__('Summary'))->required()->rows(4)->maxLength(5000)->columnSpanFull(),
                        Forms\Components\Textarea::make('follow_up')->label(__('Follow-up plan'))->rows(3)->maxLength(3000)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('session_date')->label(__('Date'))->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->description(fn (CounselingNote $record) => $record->student?->classroom?->name),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CounselingNote::categoryLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('summary')->label(__('Summary'))->limit(60)->wrap(),
                Tables\Columns\IconColumn::make('is_confidential')->label(__('Confidential'))->boolean(),
                Tables\Columns\TextColumn::make('counselor.name')->label(__('Counselor'))->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->label(__('Category'))->options(CounselingNote::categoryLabels()),
                Tables\Filters\TernaryFilter::make('is_confidential')->label(__('Confidential')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('session_date', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Student Affairs');
    }

    public static function getNavigationLabel(): string
    {
        return __('Counseling (BK)');
    }

    public static function getModelLabel(): string
    {
        return __('Counseling note');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Counseling (BK)');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCounselingNotes::route('/'),
            'create' => Pages\CreateCounselingNote::route('/create'),
            'edit' => Pages\EditCounselingNote::route('/{record}/edit'),
        ];
    }
}
