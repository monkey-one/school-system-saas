<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\CurriculumSettingResource\Pages;
use App\Models\CurriculumSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Manages curriculum configuration and settings
class CurriculumSettingResource extends Resource
{
    protected static ?string $model = CurriculumSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Kurikulum')
                    ->description('Konfigurasi penilaian dan kurikulum')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('academic_year_id')
                            ->label(__('Academic Year'))
                            ->relationship('academicYear', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\KeyValue::make('assessment_weights')
                            ->label('Bobot Penilaian')
                            ->keyLabel('Jenis Penilaian')
                            ->valueLabel('Bobot (%)')
                            ->addActionLabel('Tambah Bobot'),
                        Forms\Components\TextInput::make('kkm_default')
                            ->label('KKM Default')
                            ->numeric()
                            ->default(75)
                            ->required(),
                        Forms\Components\TextInput::make('passing_grade')
                            ->label('Nilai Kelulusan')
                            ->numeric()
                            ->default(70),
                        Forms\Components\Select::make('grading_type')
                            ->label('Tipe Penilaian')
                            ->options([
                                'numeric' => 'Angka',
                                'letter' => 'Huruf',
                                'description' => 'Deskripsi',
                            ])
                            ->default('numeric'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')
                    ->label(__('Academic Year'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('kkm_default')
                    ->label('KKM Default')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('passing_grade')
                    ->label('Nilai Kelulusan')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('grading_type')
                    ->label('Tipe Penilaian')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'numeric' => 'Angka',
                        'letter' => 'Huruf',
                        'description' => 'Deskripsi',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('academic_year_id')
                    ->label(__('Academic Year'))
                    ->relationship('academicYear', 'name'),
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
        return __('Academic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Curriculum');
    }

    public static function getModelLabel(): string
    {
        return __('Curriculum Setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Curriculum Settings');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurriculumSettings::route('/'),
            'create' => Pages\CreateCurriculumSetting::route('/create'),
            'edit' => Pages\EditCurriculumSetting::route('/{record}/edit'),
        ];
    }
}
