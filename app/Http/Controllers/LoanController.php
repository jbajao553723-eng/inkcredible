<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanRequest;
use App\Models\Loan;
use App\Models\LoanType;
use App\Services\LoanApplicationService;
use App\Services\LoanRiskAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CLIENT LOANS PAGE
    |--------------------------------------------------------------------------
    */
    public function index(): View
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
    public function create(LoanRiskAssessmentService $riskAssessments): View
    {
        $loanTypes = LoanType::active()
            ->orderBy('min_amount')
            ->get();

        $purposeOptions = config('loan_purposes');
        $affordability = $riskAssessments->clientBaseline(auth()->user());

        return view('client.loans.create', compact('loanTypes', 'purposeOptions', 'affordability'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE LOAN REQUEST
    |--------------------------------------------------------------------------
    */
    public function store(StoreLoanRequest $request, LoanApplicationService $applications): RedirectResponse
    {
        $validated = $request->validated();

        $loanType = LoanType::where('name', $validated['loan_type'])
            ->active()
            ->firstOrFail();

        $amount = (float) $validated['amount'];

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

        $applications->submit($request->user(), $loanType, $validated, $request->file('government_id'));

        return redirect()->route('dashboard')
            ->with('success', 'Loan request submitted successfully. Your loan is now pending approval.');
    }
}
