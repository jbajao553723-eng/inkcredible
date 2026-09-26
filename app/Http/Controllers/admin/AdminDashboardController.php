<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | STATS (REAL TIME - NO SERVICE DEPENDENCY)
        |--------------------------------------------------------------------------
        */

        $stats = [

            'total_loans' => Loan::count(),

            'pending_loans' => Loan::where('status', 'pending')->count(),

            'approved_loans' => Loan::where('status', 'approved')->count(),

            'rejected_loans' => Loan::where('status', 'rejected')->count(),

            'paid_loans' => Loan::where('status', 'paid')->count(),

            'total_released' => Loan::whereIn('status', ['approved', 'paid'])->sum('amount'),

            'total_collected' => Payment::where('status', Payment::STATUS_APPROVED)->sum('amount'),

            'pending_cash_payments' => Payment::where('status', Payment::STATUS_PENDING)->where('method', 'cash')->count(),

            'total_clients' => User::where('role', 'client')->count(),

            'pending_verifications' => ClientVerification::where('status', ClientVerification::STATUS_PENDING)->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | RECENT LOANS (FIXED - NO FILTER LIMITING)
        |--------------------------------------------------------------------------
        */

        $recentLoans = Loan::with([
            'user',
            'loanType',
            'payments',
            'paymentSchedules',
        ])
            ->latest()
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | OVERDUE LOANS (FIXED)
        |--------------------------------------------------------------------------
        */

        $overdueLoans = Loan::with([
            'user',
            'loanType',
            'paymentSchedules',
        ])
            ->whereHas('paymentSchedules', function ($q) {
                $q->where('status', 'overdue');
            })
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CLIENTS
        |--------------------------------------------------------------------------
        */

        $clients = User::where('role', 'client')->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentLoans',
            'overdueLoans',
            'clients'
        ));
    }
}
