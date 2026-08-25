<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | USER PAYMENT PAGE
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $user = auth()->user();

        $loans = Loan::where('user_id', $user->id)
            ->where('status', 'approved')
            ->with('payments')
            ->get();

        return view('payments.index', compact('loans'));
    }

    /*
    |----------------------------------------------------------------------
    | STORE PAYMENT (FIXED)
    |----------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:gcash,cash',
            'proof' => 'required_if:method,gcash|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $proofPath = null;

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payments', 'public');
        }

        /*
        |------------------------------------------------------------------
        | ONLY INSERT PAYMENT (NO LOAN UPDATE HERE)
        |------------------------------------------------------------------
        */
        Payment::create([
            'loan_id' => $loan->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'proof' => $proofPath,
            'status' => 'pending',
        ]);

        return redirect()->route('payments.index')
            ->with('success', 'Payment submitted successfully! Awaiting approval.');
    }

    /*
    |----------------------------------------------------------------------
    | ADMIN PAYMENTS LIST
    |----------------------------------------------------------------------
    */
    public function adminIndex()
    {
        $payments = Payment::with(['loan.user'])
            ->latest()
            ->get();

        return view('admin.payments.index', compact('payments'));
    }

    /*
    |----------------------------------------------------------------------
    | SHOW PAYMENT
    |----------------------------------------------------------------------
    */
    public function adminShow($id)
    {
        $payment = Payment::with(['loan.user'])
            ->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /*
    |----------------------------------------------------------------------
    | APPROVE PAYMENT (FIXED)
    |----------------------------------------------------------------------
    */
    public function approve($id): RedirectResponse
    {
        $payment = Payment::with('loan')->findOrFail($id);

        if ($payment->status === 'approved') {
            return back()->with('success', 'Payment already approved.');
        }

        $payment->update(['status' => 'approved']);

        /*
        |------------------------------------------------------------------
        | RECALCULATE TOTAL (NO DUPLICATION)
        |------------------------------------------------------------------
        */
        $loan = $payment->loan;

        $totalPaid = Payment::where('loan_id', $loan->id)
            ->where('status', 'approved')
            ->sum('amount');

        $loan->paid_amount = $totalPaid;

        if ($totalPaid >= $loan->total_payable) {
            $loan->status = 'paid';
        }

        $loan->save();

        return back()->with('success', 'Payment approved successfully!');
    }

    /*
    |----------------------------------------------------------------------
    | REJECT PAYMENT
    |----------------------------------------------------------------------
    */
    public function reject($id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);

        $payment->update(['status' => 'rejected']);

        return back()->with('success', 'Payment rejected!');
    }
}