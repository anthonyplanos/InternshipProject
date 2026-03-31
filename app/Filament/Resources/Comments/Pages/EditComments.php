<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditComments extends EditRecord
{
    protected static string $resource = CommentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Delete Comment')
                ->modalDescription('This will permanently delete the selected comment or reply.')
                ->modalSubmitActionLabel('Delete')
                ->visible(fn (): bool => (bool) auth()->user()?->can('posts.manage')),
        ];
    }
}
