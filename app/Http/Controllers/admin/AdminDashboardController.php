<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;

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

            'total_released' => Loan::where('status', 'approved')->sum('amount'),

            'total_collected' => Payment::where('status', 'approved')->sum('amount'),
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
                'paymentSchedules'
            ])
            ->latest()
            ->take(20)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | OVERDUE LOANS (FIXED)
        |--------------------------------------------------------------------------
        */

        $overdueLoans = Loan::with([
                'user',
                'loanType',
                'paymentSchedules'
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