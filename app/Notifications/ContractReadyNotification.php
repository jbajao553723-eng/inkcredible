<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContractReadyNotification extends Notification
{
    use Queueable;

    public function __construct(public Loan $loan) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Loan contract ready for signature',
            'message' => 'Review, download, and sign the contract for '.($this->loan->loan_code ?: 'loan #'.$this->loan->id).'.',
            'url' => route('loan.contract.show', $this->loan),
            'loan_id' => $this->loan->id,
            'severity' => 'info',
        ];
    }
}
