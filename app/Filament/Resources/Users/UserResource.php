<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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
        ];
    }

    public static function canViewAny(): bool
    {
        return static::userCan('users.view') || static::userCan('users.manage');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::userCan('users.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCan('users.manage');
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCan('users.manage');
    }

    public static function canDeleteAny(): bool
    {
        return static::userCan('users.manage');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::userCan('users.manage');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::userCan('users.manage');
    }

    public static function canRestore(Model $record): bool
    {
        return static::userCan('users.manage');
    }

    public static function canRestoreAny(): bool
    {
        return static::userCan('users.manage');
    }

    protected static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return (bool) $user?->can($permission);
    }
}
