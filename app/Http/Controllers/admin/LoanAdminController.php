<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;

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
    public function show($id)
    {
        $loan = Loan::with(['user', 'loanType', 'paymentSchedules'])
            ->findOrFail($id);

        return view('admin.loans.show', compact('loan'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        $loan = Loan::with('loanType')->findOrFail($id);

        // FIX: consistent status usage
        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approved_at' => now(),
            'disbursed_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE PAYMENT SCHEDULE IF NONE EXISTS
        |--------------------------------------------------------------------------
        */
        if (!$loan->paymentSchedules()->exists()) {

            $dueDays = $loan->loanType->due_days ?? 7;

            $loan->paymentSchedules()->create([
                'installment_number' => 1,
                'scheduled_amount' => $loan->total_payable ?? 0,
                'due_date' => now()->addDays($dueDays),
                'paid_amount' => 0,
                'status' => PaymentSchedule::STATUS_PENDING,
                'penalty_amount' => 0,
            ]);
        }

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

        // FIX: consistent status usage (IMPORTANT)
        $loan->update([
            'status' => Loan::STATUS_REJECTED
        ]);

        return back()->with('success', 'Loan rejected.');
    }
}