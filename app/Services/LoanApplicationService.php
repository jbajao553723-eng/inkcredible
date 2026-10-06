<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Models\LoanType;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LoanApplicationService
{
    public function __construct(private readonly LoanPricingService $pricing) {}

    /**
     * @param  array{amount: mixed, purpose_choice: string, purpose_other?: string|null}  $data
     */
    public function submit(User $user, LoanType $loanType, array $data, UploadedFile $governmentId): Loan
    {
        $charges = $this->pricing->calculate((float) $data['amount'], $loanType);
        $amount = $charges['principal'];
        $interestAmount = $charges['interest'];
        $financeFee = $charges['finance_fee'];
        $processingFee = $charges['processing_fee'];
        $totalPayable = $charges['total_payable'];
        $installmentCount = $loanType->installmentCount();
        $repaymentPeriodDays = $loanType->repaymentPeriodDays();
        $purposeOptions = config("loan_purposes.{$loanType->name}", []);
        $purpose = $data['purpose_choice'] === 'other'
            ? trim((string) ($data['purpose_other'] ?? ''))
            : $purposeOptions[$data['purpose_choice']];
        $filePath = $governmentId->store('ids', config('filesystems.public_disk'));

        try {
            return DB::transaction(function () use ($user, $loanType, $amount, $purpose, $interestAmount, $financeFee, $processingFee, $totalPayable, $installmentCount, $repaymentPeriodDays, $filePath, $governmentId) {
                $terms = [
                    'terms_accepted_at' => now(),
                    'terms_version' => config('legal.loan_terms_version'),
                ];

                LoanApplication::create([
                    'user_id' => $user->id,
                    'loan_type_id' => $loanType->id,
                    'requested_amount' => $amount,
                    'purpose' => $purpose,
                    'calculated_interest' => $interestAmount,
                    'finance_fee' => $financeFee,
                    'processing_fee' => $processingFee,
                    'total_payable' => $totalPayable,
                    'installment_count' => $installmentCount,
                    'repayment_period_days' => $repaymentPeriodDays,
                    'status' => LoanApplication::STATUS_PENDING,
                    'submitted_at' => now(),
                    ...$terms,
                ]);

                $loan = Loan::create([
                    'user_id' => $user->id,
                    'loan_type_id' => $loanType->id,
                    'purpose' => $purpose,
                    'amount' => $amount,
                    'finance_fee' => $financeFee,
                    'processing_fee' => $processingFee,
                    'total_payable' => $totalPayable,
                    'installment_count' => $installmentCount,
                    'repayment_period_days' => $repaymentPeriodDays,
                    'paid_amount' => 0,
                    'status' => Loan::STATUS_PENDING,
                    'loan_code' => 'LN-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                    ...$terms,
                ]);

                $loan->loanDocuments()->create([
                    'document_type' => LoanDocument::TYPE_ID,
                    'file_path' => $filePath,
                    'original_filename' => $governmentId->getClientOriginalName(),
                    'mime_type' => $governmentId->getClientMimeType(),
                    'file_size' => $governmentId->getSize(),
                    'is_verified' => false,
                ]);

                return $loan;
            });
        } catch (Throwable $exception) {
            Storage::disk(config('filesystems.public_disk'))->delete($filePath);

            throw $exception;
        }
    }
}
