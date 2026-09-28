<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\LoanApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    public function show(Loan $loan)
    {
        $loan->load(['user', 'loanType', 'paymentSchedules', 'loanDocuments', 'payments']);

        return view('admin.loans.show', compact('loan'));
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

        $approvals->approve($loan);

        return back()->with('success', 'Loan approved successfully.');
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
