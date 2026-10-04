<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Loan $loan) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loanReference = $this->loan->loan_code ?: 'loan #'.$this->loan->id;

        return (new MailMessage)
            ->subject('Your loan has been approved')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line($loanReference.' has been fully approved.')
            ->line('Your repayment schedule and final signed contract are available in your dashboard.')
            ->action('View loan dashboard', route('dashboard'))
            ->line('Please review the due dates in your repayment schedule.')
            ->salutation('Regards, Inkcredible Lending Company');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Loan final-approved',
            'message' => ($this->loan->loan_code ?: 'Your loan').' is approved. Your repayment schedule is now available.',
            'url' => route('dashboard'),
            'loan_id' => $this->loan->id,
            'severity' => 'success',
        ];
    }
}
