<?php

namespace App\Services;

use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\PaymentSchedule;
use App\Models\User;

class LoanRiskAssessmentService
{
    /** @return array<string, mixed> */
    public function assess(Loan $loan): array
    {
        $loan->loadMissing(['user.clientVerification', 'loanType']);
        $baseline = $this->clientBaseline($loan->user, $loan->id);
        $income = $baseline['income'];
        $existingCommitments = $baseline['existingCommitments'];
        $proposedCommitment = (float) $loan->total_payable;
        $ratio = $income > 0 ? round((($existingCommitments + $proposedCommitment) / $income) * 100, 1) : null;
        $behavior = $this->paymentBehavior($loan->user, $loan->id);
        $score = $this->score($ratio, $loan->user->clientVerification, $behavior);

        $rate = (float) ($loan->loanType?->interest_rate ?? 0);
        $availablePayment = $baseline['availablePayment'];
        $suggestedPrincipal = $rate >= 0 ? $availablePayment / (1 + ($rate / 100)) : $availablePayment;
        $suggestedPrincipal = max(0, floor($suggestedPrincipal / 100) * 100);

        $alternative = LoanType::active()
            ->whereKeyNot($loan->loan_type_id)
            ->where('min_amount', '<=', $suggestedPrincipal)
            ->where('max_amount', '>=', $suggestedPrincipal)
            ->orderBy('interest_rate')
            ->first();

        return [
            'income' => $income,
            'existingCommitments' => $existingCommitments,
            'proposedCommitment' => $proposedCommitment,
            'ratio' => $ratio,
            'suggestedPrincipal' => $suggestedPrincipal,
            'alternative' => $alternative,
            'suggestion' => $this->suggestion($ratio, $suggestedPrincipal, $loan, $alternative, $score, $behavior),
            ...$score,
            ...$behavior,
        ];
    }

    /** @return array<string, mixed> */
    public function profile(User $user): array
    {
        $user->loadMissing('clientVerification');
        $baseline = $this->clientBaseline($user);
        $ratio = $baseline['income'] > 0
            ? round(($baseline['existingCommitments'] / $baseline['income']) * 100, 1)
            : null;
        $behavior = $this->paymentBehavior($user);

        return [
            'income' => $baseline['income'],
            'existingCommitments' => $baseline['existingCommitments'],
            'ratio' => $ratio,
            ...$this->score($ratio, $user->clientVerification, $behavior),
            ...$behavior,
        ];
    }

    /** @return array{income: float, existingCommitments: float, availablePayment: float} */
    public function clientBaseline(User $user, ?int $excludedLoanId = null): array
    {
        $user->loadMissing('clientVerification');
        $income = (float) ($user->clientVerification?->monthly_income ?? 0);
        $existingCommitments = (float) Loan::query()
            ->where('user_id', $user->id)
            ->where('status', Loan::STATUS_APPROVED)
            ->when($excludedLoanId, fn ($query) => $query->where('id', '!=', $excludedLoanId))
            ->get()
            ->sum(fn (Loan $loan) => $loan->getRemainingBalance());
        $availablePayment = max(0, ($income * 0.30) - $existingCommitments);

        return compact('income', 'existingCommitments', 'availablePayment');
    }

    /** @return array{earlyPayments: int, onTimePayments: int, latePayments: int, completedLoans: int} */
    private function paymentBehavior(User $user, ?int $excludedLoanId = null): array
    {
        $schedules = PaymentSchedule::query()
            ->whereHas('loan', function ($query) use ($user, $excludedLoanId): void {
                $query->where('user_id', $user->id)
                    ->when($excludedLoanId, fn ($query) => $query->where('id', '!=', $excludedLoanId));
            })
            ->get(['due_date', 'paid_date', 'status']);

        $paidSchedules = $schedules->filter(fn (PaymentSchedule $schedule) => $schedule->paid_date !== null);
        $earlyPayments = $paidSchedules->filter(fn (PaymentSchedule $schedule) => $schedule->paid_date->lt($schedule->due_date))->count();
        $onTimePayments = $paidSchedules->filter(fn (PaymentSchedule $schedule) => $schedule->paid_date->lte($schedule->due_date))->count();
        $latePayments = $paidSchedules->filter(fn (PaymentSchedule $schedule) => $schedule->paid_date->gt($schedule->due_date))->count()
            + $schedules->where('status', PaymentSchedule::STATUS_OVERDUE)->count();
        $completedLoans = Loan::query()
            ->where('user_id', $user->id)
            ->where('status', Loan::STATUS_PAID)
            ->when($excludedLoanId, fn ($query) => $query->where('id', '!=', $excludedLoanId))
            ->count();

        return compact('earlyPayments', 'onTimePayments', 'latePayments', 'completedLoans');
    }

    /** @param array{earlyPayments: int, onTimePayments: int, latePayments: int, completedLoans: int} $behavior */
    private function score(?float $ratio, ?ClientVerification $verification, array $behavior): array
    {
        $baseScore = match (true) {
            $ratio === null => 30,
            $ratio <= 30 => 72,
            $ratio <= 50 => 57,
            $ratio <= 70 => 42,
            default => 25,
        };
        $verifiedPayslip = $verification?->status === ClientVerification::STATUS_APPROVED
            && $verification->payslip_verified_at !== null;
        $payslipImpact = $verifiedPayslip ? 10 : 0;
        $earlyImpact = min(12, $behavior['earlyPayments'] * 3);
        $onTimeImpact = min(8, $behavior['onTimePayments']);
        $completedImpact = min(10, $behavior['completedLoans'] * 5);
        $lateImpact = min(30, $behavior['latePayments'] * 8);
        $scoreAdjustment = $payslipImpact + $earlyImpact + $onTimeImpact + $completedImpact - $lateImpact;
        $readinessScore = max(0, min(100, $baseScore + $scoreAdjustment));

        [$level, $tone, $approvalOutlook] = match (true) {
            $readinessScore >= 75 => ['Low', 'success', 'Strong'],
            $readinessScore >= 55 => ['Moderate', 'warning', 'Improving'],
            $readinessScore >= 35 => ['High', 'danger', 'Cautious'],
            default => ['Very high', 'danger', 'Limited'],
        };

        $scoreFactors = [
            ['label' => 'Affordability', 'impact' => $baseScore, 'tone' => $ratio !== null && $ratio <= 30 ? 'positive' : 'neutral', 'detail' => $ratio === null ? 'Verified income is unavailable.' : number_format($ratio, 1).'% commitment-to-income ratio.'],
            ['label' => 'Verified payslip', 'impact' => $payslipImpact, 'tone' => $verifiedPayslip ? 'positive' : 'neutral', 'detail' => $verifiedPayslip ? 'Income evidence was reviewed by an administrator.' : 'No administrator-verified payslip bonus yet.'],
            ['label' => 'Early payments', 'impact' => $earlyImpact, 'tone' => $earlyImpact > 0 ? 'positive' : 'neutral', 'detail' => $behavior['earlyPayments'].' installment(s) paid before the due date.'],
            ['label' => 'Late or overdue', 'impact' => -$lateImpact, 'tone' => $lateImpact > 0 ? 'negative' : 'positive', 'detail' => $behavior['latePayments'].' late or currently overdue installment(s).'],
        ];

        return compact('readinessScore', 'scoreAdjustment', 'level', 'tone', 'approvalOutlook', 'verifiedPayslip', 'scoreFactors');
    }

    /** @param array<string, mixed> $score @param array<string, int> $behavior */
    private function suggestion(?float $ratio, float $suggestedPrincipal, Loan $loan, ?LoanType $alternative, array $score, array $behavior): string
    {
        if ($ratio === null) {
            return 'Monthly income is unavailable. Complete income verification and provide a recent payslip before a decision.';
        }

        if ($behavior['latePayments'] > 0) {
            return 'Recent late or overdue installments increase risk. Consistent on-time payments can improve future assessments.';
        }

        if ($score['readinessScore'] >= 75 && $ratio <= 30) {
            return 'Affordability, verified evidence, and repayment behavior support a strong review. Final approval still requires administrator judgment.';
        }

        if (! $score['verifiedPayslip']) {
            return 'A verified recent payslip may improve confidence in the declared income. The current affordability result remains '.$score['level'].' risk.';
        }

        if ($suggestedPrincipal >= (float) ($loan->loanType?->min_amount ?? 0)) {
            return 'Consider lowering the principal to PHP '.number_format($suggestedPrincipal, 2).($alternative ? ' or offering '.$alternative->display_name.'.' : '.');
        }

        return 'The request exceeds the recommended repayment threshold. Reduce the amount or provide additional affordability information.';
    }
}
