<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanApprovedNotification extends Notification
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
            'title' => 'Loan final-approved',
            'message' => ($this->loan->loan_code ?: 'Your loan').' is approved. Your repayment schedule is now available.',
            'url' => route('dashboard'),
            'loan_id' => $this->loan->id,
            'severity' => 'success',
        ];
    }
}
