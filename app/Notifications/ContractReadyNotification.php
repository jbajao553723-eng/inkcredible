<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractReadyNotification extends Notification
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
            ->subject('Your loan contract is ready to sign')
            ->greeting('Hello '.$notifiable->full_name.',')
            ->line('The contract for '.$loanReference.' is now available in your Inkcredible dashboard.')
            ->line('Please review and digitally sign it so the administrator can complete the final approval.')
            ->action('Review and sign contract', route('loan.contract.show', $this->loan))
            ->line('If you did not submit this loan request, please contact Inkcredible support.')
            ->salutation('Regards, Inkcredible Lending Company');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Loan contract ready for signature',
            'message' => 'Review and digitally sign the contract for '.($this->loan->loan_code ?: 'loan #'.$this->loan->id).'.',
            'url' => route('loan.contract.show', $this->loan),
            'loan_id' => $this->loan->id,
            'severity' => 'info',
        ];
    }
}
