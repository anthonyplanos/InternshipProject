<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Models\Activity;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $modelLabel = 'Activity Log';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event', 'description', 'log_name', 'causer.name', 'causer.email', 'properties'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return Str::headline((string) $record->event) . ' - ' . Str::limit((string) $record->description, 60);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Log' => (string) ($record->log_name ?? '-'),
            'Actor' => (string) ($record->causer?->name ?? 'System'),
        ];
    }

    public static function canViewAny(): bool
    {
        return static::userCan('logs.view');
    }

    public static function canView(Model $record): bool
    {
        return static::userCan('logs.view');
    }

    protected static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return (bool) $user?->can($permission);
    }
}
