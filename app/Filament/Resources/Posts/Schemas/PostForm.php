<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

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
                    ->getUploadedFileUsing(static function (BaseFileUpload $component, string $file, string | array | null $storedFileNames): ?array {
                        $normalizedPath = ltrim($file, '/');
                        $storage = Storage::disk('public');

                        if (! $storage->exists($normalizedPath)) {
                            return null;
                        }

                        $name = is_array($storedFileNames)
                            ? ($storedFileNames[$file] ?? $storedFileNames[$normalizedPath] ?? basename($normalizedPath))
                            : ($storedFileNames ?? basename($normalizedPath));

                        return [
                            'name' => $name,
                            'size' => 0,
                            'type' => null,
                            'url' => asset('storage/' . $normalizedPath),
                        ];
                    })
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->maxSize(config('uploads.post_attachment_max_kb'))
                    ->helperText('Maximum file size: ' . config('uploads.post_attachment_max_mb') . ' MB.')
                    ->nullable(),
            ]);
    }
}
