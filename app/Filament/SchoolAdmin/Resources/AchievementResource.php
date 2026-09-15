<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\AchievementResource\Pages;
use App\Models\Achievement;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Achievements of students, teachers and the school (website showcase).
class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Achievement'))
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Competition / award'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('rank')
                            ->label(__('Rank'))
                            ->placeholder(__('e.g. 1st place, Gold medal'))
                            ->maxLength(100),
                        Forms\Components\DatePicker::make('achieved_at')
                            ->label(__('Date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('level')
                            ->label(__('Level'))
                            ->options(Achievement::levelLabels())
                            ->default('city')
                            ->required(),
                        Forms\Components\Select::make('category')
                            ->label(__('Category'))
                            ->options(Achievement::categoryLabels())
                            ->default('academic')
                            ->required(),
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student (optional)'))
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (?string $state, Forms\Set $set) => $state ? $set('participant', Student::find($state)?->full_name) : null),
                        Forms\Components\TextInput::make('participant')
                            ->label(__('Participant / team'))
                            ->required()
                            ->maxLength(200),
                        Forms\Components\TextInput::make('organizer')
                            ->label(__('Organizer'))
                            ->maxLength(200),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label(__('Photo'))
                            ->image()
                            ->directory('website/achievements')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048),
                        Forms\Components\Toggle::make('is_published')
                            ->label(__('Show on website'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('')->square()->size(48),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Competition / award'))
                    ->searchable()
                    ->limit(50)
                    ->description(fn (Achievement $record) => $record->participant),
                Tables\Columns\TextColumn::make('rank')->label(__('Rank')),
                Tables\Columns\TextColumn::make('level')
                    ->label(__('Level'))
                    ->badge()
                    ->color(fn (string $state) => in_array($state, ['national', 'international'], true) ? 'success' : 'info')
                    ->formatStateUsing(fn (string $state) => Achievement::levelLabels()[$state] ?? $state),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Category'))
                    ->formatStateUsing(fn (string $state) => Achievement::categoryLabels()[$state] ?? $state)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('achieved_at')->label(__('Date'))->date('d M Y')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->label(__('Published'))->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')->label(__('Level'))->options(Achievement::levelLabels()),
                Tables\Filters\SelectFilter::make('category')->label(__('Category'))->options(Achievement::categoryLabels()),
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
            ->defaultSort('achieved_at', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getNavigationLabel(): string
    {
        return __('Achievements');
    }

    public static function getModelLabel(): string
    {
        return __('Achievement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Achievements');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit' => Pages\EditAchievement::route('/{record}/edit'),
        ];
    }
}
