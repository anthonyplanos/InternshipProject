<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
            ForceDeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
            RestoreAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage') && (bool) $this->record?->trashed()),
        ];
    }
}