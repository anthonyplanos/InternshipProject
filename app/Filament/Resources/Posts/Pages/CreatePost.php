<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Category;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $categoryName = trim((string) ($data['category'] ?? ''));

        if ($categoryName !== '') {
            $category = Category::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($categoryName)])
                ->first();

            if (! $category) {
                $category = Category::create([
                    'name' => $categoryName,
                ]);
            }

            $data['category'] = $categoryName;
            $data['category_id'] = $category->id;
        }

        $data['user_id'] = auth()->id();

        return $data;
    }
}