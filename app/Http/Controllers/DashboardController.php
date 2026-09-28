<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $loans = Loan::with(['loanType', 'paymentSchedules', 'payments'])
            ->whereBelongsTo($user)
            ->latest()
            ->get();

        $pendingPayments = $loans
            ->flatMap->payments
            ->where('status', Payment::STATUS_PENDING)
            ->values();

        $activeLoan = $loans->firstWhere('status', Loan::STATUS_APPROVED);

        return view('dashboard', compact(
            'loans',
            'pendingPayments',
            'activeLoan'
        ));
    }
}
