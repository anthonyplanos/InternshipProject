<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->limit(80),
                TextColumn::make('causer.name')
                    ->label('Actor')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('causer.email')
                    ->label('Actor Email')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('properties.ip')
                    ->label('IP')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Recorded At')
                    ->timezone(config('app.timezone'))
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log Type')
                    ->options([
                        'account' => 'Account',
                        'auth' => 'Authentication',
                        'default' => 'Default',
                    ]),
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'restored' => 'Restored',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'login_failed' => 'Failed Login',
                        'password_updated' => 'Password Updated',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
