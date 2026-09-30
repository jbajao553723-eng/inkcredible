<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PenaltyAppliedNotification extends Notification
{
    use Queueable;

    public function __construct(public Loan $loan, public float $increase) {}

    public function via(object $notifiable): array
    {
        return ['database'];
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
