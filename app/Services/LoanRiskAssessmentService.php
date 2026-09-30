<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanType;
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

        [$level, $tone] = match (true) {
            $ratio === null => ['Cannot assess', 'neutral'],
            $ratio <= 30 => ['Low', 'success'],
            $ratio <= 50 => ['Moderate', 'warning'],
            $ratio <= 70 => ['High', 'danger'],
            default => ['Very high', 'danger'],
        };

        $rate = (float) ($loan->loanType?->interest_rate ?? 0);
        $availablePayment = $baseline['availablePayment'];
        $suggestedPrincipal = $rate >= 0 ? $availablePayment / (1 + ($rate / 100)) : $availablePayment;
        $suggestedPrincipal = floor($suggestedPrincipal / 100) * 100;

        $alternative = LoanType::active()
            ->whereKeyNot($loan->loan_type_id)
            ->where('min_amount', '<=', $suggestedPrincipal)
            ->where('max_amount', '>=', $suggestedPrincipal)
            ->orderBy('interest_rate')
            ->first();

        $suggestion = match (true) {
            $ratio === null => 'Monthly income is unavailable. Verify the client income before making a decision.',
            $ratio <= 30 => 'The requested amount is within the recommended 30% monthly repayment threshold.',
            $suggestedPrincipal >= (float) ($loan->loanType?->min_amount ?? 0) => 'Consider lowering the principal to PHP '.number_format($suggestedPrincipal, 2).($alternative ? ' or offering '.$alternative->display_name.'.' : '.'),
            $alternative !== null => 'The current amount is above the recommended threshold. Consider '.$alternative->display_name.' at a lower amount.',
            default => 'The request exceeds the recommended repayment threshold. Ask the client to reduce the amount or provide additional affordability information.',
        };

        return compact('income', 'existingCommitments', 'proposedCommitment', 'ratio', 'level', 'tone', 'suggestedPrincipal', 'alternative', 'suggestion');
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
            ->sum('total_payable');
        $availablePayment = max(0, ($income * 0.30) - $existingCommitments);

        return compact('income', 'existingCommitments', 'availablePayment');
    }
}
