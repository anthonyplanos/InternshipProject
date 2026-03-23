<?php

namespace App\Filament\Resources\Posts;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Schemas\PostForm;
use App\Filament\Resources\Posts\Tables\PostsTable;
use App\Models\Post;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return static::userCan('posts.view') || static::userCan('posts.manage');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canDeleteAny(): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canRestore(Model $record): bool
    {
        return static::userCan('posts.manage');
    }

    public static function canRestoreAny(): bool
    {
        return static::userCan('posts.manage');
    }

    protected static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return (bool) $user?->can($permission);
    }
}
