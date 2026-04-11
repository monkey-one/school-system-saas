<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\ReportCardResource\Pages;
use App\Models\ReportCard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportCardResource extends Resource
{
    protected static ?string $model = ReportCard::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Report Card Data'))
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label(__('Student'))
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('semester_id')
                            ->label(__('Semester'))
                            ->relationship('semester', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('classroom_id')
                            ->label(__('Classroom'))
                            ->relationship('classroom', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('Status'))
                            ->options([
                                'draft' => __('Draft'),
                                'published' => __('Published'),
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('Published At')),
                    ]),
                Forms\Components\Section::make(__('Comments'))
                    ->icon('heroicon-o-chat-bubble-bottom-center-text')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('homeroom_comment')
                            ->label(__('Homeroom Teacher Comment'))
                            ->rows(3),
                        Forms\Components\Textarea::make('principal_comment')
                            ->label(__('Principal Comment'))
                            ->rows(3),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.name')
                    ->label(__('Semester'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('classroom.name')
                    ->label(__('Classroom'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Published At'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('reportCardSubjects_count')
                    ->label(__('Subjects'))
                    ->counts('reportCardSubjects')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('semester_id')
                    ->label(__('Semester'))
                    ->relationship('semester', 'name'),
                Tables\Filters\SelectFilter::make('classroom_id')
                    ->label(__('Classroom'))
                    ->relationship('classroom', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'draft' => __('Draft'),
                        'published' => __('Published'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->label(__('Publish'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(fn ($record) => $record->update([
                        'status' => 'published',
                        'published_at' => now(),
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc');
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
        return __('Report Cards');
    }

    public static function getModelLabel(): string
    {
        return __('Report Card');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Report Cards');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportCards::route('/'),
            'create' => Pages\CreateReportCard::route('/create'),
            'edit' => Pages\EditReportCard::route('/{record}/edit'),
        ];
    }
}
