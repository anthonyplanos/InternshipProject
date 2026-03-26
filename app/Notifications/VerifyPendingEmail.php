<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyPendingEmail extends Notification
{
    use Queueable;

    public function __construct(
        protected User $user,
        protected string $pendingEmail,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'profile.email.verify',
            now()->addMinutes(60),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1(strtolower($this->pendingEmail)),
            ],
        );

        return (new MailMessage)
            ->subject('Verify your new email address')
            ->line('You requested to change your account email address.')
            ->line('Click the button below to verify this new email before it can be saved to your account.')
            ->action('Verify New Email', $verificationUrl)
            ->line('If you did not request this change, no further action is required.');
    }
}
