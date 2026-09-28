<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;

class LoanApprovalService
{
    public function approve(Loan $loan): void
    {
        DB::transaction(function () use ($loan): void {
            $loan = Loan::with('loanType')
                ->whereKey($loan->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== Loan::STATUS_PENDING) {
                return;
            }

            $installmentCount = $loan->loanType?->installmentCount()
                ?? max(1, (int) ($loan->installment_count ?? 1));
            $periodDays = $loan->loanType?->repaymentPeriodDays()
                ?? max(1, (int) ($loan->repayment_period_days ?? 7));
            $approvedAt = now();

            $loan->update([
                'status' => Loan::STATUS_APPROVED,
                'approved_at' => $approvedAt,
                'disbursed_at' => $approvedAt,
                'installment_count' => $installmentCount,
                'repayment_period_days' => $periodDays,
            ]);

            if ($loan->paymentSchedules()->exists()) {
                return;
            }

            $intervalDays = $installmentCount === 1
                ? $periodDays
                : (int) floor($periodDays / $installmentCount);
            $scheduleStart = $approvedAt->copy()->startOfDay();
            $totalCents = (int) round((float) $loan->total_payable * 100);
            $baseCents = intdiv($totalCents, $installmentCount);
            $remainderCents = $totalCents - ($baseCents * $installmentCount);

            $schedules = collect(range(1, $installmentCount))->map(function (int $installment) use ($baseCents, $remainderCents, $installmentCount, $scheduleStart, $intervalDays): array {
                $installmentCents = $baseCents + ($installment === $installmentCount ? $remainderCents : 0);

                return [
                    'installment_number' => $installment,
                    'scheduled_amount' => number_format($installmentCents / 100, 2, '.', ''),
                    'due_date' => $scheduleStart->copy()->addDays($intervalDays * $installment),
                    'paid_amount' => 0,
                    'status' => PaymentSchedule::STATUS_PENDING,
                    'penalty_amount' => 0,
                ];
            });

            $loan->paymentSchedules()->createMany($schedules->all());
        });
    }
}
