<?php

namespace App\Filament\Resources\InactiveUsers;

use App\Filament\Resources\InactiveUsers\Pages\ListInactiveUsers;
use App\Filament\Resources\InactiveUsers\Schemas\InactiveUsersForm;
use App\Filament\Resources\InactiveUsers\Tables\InactiveUsersTable;
use App\Models\InactiveUsers;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InactiveUsersResource extends Resource
{
    protected static ?string $model = InactiveUsers::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Deactivated Accounts';

    protected static ?string $modelLabel = 'Deactivated Account';

    protected static ?string $pluralModelLabel = 'Deactivated Accounts';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return InactiveUsersForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InactiveUsersTable::configure($table);
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
            'index' => ListInactiveUsers::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'role'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return (string) $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Email' => (string) $record->email,
            'Role' => (string) ($record->role ?? '-'),
            'Deactivated' => optional($record->deleted_at)?->format('M d, Y h:i A') ?? '-',
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->onlyTrashed();
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
