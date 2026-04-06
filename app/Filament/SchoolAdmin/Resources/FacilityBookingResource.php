<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\FacilityBookingResource\Pages;
use App\Models\FacilityBooking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages facility booking requests and scheduling
class FacilityBookingResource extends Resource
{
    protected static ?string $model = FacilityBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Booking Data'))
                    ->description('Informasi booking fasilitas')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('facility_id')
                            ->label(__('Facility'))
                            ->relationship('facility', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\TimePicker::make('start_time')
                            ->label(__('Start Time'))
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->label(__('End Time'))
                            ->required()
                            ->after('start_time'),
                        Forms\Components\TextInput::make('purpose')
                            ->label(__('Purpose'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'pending' => 'Menunggu',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('pending')
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
                Tables\Columns\TextColumn::make('facility.name')
                    ->label(__('Facility'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label(__('Start'))
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->label(__('Purpose'))
                    ->searchable()
                    ->toggleable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('booker.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('facility_id')
                    ->label(__('Facility'))
                    ->relationship('facility', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (FacilityBooking $record) => $record->update(['status' => 'approved']))
                    ->visible(fn (FacilityBooking $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (FacilityBooking $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (FacilityBooking $record) => $record->status === 'pending'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('date', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Facility Bookings');
    }

    public static function getModelLabel(): string
    {
        return __('Facility Booking');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Facility Bookings');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFacilityBookings::route('/'),
            'create' => Pages\CreateFacilityBooking::route('/create'),
            'edit' => Pages\EditFacilityBooking::route('/{record}/edit'),
        ];
    }
}
