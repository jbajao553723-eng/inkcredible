<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $loans = Loan::with(['loanType', 'paymentSchedules', 'payments'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $pendingPayments = Payment::whereHas('loan', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'pending')->get();

        $activeLoan = $loans->first(function ($loan) {
            return strtolower($loan->status) === 'approved';
        });

        return view('dashboard', compact(
            'loans',
            'pendingPayments',
            'activeLoan'
        ));
    }
}