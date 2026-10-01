<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (request()->user()?->isSuperAdmin()) {
            return redirect()->route('admin.security.dashboard');
        }

        $loanStats = Loan::query()
            ->selectRaw('COUNT(*) as total_loans')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_loans', [Loan::STATUS_PENDING])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved_loans', [Loan::STATUS_APPROVED])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected_loans', [Loan::STATUS_REJECTED])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as paid_loans', [Loan::STATUS_PAID])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN amount ELSE 0 END) as total_released', [Loan::STATUS_APPROVED, Loan::STATUS_PAID])
            ->selectRaw('SUM(CASE WHEN status IN (?, ?) THEN total_payable ELSE 0 END) as scheduled_receivables', [Loan::STATUS_APPROVED, Loan::STATUS_PAID])
            ->first();
        $paymentStats = Payment::query()
            ->selectRaw('SUM(CASE WHEN status = ? THEN amount ELSE 0 END) as total_collected', [Payment::STATUS_APPROVED])
            ->selectRaw("SUM(CASE WHEN status = ? AND method = 'cash' THEN 1 ELSE 0 END) as pending_cash_payments", [Payment::STATUS_PENDING])
            ->first();
        $penaltyCharges = (float) PaymentSchedule::query()
            ->whereHas('loan', fn ($query) => $query->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_PAID]))
            ->sum('penalty_amount');
        $contractInterest = max(0, (float) $loanStats->scheduled_receivables - (float) $loanStats->total_released);
        $projectedProfit = $contractInterest + $penaltyCharges;
        $portfolioReceivable = (float) $loanStats->scheduled_receivables + $penaltyCharges;

        $stats = [
            'total_loans' => (int) $loanStats->total_loans,
            'pending_loans' => (int) $loanStats->pending_loans,
            'approved_loans' => (int) $loanStats->approved_loans,
            'rejected_loans' => (int) $loanStats->rejected_loans,
            'paid_loans' => (int) $loanStats->paid_loans,
            'total_released' => (float) $loanStats->total_released,
            'scheduled_receivables' => (float) $loanStats->scheduled_receivables,
            'total_collected' => (float) $paymentStats->total_collected,
            'contract_interest' => $contractInterest,
            'penalty_charges' => $penaltyCharges,
            'projected_profit' => $projectedProfit,
            'profit_margin' => $portfolioReceivable > 0 ? round(($projectedProfit / $portfolioReceivable) * 100, 1) : 0,
            'pending_cash_payments' => (int) $paymentStats->pending_cash_payments,
            'total_clients' => User::where('role', 'client')->count(),
            'pending_verifications' => ClientVerification::where('status', ClientVerification::STATUS_PENDING)->count(),
        ];

        $recentLoans = Loan::with([
            'user',
            'loanType',
        ])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentLoans',
        ));
    }
}
