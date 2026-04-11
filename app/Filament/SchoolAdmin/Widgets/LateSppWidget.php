<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\PaymentStatus;
use App\Models\SppBill;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Helpers\CurrencyHelper;

// Displays overdue tuition fee bills in a table
class LateSppWidget extends BaseWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Overdue SPP Bills');
    }

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SppBill::query()
                    ->whereIn('status', [PaymentStatus::OVERDUE, PaymentStatus::UNPAID])
                    ->where('due_date', '<', now())
                    ->with(['student', 'sppType'])
                    ->orderBy('due_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.classroom.name')
                    ->label(__('Class')),
                Tables\Columns\TextColumn::make('sppType.name')
                    ->label(__('SPP Type')),
                Tables\Columns\TextColumn::make('period')
                    ->label(__('Period')),
                Tables\Columns\TextColumn::make('final_amount')
                    ->label(__('Amount'))
                    ->money(CurrencyHelper::code())
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('Due Date'))
                    ->date('d M Y')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn ($state) => in_array($state, [PaymentStatus::OVERDUE, PaymentStatus::UNPAID]) ? 'danger' : 'gray'),
            ])
            ->paginated(false);
    }
}
