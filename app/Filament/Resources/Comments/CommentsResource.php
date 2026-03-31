<?php

namespace App\Filament\Resources\Comments;

use App\Filament\Resources\Comments\Pages\EditComments;
use App\Filament\Resources\Comments\Pages\ListComments;
use App\Filament\Resources\Comments\Schemas\CommentsForm;
use App\Filament\Resources\Comments\Tables\CommentsTable;
use App\Models\Comment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommentsResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Comments';

    protected static ?string $modelLabel = 'Comment';

    protected static ?string $pluralModelLabel = 'Comments';

    protected static ?string $recordTitleAttribute = 'content';

    public static function form(Schema $schema): Schema
    {
        return CommentsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommentsTable::configure($table);
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
            'index' => ListComments::route('/'),
            'edit' => EditComments::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['content', 'user.name', 'user.email', 'post.id'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return filled($record->parent_id)
            ? 'Reply #' . $record->id . ' - ' . Str::limit((string) $record->content, 50)
            : 'Comment #' . $record->id . ' - ' . Str::limit((string) $record->content, 50);
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => (string) ($record->user?->name ?? 'Unknown User'),
            'Post' => 'Post #' . (string) ($record->post_id ?? '-'),
            'Type' => filled($record->parent_id) ? 'Reply' : 'Comment',
        ];
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
        return false;
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

    protected static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return (bool) $user?->can($permission);
    }
}
