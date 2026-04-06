<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Enums\PaymentStatus;
use App\Filament\SchoolAdmin\Resources\SppBillResource\Pages;
use App\Models\SppBill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages tuition fee billing records
class SppBillResource extends Resource
{
    protected static ?string $model = SppBill::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Tagihan')
                    ->description('Informasi tagihan SPP')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('spp_type_id')
                            ->label('Jenis SPP')
                            ->relationship('sppType', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('period')
                            ->label(__('Period'))
                            ->placeholder('2024-01')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Diskon (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\TextInput::make('final_amount')
                            ->label('Jumlah Akhir (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date'))
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::UNPAID)
                            ->required(),
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
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sppType.name')
                    ->label('Jenis SPP')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('period')
                    ->label(__('Period'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('final_amount')
                    ->label(__('Total'))
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state) => $state->label())
                    ->color(fn (PaymentStatus $state) => $state->color())
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(PaymentStatus::class),
                Tables\Filters\SelectFilter::make('spp_type_id')
                    ->label('Jenis SPP')
                    ->relationship('sppType', 'name'),
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
            ->headerActions([
                Tables\Actions\Action::make('generate')
                    ->label('Generate Tagihan Bulanan')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Tagihan Bulanan')
                    ->modalDescription('Tagihan akan digenerate untuk semua siswa aktif berdasarkan jenis SPP bulanan.')
                    ->form([
                        Forms\Components\TextInput::make('period')
                            ->label(__('Period'))
                            ->placeholder('2024-01')
                            ->required(),
                        Forms\Components\Select::make('spp_type_id')
                            ->label('Jenis SPP')
                            ->options(\App\Models\SppType::pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Generate bills placeholder
                    }),
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
        return __('Finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tuition Bills');
    }

    public static function getModelLabel(): string
    {
        return __('Tuition Bill');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Tuition Bills');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSppBills::route('/'),
            'create' => Pages\CreateSppBill::route('/create'),
            'edit' => Pages\EditSppBill::route('/{record}/edit'),
        ];
    }
}
