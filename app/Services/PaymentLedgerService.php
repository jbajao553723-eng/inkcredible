<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;

class PaymentLedgerService
{
    public function approve(
        Payment $payment,
        ?string $sessionId = null,
        ?string $providerPaymentId = null,
        ?string $method = null,
    ): void {
        DB::transaction(function () use ($payment, $sessionId, $providerPaymentId, $method): void {
            $payment = Payment::whereKey($payment->getKey())->lockForUpdate()->firstOrFail();

            if ($payment->status === Payment::STATUS_APPROVED) {
                return;
            }

            $payment->update([
                'status' => Payment::STATUS_APPROVED,
                'paid_at' => now(),
                'paymongo_session_id' => $sessionId ?: $payment->paymongo_session_id,
                'paymongo_payment_id' => $providerPaymentId ?: $payment->paymongo_payment_id,
                'method' => $method ?: $payment->method,
            ]);

            $this->recalculate($payment->loan()->lockForUpdate()->firstOrFail());
        });
    }

    public function recalculate(Loan $loan): void
    {
        $successfulPayments = Payment::query()
            ->whereBelongsTo($loan)
            ->where('status', Payment::STATUS_APPROVED)
            ->get(['amount']);
        $totalPaid = round((float) $successfulPayments->sum('amount'), 2);
        $totalDue = (float) $loan->getTotalWithPenalty();
        $unallocatedPaidAmount = $totalPaid;

        $loan->paymentSchedules()
            ->orderBy('installment_number')
            ->lockForUpdate()
            ->get()
            ->each(function (PaymentSchedule $schedule) use (&$unallocatedPaidAmount): void {
                $scheduledAmount = (float) $schedule->scheduled_amount;
                $allocatedAmount = min($unallocatedPaidAmount, $scheduledAmount);
                $unallocatedPaidAmount = round(max(0, $unallocatedPaidAmount - $allocatedAmount), 2);
                $isPaid = $allocatedAmount >= $scheduledAmount;
                $isOverdue = ! $isPaid && $schedule->due_date->isBefore(today());

                $schedule->update([
                    'paid_amount' => round($allocatedAmount, 2),
                    'paid_date' => $isPaid ? ($schedule->paid_date ?? today()) : null,
                    'status' => $isPaid
                        ? PaymentSchedule::STATUS_PAID
                        : ($isOverdue ? PaymentSchedule::STATUS_OVERDUE : PaymentSchedule::STATUS_PENDING),
                ]);
            });

        $loan->update([
            'paid_amount' => $totalPaid,
            'payment_count' => $successfulPayments->count(),
            'status' => $totalPaid >= $totalDue
                ? Loan::STATUS_PAID
                : ($loan->status === Loan::STATUS_PAID ? Loan::STATUS_APPROVED : $loan->status),
        ]);
    }
}
