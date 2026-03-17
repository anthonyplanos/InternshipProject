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

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content')
                    ->limit(80)
                    ->searchable(),
                ImageColumn::make('attachment')
                    ->label('Image')
                    ->url(fn ($record): ?string => $record->attachment ? asset('storage/' . $record->attachment) : null)
                    ->height(60)
                    ->width(80)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
