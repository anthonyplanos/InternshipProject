<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
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
                    ->label('Role')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                ->label('Role')
                ->options([
                    'admin' => 'Admin',
                    'staff' => 'Staff',
                    'employee' => 'Employee',
                ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
                DeleteAction::make()
                    ->label('Deactivate')
                    ->modalHeading('Deactivate User')
                    ->modalDescription('This will deactivate the user account. The account can be reactivated later.')
                    ->modalSubmitActionLabel('Deactivate')
                    ->successNotificationTitle('User deactivated')
                    ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Deactivate Selected')
                        ->modalHeading('Deactivate Selected Users')
                        ->modalDescription('Selected user accounts will be deactivated and can be reactivated later.')
                        ->modalSubmitActionLabel('Deactivate')
                        ->successNotificationTitle('Users deactivated')
                        ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
                ])
                    ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            ]);
    }
}
