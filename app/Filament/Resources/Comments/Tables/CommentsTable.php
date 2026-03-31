<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->state(fn ($record): string => filled($record->parent_id) ? 'Reply' : 'Comment')
                    ->badge()
                    ->color(fn ($state): string => $state === 'Reply' ? 'warning' : 'success'),
                TextColumn::make('content')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->placeholder('Deactivated User')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Author Email')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('post_id')
                    ->label('Post')
                    ->formatStateUsing(fn ($state): string => 'Post #' . (string) $state)
                    ->sortable(),
                TextColumn::make('parent_id')
                    ->label('Reply To')
                    ->formatStateUsing(fn ($state): string => filled($state) ? ('Comment #' . $state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'comment' => 'Comment',
                        'reply' => 'Reply',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'comment' => $query->whereNull('parent_id'),
                            'reply' => $query->whereNotNull('parent_id'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
                DeleteAction::make()
                    ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
                ]),
            ]);
    }
}
