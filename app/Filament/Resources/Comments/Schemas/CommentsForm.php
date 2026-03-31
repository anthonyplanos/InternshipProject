<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CommentsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('post_id')
                    ->label('Post')
                    ->relationship('post', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => 'Post #' . $record->id . ' - ' . Str::limit((string) $record->content, 50))
                    ->searchable()
                    ->disabled()
                    ->dehydrated(false),
                Select::make('user_id')
                    ->label('Author')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->disabled()
                    ->dehydrated(false),
                Select::make('parent_id')
                    ->label('Reply To')
                    ->relationship('parent', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record): string => 'Comment #' . $record->id . ' - ' . Str::limit((string) $record->content, 40))
                    ->searchable()
                    ->placeholder('Top-level comment')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('content')
                    ->label('Content')
                    ->required()
                    ->rows(5)
                    ->maxLength(400)
                    ->columnSpanFull(),
            ]);
    }
}
