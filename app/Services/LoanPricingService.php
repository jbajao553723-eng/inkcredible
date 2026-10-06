<?php

namespace App\Services;

use App\Models\LoanType;

class LoanPricingService
{
    public const FINANCE_FEE_RATE = 5.00;

    public const PROCESSING_FEE_PER_BLOCK = 100.00;

    public const PROCESSING_FEE_BLOCK_SIZE = 5000.00;

    /** @return array{principal: float, interest: float, finance_fee: float, processing_fee: float, total_payable: float} */
    public function calculate(float $amount, LoanType $loanType): array
    {
        $principal = round(max(0, $amount), 2);
        $interest = round($principal * ((float) $loanType->interest_rate / 100), 2);
        $financeFee = round($principal * (self::FINANCE_FEE_RATE / 100), 2);
        $processingFee = $principal > 0
            ? ceil($principal / self::PROCESSING_FEE_BLOCK_SIZE) * self::PROCESSING_FEE_PER_BLOCK
            : 0.0;

        return [
            'principal' => $principal,
            'interest' => $interest,
            'finance_fee' => $financeFee,
            'processing_fee' => round($processingFee, 2),
            'total_payable' => round($principal + $interest + $financeFee + $processingFee, 2),
        ];
    }

    public function affordablePrincipal(float $availablePayment, LoanType $loanType): float
    {
        $candidate = floor(min($availablePayment, (float) $loanType->max_amount) / 100) * 100;

        while ($candidate > 0 && $this->calculate($candidate, $loanType)['total_payable'] > $availablePayment) {
            $candidate -= 100;
        }

        return max(0, $candidate);
    }
}
