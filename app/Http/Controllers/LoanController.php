<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanDocument;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CLIENT LOANS PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        // FIX: eager load loanType so dashboard does not break
        $loans = Loan::with('loanType')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('client.loans.index', compact('loans'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE LOAN PAGE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $loanTypes = LoanType::active()
            ->orderBy('min_amount')
            ->get();

        $purposeOptions = config('loan_purposes');

        return view('client.loans.create', compact('loanTypes', 'purposeOptions'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE LOAN REQUEST
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $purposeOptions = config('loan_purposes');
        $allowedPurposes = array_keys($purposeOptions[$request->input('loan_type')] ?? []);

        $request->validate([
            'loan_type' => 'required|in:arawan,weekly,emergency',
            'amount' => 'required|numeric|min:1',
            'government_id' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'purpose_choice' => ['required', Rule::in($allowedPurposes)],
            'purpose_other' => ['nullable', 'string', 'max:255', 'required_if:purpose_choice,other', 'regex:/\S/'],
            'loan_terms_accepted' => ['accepted'],
        ]);

        $loanType = LoanType::where('name', $request->loan_type)
            ->active()
            ->firstOrFail();

        $amount = $request->amount;

        if ($amount < $loanType->min_amount || $amount > $loanType->max_amount) {
            return back()
                ->with('error', sprintf(
                    '%s amount must be between PHP %s and PHP %s.',
                    $loanType->display_name,
                    number_format((float) $loanType->min_amount, 2),
                    number_format((float) $loanType->max_amount, 2)
                ))
                ->withInput();
        }

        $interestAmount = $amount * ($loanType->interest_rate / 100);
        $totalPayable = $amount + $interestAmount;
        $installmentCount = match ($loanType->name) {
            'arawan' => 30,
            'weekly', 'emergency' => 4,
            default => 1,
        };
        $repaymentPeriodDays = match ($loanType->name) {
            'arawan' => 30,
            'weekly', 'emergency' => 28,
            default => max(1, (int) $loanType->due_days),
        };
        $purpose = $request->purpose_choice === 'other'
            ? trim($request->purpose_other)
            : $purposeOptions[$loanType->name][$request->purpose_choice];

        $filePath = $request->file('government_id')->store('ids', 'public');

        /*
        |--------------------------------------------------------------------------
        | LOAN APPLICATION (PENDING)
        |--------------------------------------------------------------------------
        */
        LoanApplication::create([
            'user_id' => auth()->id(),
            'loan_type_id' => $loanType->id,
            'requested_amount' => $amount,
            'purpose' => $purpose,
            'calculated_interest' => $interestAmount,
            'total_payable' => $totalPayable,
            'installment_count' => $installmentCount,
            'repayment_period_days' => $repaymentPeriodDays,
            'status' => LoanApplication::STATUS_PENDING,
            'submitted_at' => now(),
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.loan_terms_version'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | LOAN RECORD (FIXED + SAFE)
        |--------------------------------------------------------------------------
        */
        $loan = Loan::create([
            'user_id' => auth()->id(),
            'loan_type_id' => $loanType->id,
            'purpose' => $purpose,
            'amount' => $amount,
            'total_payable' => $totalPayable,
            'installment_count' => $installmentCount,
            'repayment_period_days' => $repaymentPeriodDays,
            'paid_amount' => 0,
            'status' => Loan::STATUS_PENDING,
            'loan_code' => 'LN-'.date('Ymd').'-'.rand(1000, 9999),
            'terms_accepted_at' => now(),
            'terms_version' => config('legal.loan_terms_version'),
        ]);

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT
        |--------------------------------------------------------------------------
        */
        LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => LoanDocument::TYPE_ID,
            'file_path' => $filePath,
            'original_filename' => $request->file('government_id')->getClientOriginalName(),
            'mime_type' => $request->file('government_id')->getClientMimeType(),
            'file_size' => $request->file('government_id')->getSize(),
            'is_verified' => false,
        ]);

        return redirect('/dashboard')
            ->with('success', 'Loan request submitted successfully. Your loan is now pending approval.');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
        ]);

        return back()->with('success', 'Loan approved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */
    public function reject($id)
    {
        $loan = Loan::findOrFail($id);

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
        ]);

        return back()->with('success', 'Loan rejected successfully.');
    }
}
