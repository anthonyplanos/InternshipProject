<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
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
        $defaultPassword = (string) config('users.default_password', 'Password123!');

        return [
            Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset User Password')
                ->modalDescription('This will reset the user password to the configured default password.')
                ->action(function () use ($defaultPassword): void {
                    $this->record->forceFill([
                        'password' => $defaultPassword,
                    ])->save();

                    activity('account')
                        ->causedBy(auth()->user())
                        ->performedOn($this->record)
                        ->event('password_updated')
                        ->withProperties([
                            'source' => 'admin_reset',
                            'ip' => request()->ip(),
                            'target_user_id' => $this->record->id,
                            'target_user_name' => $this->record->name,
                            'target_user_email' => $this->record->email,
                        ])
                        ->log("Admin reset password for {$this->record->name} ({$this->record->email})");

                    Notification::make()
                        ->title('Password reset complete')
                        ->body('The user password was reset to the default password.')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            DeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            ForceDeleteAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            RestoreAction::make()
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
        ];
    }
}
