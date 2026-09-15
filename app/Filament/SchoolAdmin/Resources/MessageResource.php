<?php

namespace App\Filament\SchoolAdmin\Resources;

use App\Filament\Actions\MessageConversationAction;
use App\Filament\SchoolAdmin\Resources\MessageResource\Pages;
use App\Models\Message;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// School message center: every conversation in the school (one row per
// thread, latest message first). Admins can start a conversation with any
// user of the school and reply to conversations they take part in.
// Message content is plain text and is never rendered as HTML.
class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('id', Message::query()->selectRaw('MAX(id)')->groupBy('thread_id'))
            ->with('sender', 'recipient', 'student');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = Message::where('recipient_id', auth()->id())->whereNull('read_at')->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('New message'))
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('recipient_id')
                            ->label(__('To'))
                            ->options(fn () => User::query()
                                ->whereKeyNot(auth()->id())
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (User $user) => [$user->id => "{$user->name} · {$user->type?->label()}"]))
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('student_id')
                            ->label(__('About student'))
                            ->relationship('student', 'full_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('subject')
                            ->label(__('Subject'))
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('content')
                            ->label(__('Message'))
                            ->required()
                            ->rows(6)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.name')
                    ->label(__('From'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient.name')
                    ->label(__('To'))
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(__('Student'))
                    ->placeholder('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label(__('Subject'))
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('Last message'))
                    ->limit(60)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label(__('Read'))
                    ->boolean()
                    ->getStateUsing(fn (Message $record) => $record->read_at !== null),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Sent At'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('mine')
                    ->label(__('Only my conversations'))
                    ->query(fn (Builder $query) => $query->involving(auth()->id())),
                Tables\Filters\TernaryFilter::make('read')
                    ->label(__('Read Status'))
                    ->nullable()
                    ->trueLabel(__('Read'))
                    ->falseLabel(__('Unread'))
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                    ),
            ])
            ->actions([
                MessageConversationAction::make(),
                Tables\Actions\Action::make('deleteConversation')
                    ->label(__('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription(__('All messages in this conversation will be deleted.'))
                    ->action(fn (Message $record) => Message::where('thread_id', $record->thread_id)->delete()),
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
        return __('Communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('Messages');
    }

    public static function getModelLabel(): string
    {
        return __('Message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Messages');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
        ];
    }
}
