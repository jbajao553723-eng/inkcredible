<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use App\Services\LoanRiskAssessmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(LoanRiskAssessmentService $riskAssessments): View|RedirectResponse
    {
        $user = request()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.security.dashboard');
        }

        if ($user->role === User::ROLE_ADMIN) {
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
        $riskProfile = $riskAssessments->profile($user);

        return view('dashboard', compact(
            'loans',
            'pendingPayments',
            'activeLoan',
            'riskProfile'
        ));
    }
}
