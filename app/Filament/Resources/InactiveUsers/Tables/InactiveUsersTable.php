<?php

namespace App\Filament\Resources\InactiveUsers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InactiveUsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->badge()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label('Deactivated At')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('deleted_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                RestoreAction::make()
                    ->label('Reactivate')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Reactivate Account')
                    ->modalDescription('This will reactivate the selected account.')
                    ->modalSubmitActionLabel('Reactivate')
                    ->successNotificationTitle('User reactivated')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
                ForceDeleteAction::make()
                    ->label('Delete')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Account Permanently')
                    ->modalDescription('This will permanently delete the selected account and cannot be undone.')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->label('Reactivate Selected')
                        ->requiresConfirmation()
                        ->modalHeading('Reactivate Selected Accounts')
                        ->modalDescription('This will reactivate all selected accounts.')
                        ->modalSubmitActionLabel('Reactivate')
                        ->successNotificationTitle('Users reactivated')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
                    ForceDeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Accounts Permanently')
                        ->modalDescription('This will permanently delete all selected accounts and cannot be undone.')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
                ]),
            ]);
    }
}
