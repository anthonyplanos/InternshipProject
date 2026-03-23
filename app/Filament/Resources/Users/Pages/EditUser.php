<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        $this->record->syncRoleSnapshot();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            ForceDeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            RestoreAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
        ];
    }
}
