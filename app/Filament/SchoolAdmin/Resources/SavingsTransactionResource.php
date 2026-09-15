<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\StudentStatus;
use App\Filament\SchoolAdmin\Resources\SavingsTransactionResource\Pages;
use App\Helpers\CurrencyHelper;
use App\Models\SavingsTransaction;
use App\Models\Student;
use App\Services\SavingsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// Student savings (tabungan) and cashless wallet. Transactions are immutable
// and always created through SavingsService, which maintains the running
// balance and blocks withdrawals/purchases beyond the balance.
class SavingsTransactionResource extends Resource
{
    protected static ?string $model = SavingsTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 6;

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('New transaction'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->options(fn () => Student::where('status', StudentStatus::ACTIVE)->with('classroom')->orderBy('full_name')->get()->mapWithKeys(fn (Student $s) => [$s->id => "{$s->full_name} · {$s->nis} · {$s->classroom?->name}"]))
                            ->searchable()
                            ->live()
                            ->required(),
                        Forms\Components\Placeholder::make('balance')
                            ->label(__('Current balance'))
                            ->content(fn (Forms\Get $get) => $get('student_id') ? CurrencyHelper::format(app(SavingsService::class)->balance(Student::findOrFail($get('student_id')))) : '-'),
                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options(SavingsTransaction::typeLabels())
                            ->default('deposit')
                            ->live()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('Amount'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100000000)
                            ->prefix(CurrencyHelper::symbol())
                            ->required(),
                        Forms\Components\TextInput::make('merchant')
                            ->label(__('Merchant / canteen'))
                            ->maxLength(100)
                            ->visible(fn (Forms\Get $get) => $get('type') === 'purchase'),
                        Forms\Components\TextInput::make('description')
                            ->label(__('Description'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transacted_at')->label(__('Date'))->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->description(fn (SavingsTransaction $record) => $record->student?->nis),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'deposit' => 'success',
                        'purchase' => 'info',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => SavingsTransaction::typeLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn (SavingsTransaction $record) => ($record->isCredit() ? '+ ' : '− ') . CurrencyHelper::format($record->amount))
                    ->color(fn (SavingsTransaction $record) => $record->isCredit() ? 'success' : 'danger')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label(__('Balance'))
                    ->formatStateUsing(fn ($state) => CurrencyHelper::format($state))
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('merchant')->label(__('Merchant'))->toggleable(),
                Tables\Columns\TextColumn::make('description')->label(__('Description'))->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('reference')->label(__('Reference'))->copyable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('recorder.name')->label(__('Recorded by'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label(__('Type'))->options(SavingsTransaction::typeLabels()),
                Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->relationship('student', 'full_name')
                    ->searchable(),
                Tables\Filters\Filter::make('today')
                    ->label(__('Today'))
                    ->query(fn (Builder $query) => $query->whereDate('transacted_at', today())),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Savings & Cashless');
    }

    public static function getModelLabel(): string
    {
        return __('Savings transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Savings & Cashless');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSavingsTransactions::route('/'),
            'create' => Pages\CreateSavingsTransaction::route('/create'),
        ];
    }
}
