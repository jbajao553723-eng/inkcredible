<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\PaymentSchedule;
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
        $loans = Loan::with(['user', 'loanType', 'paymentSchedules', 'loanDocuments'])
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
        $loan = Loan::with(['user', 'loanType', 'paymentSchedules', 'loanDocuments', 'payments'])
            ->findOrFail($id);

        return view('admin.loans.show', compact('loan'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */
    public function approve($id): RedirectResponse
    {
        $loan = Loan::with('loanType')->findOrFail($id);

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loan requests can be approved.');
        }

        [$installmentCount, $periodDays] = match ($loan->loanType?->name) {
            'arawan' => [30, 30],
            'weekly', 'emergency' => [4, 28],
            default => [
                max(1, (int) ($loan->installment_count ?? 1)),
                max(1, (int) ($loan->repayment_period_days ?? $loan->loanType?->due_days ?? 7)),
            ],
        };
        $approvedAt = now();

        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approved_at' => $approvedAt,
            'disbursed_at' => $approvedAt,
            'installment_count' => $installmentCount,
            'repayment_period_days' => $periodDays,
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE PAYMENT SCHEDULE IF NONE EXISTS
        |--------------------------------------------------------------------------
        */
        if (! $loan->paymentSchedules()->exists()) {

            $intervalDays = $installmentCount === 1
                ? $periodDays
                : (int) floor($periodDays / $installmentCount);
            $scheduleStart = $approvedAt->copy()->startOfDay();
            $totalCents = (int) round(((float) ($loan->total_payable ?? 0)) * 100);
            $baseCents = intdiv($totalCents, $installmentCount);
            $remainingCents = $totalCents - ($baseCents * $installmentCount);

            for ($installment = 1; $installment <= $installmentCount; $installment++) {
                $installmentCents = $baseCents + ($installment === $installmentCount ? $remainingCents : 0);

                $loan->paymentSchedules()->create([
                    'installment_number' => $installment,
                    'scheduled_amount' => number_format($installmentCents / 100, 2, '.', ''),
                    'due_date' => $scheduleStart->copy()->addDays($intervalDays * $installment),
                    'paid_amount' => 0,
                    'status' => PaymentSchedule::STATUS_PENDING,
                    'penalty_amount' => 0,
                ]);
            }
        }

        return back()->with('success', 'Loan approved successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, $id): RedirectResponse
    {
        $loan = Loan::findOrFail($id);

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
