<?php

namespace App\Notifications;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        Log::info('Password Reset Email Details (Simplified Attempt)', [
            'to' => $notifiable->getEmailForPasswordReset(),
            'url' => $url,
        ]);

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->view('emails.reset_password', [
                'name' => $notifiable->name,
                'url' => $url,
            ]);
    }

    protected function resetUrl(mixed $notifiable): string
    {
        return Filament::getResetPasswordUrl($this->token, $notifiable, ['EMAIL_PEGAWAI' => $notifiable->getEmailForPasswordReset()]);
    }
}
