<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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
                    ->formatStateUsing(fn (?string $state): ?string => match ($state) {
                        'deleted' => 'deactivated',
                        'restored' => 'reactivated',
                        'force_deleted' => 'force deleted',
                        default => $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        'force_deleted' => 'gray',
                        'login' => 'success',
                        'logout' => 'gray',
                        'login_failed' => 'danger',
                        'password_updated' => 'warning',
                        'role_updated' => 'info',
                        default => 'primary',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $table = $query->getModel()->getTable();
                        $driver = DB::connection($query->getModel()->getConnectionName())->getDriverName();

                        $jsonUserExpressions = match ($driver) {
                            'pgsql' => [
                                "properties->'old'->>'name'",
                                "properties->'old'->>'email'",
                                "properties->'attributes'->>'name'",
                                "properties->'attributes'->>'email'",
                                "properties->>'email'",
                            ],
                            'sqlite' => [
                                "json_extract(properties, '$.old.name')",
                                "json_extract(properties, '$.old.email')",
                                "json_extract(properties, '$.attributes.name')",
                                "json_extract(properties, '$.attributes.email')",
                                "json_extract(properties, '$.email')",
                            ],
                            'sqlsrv' => [
                                "JSON_VALUE(properties, '$.old.name')",
                                "JSON_VALUE(properties, '$.old.email')",
                                "JSON_VALUE(properties, '$.attributes.name')",
                                "JSON_VALUE(properties, '$.attributes.email')",
                                "JSON_VALUE(properties, '$.email')",
                            ],
                            default => [
                                "JSON_UNQUOTE(JSON_EXTRACT(properties, '$.old.name'))",
                                "JSON_UNQUOTE(JSON_EXTRACT(properties, '$.old.email'))",
                                "JSON_UNQUOTE(JSON_EXTRACT(properties, '$.attributes.name'))",
                                "JSON_UNQUOTE(JSON_EXTRACT(properties, '$.attributes.email'))",
                                "JSON_UNQUOTE(JSON_EXTRACT(properties, '$.email'))",
                            ],
                        };

                        return $query
                            ->where('description', 'like', "%{$search}%")
                            ->orWhereExists(function ($subQuery) use ($table, $search): void {
                                $subQuery
                                    ->selectRaw('1')
                                    ->from('users')
                                    ->whereColumn('users.id', "{$table}.causer_id")
                                    ->where("{$table}.causer_type", User::class)
                                    ->where(function ($userQuery) use ($search): void {
                                        $userQuery
                                            ->where('users.name', 'like', "%{$search}%")
                                            ->orWhere('users.email', 'like', "%{$search}%");
                                    });
                            })
                            ->orWhereExists(function ($subQuery) use ($table, $search): void {
                                $subQuery
                                    ->selectRaw('1')
                                    ->from('users')
                                    ->whereColumn('users.id', "{$table}.subject_id")
                                    ->where("{$table}.subject_type", User::class)
                                    ->where(function ($userQuery) use ($search): void {
                                        $userQuery
                                            ->where('users.name', 'like', "%{$search}%")
                                            ->orWhere('users.email', 'like', "%{$search}%");
                                    });
                            })
                            ->orWhere(function (Builder $jsonQuery) use ($jsonUserExpressions, $search): void {
                                foreach ($jsonUserExpressions as $index => $expression) {
                                    if ($index === 0) {
                                        $jsonQuery->whereRaw("{$expression} like ?", ["%{$search}%"]);

                                        continue;
                                    }

                                    $jsonQuery->orWhereRaw("{$expression} like ?", ["%{$search}%"]);
                                }
                            });
                    })
                    ->wrap()
                    ->limit(80),
                TextColumn::make('causer.name')
                    ->label('Actor')
                    ->state(fn (Activity $record): string => $record->causer?->name
                        ?? (($record->subject_type === User::class) ? $record->subject?->name : null)
                        ?? data_get($record->properties, 'old.name')
                        ?? data_get($record->properties, 'attributes.name')
                        ?? (filled(data_get($record->properties, 'email')) ? 'Guest' : null)
                        ?? 'System')
                    ->searchable(),
                TextColumn::make('causer.email')
                    ->label('Actor Email')
                    ->state(fn (Activity $record): string => $record->causer?->email
                        ?? (($record->subject_type === User::class) ? $record->subject?->email : null)
                        ?? data_get($record->properties, 'old.email')
                        ?? data_get($record->properties, 'attributes.email')
                        ?? data_get($record->properties, 'email')
                        ?? '-')
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
                        'post' => 'Post',
                        'default' => 'Default',
                    ]),
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deactivated',
                        'restored' => 'Reactivated',
                        'force_deleted' => 'Force Deleted',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'login_failed' => 'Failed Login',
                        'password_updated' => 'Password Updated',
                        'role_updated' => 'Role Updated',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
