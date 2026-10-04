<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PenaltyAppliedNotification extends Notification
{
    use Queueable;

    public function __construct(public Loan $loan, public float $increase) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loanReference = $this->loan->loan_code ?: 'loan #'.$this->loan->id;

        return (new MailMessage)
            ->subject('Overdue loan payment notice')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line('A payment for '.$loanReference.' is overdue.')
            ->line('An additional penalty of PHP '.number_format($this->increase, 2).' has been applied based on the current overdue balance.')
            ->action('Review payment details', route('payments.index'))
            ->line('Please make or arrange payment as soon as possible to prevent additional penalties.')
            ->salutation('Regards, Inkcredible Lending Company');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Loan penalty updated',
            'message' => 'A PHP '.number_format($this->increase, 2).' penalty was added to '.($this->loan->loan_code ?: 'loan #'.$this->loan->id).' because a payment is overdue.',
            'url' => route('payments.index'),
            'loan_id' => $this->loan->id,
            'severity' => 'warning',
        ];
    }
}
