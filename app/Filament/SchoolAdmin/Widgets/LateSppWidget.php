<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Enums\PaymentStatus;
use App\Models\SppBill;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

// Displays overdue tuition fee bills in a table
class LateSppWidget extends BaseWidget
{
    protected static ?string $heading = 'Tunggakan SPP';

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
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.classroom.name')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('sppType.name')
                    ->label('Jenis SPP'),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode'),
                Tables\Columns\TextColumn::make('final_amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color('danger')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => fn ($state) => in_array($state, [PaymentStatus::OVERDUE, PaymentStatus::UNPAID]),
                    ]),
            ])
            ->paginated(false);
    }
}
