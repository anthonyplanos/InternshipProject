<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Notifications\AdminPasswordResetNotification;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Throwable;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected ?string $roleBeforeSave = null;

    protected function beforeSave(): void
    {
        $this->roleBeforeSave = $this->record->roles()->orderBy('roles.id')->value('name');
    }

    protected function afterSave(): void
    {
        $this->record->syncRoleSnapshot();

        $roleAfterSave = $this->record->roles()->orderBy('roles.id')->value('name');

        if (strcasecmp((string) $this->roleBeforeSave, (string) $roleAfterSave) !== 0) {
            activity('account')
                ->causedBy(auth()->user())
                ->performedOn($this->record)
                ->event('role_updated')
                ->withProperties([
                    'source' => 'admin_role_update',
                    'ip' => request()->ip(),
                    'target_user_id' => $this->record->id,
                    'old_role' => $this->roleBeforeSave,
                    'new_role' => $roleAfterSave,
                ])
                ->log("Admin changed role from "
                    . ($this->roleBeforeSave ?? 'none')
                    . ' to '
                    . ($roleAfterSave ?? 'none'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset User Password')
                ->modalDescription('This will generate a new temporary password and email it to the user.')
                ->action(function (): void {
                    try {
                        $temporaryPassword = Str::password(14, true, true, true, false);

                        $this->record->forceFill([
                            'password' => $temporaryPassword,
                        ])->save();

                        $this->record->notify(new AdminPasswordResetNotification($temporaryPassword));
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('Password reset failed')
                            ->body('The password reset email could not be sent. Please try again.')
                            ->danger()
                            ->send();

                        return;
                    }

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
                        ->body('A temporary password was generated and sent to the user email.')
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
            DeleteAction::make()
                ->label('Deactivate')
                ->modalHeading('Deactivate User')
                ->modalDescription('This will deactivate the user account. The account can be reactivated later.')
                ->modalSubmitActionLabel('Deactivate')
                ->successNotificationTitle('User deactivated')
                ->visible(fn (): bool => (bool) auth()->user()?->can('users.manage')),
        ];
    }
}
