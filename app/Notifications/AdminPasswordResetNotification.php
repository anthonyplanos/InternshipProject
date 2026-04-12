<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $temporaryPassword,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your password has been reset')
            ->greeting("Hello {$notifiable->name},")
            ->line('An administrator reset your account password.')
            ->line("Your new temporary password is: {$this->temporaryPassword}")
            ->line('Please log in using this password and change it immediately for security.')
            ->action('Login to Your Account', url('/login'))
            ->line('If you did not expect this reset, please contact support immediately.');
    }
}
