<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationGroup = 'Komunikasi';

    protected static ?string $navigationLabel = 'Pengumuman';

    protected static ?string $modelLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pengumuman')
                    ->description('Informasi pengumuman')
                    ->icon('heroicon-o-speaker-wave')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('target_type')
                            ->label('Target')
                            ->options([
                                'all' => 'Semua',
                                'teachers' => 'Guru',
                                'students' => 'Siswa',
                                'parents' => 'Orang Tua',
                                'specific_class' => 'Kelas Tertentu',
                            ])
                            ->default('all')
                            ->required(),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Kadaluarsa'),
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Pin / Sematkan'),
                    ]),

                Forms\Components\Section::make('Lampiran')
                    ->description('File lampiran pengumuman')
                    ->icon('heroicon-o-paper-clip')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Lampiran')
                            ->multiple()
                            ->directory('announcements/attachments')
                            ->maxSize(10240)
                            ->maxFiles(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Penulis')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('target_type')
                    ->label('Target')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'all' => 'Semua',
                        'teachers' => 'Guru',
                        'students' => 'Siswa',
                        'parents' => 'Orang Tua',
                        'specific_class' => 'Kelas Tertentu',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Dipublikasi')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Disematkan')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('target_type')
                    ->label('Target')
                    ->options([
                        'all' => 'Semua',
                        'teachers' => 'Guru',
                        'students' => 'Siswa',
                        'parents' => 'Orang Tua',
                        'specific_class' => 'Kelas Tertentu',
                    ]),
                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Disematkan'),
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
            ->defaultSort('published_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
