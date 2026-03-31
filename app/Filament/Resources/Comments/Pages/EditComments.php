<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditComments extends EditRecord
{
    protected static string $resource = CommentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage'))
                ->modalHeading('Delete'),
            ForceDeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage'))
                ->modalHeading('Force Delete'),
            RestoreAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage') && (bool) $this->record?->trashed()),
        ];
    }
}
