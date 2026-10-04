<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientVerificationApprovedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Inkcredible account is fully verified')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line('Your identity and account information have been reviewed and approved.')
            ->line('Your Inkcredible account is now fully verified, and you may submit a loan request from your dashboard.')
            ->action('Open your dashboard', route('dashboard'))
            ->line('Thank you for completing the verification process.')
            ->salutation('Regards, Inkcredible Lending Company');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account fully verified',
            'message' => 'Your identity and account information have been approved. You may now request a loan.',
            'url' => route('dashboard'),
            'severity' => 'success',
        ];
    }
}
