<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Tenant;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

// Displays recently registered tenants in a table
class RecentTenantsWidget extends BaseWidget
{
    public function getHeading(): string
    {
        return __('Recent Tenants');
    }

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tenant::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('School Name'))
                    ->searchable()
                    ->icon('heroicon-o-building-office'),
                Tables\Columns\TextColumn::make('school_type')
                    ->label(__('Type'))
                    ->badge(),
                Tables\Columns\TextColumn::make('city')
                    ->label(__('City')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label(__('Students'))
                    ->counts('students'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Registered'))
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
