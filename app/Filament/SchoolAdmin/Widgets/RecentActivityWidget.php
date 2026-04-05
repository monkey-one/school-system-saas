<?php

namespace App\Filament\SchoolAdmin\Widgets;

use App\Models\Announcement;
use App\Models\Payment;
use App\Models\Student;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Aktivitas Terbaru';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Siswa Baru')
                    ->icon('heroicon-o-user-plus')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS'),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
