<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Notifications\LoanApprovedNotification;
use App\Services\LoanApprovalService;
use App\Services\LoanRiskAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoanAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ALL LOANS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        // FIX: eager load EVERYTHING needed for dashboard consistency
        $loans = Loan::with(['user', 'loanType', 'paymentSchedules'])
            ->latest()
            ->get();

        return view('admin.loans.index', compact('loans'));
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW SINGLE LOAN
    |--------------------------------------------------------------------------
    */
    public function show(Loan $loan, LoanRiskAssessmentService $riskAssessments)
    {
        $loan->load(['user.clientVerification', 'loanType', 'paymentSchedules', 'loanDocuments', 'payments']);
        $riskAssessment = $riskAssessments->assess($loan);

        return view('admin.loans.show', compact('loan', 'riskAssessment'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */
    public function approve(Loan $loan, LoanApprovalService $approvals): RedirectResponse
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loan requests can be approved.');
        }

        if (! $loan->contract_sent_at) {
            return back()->with('error', 'Send the final contract to the client before approval.');
        }

        if (! $loan->contract_signed_at
            || ! $loan->signed_contract_path
            || ! Storage::disk('local')->exists($loan->signed_contract_path)) {
            return back()->with('error', 'The client must upload and return the signed PDF before final approval.');
        }

        $approvals->approve($loan);
        $loan->refresh();
        $loan->user?->notify(new LoanApprovedNotification($loan));

        return back()->with('success', 'Signed contract verified and loan final-approved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loan requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500', 'regex:/\S/'],
        ]);

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
            'rejection_reason' => trim($validated['rejection_reason']),
        ]);

        return back()->with('success', 'Loan rejected with a reason for the client.');
    }
}
