<?php

namespace App\Notifications;

use App\Services\EmailOtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(public string $code, public string $purpose) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isRecovery = $this->purpose === 'password-recovery';
        $isEmailVerification = $this->purpose === 'email-verification';
        $isTwoFactorLogin = $this->purpose === 'two-factor-login';
        $isTwoFactorSetup = $this->purpose === 'two-factor-setup';
        $recipientName = $notifiable->first_name ?: explode(' ', trim($notifiable->name))[0];

        $subject = match (true) {
            $isRecovery => 'Your Inkcredible password recovery code',
            $isEmailVerification => 'Verify your Inkcredible email address',
            $isTwoFactorLogin => 'Your Inkcredible sign-in code',
            $isTwoFactorSetup => 'Confirm Inkcredible two-factor authentication',
            default => 'Your Inkcredible security code',
        };

        $instruction = match (true) {
            $isRecovery => 'Use this verification code to continue resetting your Inkcredible password.',
            $isEmailVerification => 'Use this verification code to confirm your email address and activate dashboard access.',
            $isTwoFactorLogin => 'Use this verification code to complete your Inkcredible sign-in.',
            $isTwoFactorSetup => 'Use this verification code to enable two-factor authentication on your Inkcredible account.',
            default => 'Use this verification code to complete your security request.',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello '.($recipientName ?: 'there').',')
            ->line($instruction)
            ->line('Verification code: '.$this->code)
            ->line('This code expires in '.EmailOtpService::EXPIRES_IN_MINUTES.' minutes and can only be used once.')
            ->line('If you did not make this request, do not share the code and you can safely ignore this email.')
            ->salutation('Regards, Inkcredible');
    }
}
