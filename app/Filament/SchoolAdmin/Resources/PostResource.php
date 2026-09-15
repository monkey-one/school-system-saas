<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\SchoolAdmin\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// News and articles for the public school website.
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Section::make(__('Content'))
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->maxLength(200),
                            Forms\Components\Textarea::make('excerpt')
                                ->label(__('Summary'))
                                ->helperText(__('Shown on news lists and when the link is shared.'))
                                ->rows(2)
                                ->maxLength(500),
                            Forms\Components\RichEditor::make('content')
                                ->label(__('Content'))
                                ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'h2', 'h3', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])
                                ->required(),
                        ]),
                ])->columnSpan(['lg' => 2]),
                Forms\Components\Group::make([
                    Forms\Components\Section::make(__('Publishing'))
                        ->schema([
                            Forms\Components\Select::make('category')
                                ->label(__('Category'))
                                ->options(Post::categoryLabels())
                                ->default('news')
                                ->required(),
                            Forms\Components\Toggle::make('is_published')
                                ->label(__('Published'))
                                ->default(true),
                            Forms\Components\Toggle::make('is_featured')
                                ->label(__('Featured on home page')),
                            Forms\Components\DateTimePicker::make('published_at')
                                ->label(__('Publish date'))
                                ->default(now()),
                            Forms\Components\TextInput::make('slug')
                                ->label(__('URL slug'))
                                ->helperText(__('Leave empty to generate from the title.'))
                                ->alphaDash()
                                ->maxLength(200),
                        ]),
                    Forms\Components\Section::make(__('Cover image'))
                        ->schema([
                            Forms\Components\FileUpload::make('cover_image')
                                ->hiddenLabel()
                                ->image()
                                ->imageEditor()
                                ->directory('website/posts')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(2048),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->square()
                    ->size(48),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->limit(60)
                    ->description(fn (Post $record) => $record->excerpt ? \Illuminate\Support\Str::limit($record->excerpt, 80) : null),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('Category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Post::categoryLabels()[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_published')
                    ->label(__('Published'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('Featured'))
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('views')
                    ->label(__('Views'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('Publish date'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('Category'))
                    ->options(Post::categoryLabels()),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label(__('Published')),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('View'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Post $record) => route('website.news.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record) => $record->is_published),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Website');
    }

    public static function getNavigationLabel(): string
    {
        return __('News & Articles');
    }

    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('News & Articles');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
