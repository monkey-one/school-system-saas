<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\StudentStatus;
use App\Filament\Actions\LeaveRequestActions;
use App\Filament\SchoolAdmin\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Online leave requests (sick / permission) from parents and students.
// Approval marks the student's attendance automatically.
class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $pending = LeaveRequest::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Leave request'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->options(fn () => Student::where('status', StudentStatus::ACTIVE)->with('classroom')->orderBy('full_name')->get()->mapWithKeys(fn (Student $s) => [$s->id => "{$s->full_name} ({$s->classroom?->name})"]))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options(LeaveRequest::typeLabels())
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')->label(__('From'))->required()->default(today()),
                        Forms\Components\DatePicker::make('end_date')->label(__('Until'))->required()->default(today())->afterOrEqual('start_date'),
                        Forms\Components\Textarea::make('reason')->label(__('Reason'))->required()->rows(3)->maxLength(1000)->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachment')
                            ->label(__('Attachment (doctor\'s note, letter)'))
                            ->disk('local')
                            ->directory('leave-requests')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(LeaveRequest::statusLabels())
                            ->default('pending')
                            ->required()
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('review_note')->label(__('Review note'))->maxLength(500)->visibleOn('edit'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->description(fn (LeaveRequest $record) => $record->student?->classroom?->name),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (string $state) => $state === 'sakit' ? 'info' : 'gray')
                    ->formatStateUsing(fn (string $state) => LeaveRequest::typeLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('Dates'))
                    ->formatStateUsing(fn (LeaveRequest $record) => $record->start_date->translatedFormat('d M') . ' – ' . $record->end_date->translatedFormat('d M Y'))
                    ->description(fn (LeaveRequest $record) => __(':count day(s)', ['count' => $record->days()]))
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')->label(__('Reason'))->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state) => LeaveRequest::statusColor($state))
                    ->formatStateUsing(fn (string $state) => LeaveRequest::statusLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('requester.name')->label(__('Requested by'))->toggleable(),
                Tables\Columns\TextColumn::make('reviewer.name')->label(__('Reviewed by'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->label(__('Submitted'))->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('Status'))->options(LeaveRequest::statusLabels()),
                Tables\Filters\SelectFilter::make('type')->label(__('Type'))->options(LeaveRequest::typeLabels()),
            ])
            ->actions([
                LeaveRequestActions::approve(),
                LeaveRequestActions::reject(),
                LeaveRequestActions::attachment(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Leave Requests');
    }

    public static function getModelLabel(): string
    {
        return __('Leave Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Leave Requests');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
