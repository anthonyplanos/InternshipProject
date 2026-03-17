<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('content')
                    ->label('Post Content')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                FileUpload::make('attachment')
                    ->label('Image')
                    ->disk('public')
                    ->directory('post-attachments')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->maxSize(5120)
                    ->nullable(),
            ]);
    }
}
