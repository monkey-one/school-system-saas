<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\BookLoanResource\Pages;
use App\Models\BookLoan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Helpers\CurrencyHelper;

// Manages library book loan transactionsclass BookLoanResource extends Resource
{
    protected static ?string $model = BookLoan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Book Loan Data'))
                    ->description('Informasi peminjaman buku')
                    ->icon('heroicon-o-arrow-path')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('book_id')
                            ->label(__('Book'))
                            ->relationship('book', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\MorphToSelect::make('borrower')
                            ->label(__('Borrower'))
                            ->types([
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Student::class)
                                    ->titleAttribute('full_name')
                                    ->label(__('Student')),
                                Forms\Components\MorphToSelect\Type::make(\App\Models\Teacher::class)
                                    ->titleAttribute('full_name')
                                    ->label(__('Teacher')),
                            ])
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('loan_date')
                            ->label(__('Borrow Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date'))
                            ->required()
                            ->default(now()->addDays(14)),
                        Forms\Components\DatePicker::make('return_date')
                            ->label(__('Return Date')),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'borrowed' => 'Dipinjam',
                                'returned' => 'Dikembalikan',
                                'overdue' => 'Terlambat',
                                'lost' => 'Hilang',
                            ])
                            ->default('borrowed')
                            ->required(),
                        Forms\Components\TextInput::make('fine_amount')
                            ->label(__('Fine'))
                            ->numeric()
                            ->prefix(CurrencyHelper::symbol())
                            ->default(0),
                        Forms\Components\Toggle::make('fine_paid')
                            ->label('Denda Dibayar'),
                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label(__('Book'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('borrower.full_name')
                    ->label(__('Borrower'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Belum dikembalikan'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'borrowed' => 'warning',
                        'returned' => 'success',
                        'overdue' => 'danger',
                        'lost' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        'lost' => 'Hilang',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Dikembalikan',
                        'overdue' => 'Terlambat',
                        'lost' => 'Hilang',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (BookLoan $record) => $record->update([
                        'return_date' => now(),
                        'status' => 'returned',
                    ]))
                    ->visible(fn (BookLoan $record) => $record->status === 'borrowed' || $record->status === 'overdue'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('loan_date', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Library');
    }

    public static function getNavigationLabel(): string
    {
        return __('Book Loans');
    }

    public static function getModelLabel(): string
    {
        return __('Book Loan');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Book Loans');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookLoans::route('/'),
            'create' => Pages\CreateBookLoan::route('/create'),
            'edit' => Pages\EditBookLoan::route('/{record}/edit'),
        ];
    }
}
