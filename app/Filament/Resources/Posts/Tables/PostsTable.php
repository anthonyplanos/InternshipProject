<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),
                ImageColumn::make('attachment')
                    ->label('Image')
                    ->getStateUsing(fn ($record): ?string => filled($record->attachment) && Storage::disk('public')->exists(ltrim($record->attachment, '/'))
                        ? asset('storage/' . ltrim($record->attachment, '/'))
                        : null)
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->imageSize(60)
                    ->square()
                    ->width(80)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
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
                    ForceDeleteBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
                    RestoreBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
                ])
                    ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
            ]);
    }
}
