<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LoanReportService
{
    /**
     * Get loan summary using the database view
     */
    public function getLoanSummary($loanId = null)
    {
        $query = DB::table('loan_summary_view');

        if ($loanId) {
            $query->where('id', $loanId);
        }

        return $query->get();
    }

    /**
     * Get payment summary using the database view
     */
    public function getPaymentSummary($loanId = null, $userId = null)
    {
        $query = DB::table('payment_summary_view');

        if ($loanId) {
            $query->where('loan_id', $loanId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('due_date')->get();
    }

    /**
     * Get overdue loans using the database view
     */
    public function getOverdueLoans()
    {
        return DB::table('overdue_loans_view')
            ->orderBy('days_overdue', 'desc')
            ->get();
    }

    /**
     * Get loan performance statistics using the database view
     */
    public function getLoanPerformance()
    {
        return DB::table('loan_performance_view')
            ->orderBy('total_loans', 'desc')
            ->get();
    }

    /**
     * Get user loan summary using the database view
     */
    public function getUserLoanSummary($userId = null)
    {
        $query = DB::table('user_loan_summary_view');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    /**
     * Get dashboard statistics using views
     */
    public function getDashboardStats()
    {
        $loanSummary = DB::table('loan_summary_view')->selectRaw('
            COUNT(*) as total_loans,
            SUM(loan_amount) as total_amount_disbursed,
            SUM(remaining_balance) as total_outstanding,
            SUM(CASE WHEN loan_status = "approved" THEN 1 ELSE 0 END) as active_loans,
            SUM(CASE WHEN loan_status = "paid" THEN 1 ELSE 0 END) as paid_loans,
            SUM(CASE WHEN is_overdue = 1 THEN 1 ELSE 0 END) as overdue_loans,
            SUM(total_penalty) as total_penalties
        ')->first();

        $performance = DB::table('loan_performance_view')->selectRaw('
            AVG(avg_loan_amount) as avg_loan_amount,
            SUM(total_penalties_collected) as total_penalties_collected
        ')->first();

        return [
            'total_loans' => $loanSummary->total_loans ?? 0,
            'total_amount_disbursed' => $loanSummary->total_amount_disbursed ?? 0,
            'total_outstanding' => $loanSummary->total_outstanding ?? 0,
            'active_loans' => $loanSummary->active_loans ?? 0,
            'paid_loans' => $loanSummary->paid_loans ?? 0,
            'overdue_loans' => $loanSummary->overdue_loans ?? 0,
            'total_penalties' => $loanSummary->total_penalties ?? 0,
            'avg_loan_amount' => $performance->avg_loan_amount ?? 0,
        ];
    }

    /**
     * Get loans by status using view
     */
    public function getLoansByStatus($status)
    {
        return DB::table('loan_summary_view')
            ->where('loan_status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get loans with high penalties using view
     */
    public function getHighPenaltyLoans($minPenalty = 1000)
    {
        return DB::table('loan_summary_view')
            ->where('total_penalty', '>=', $minPenalty)
            ->orderBy('total_penalty', 'desc')
            ->get();
    }

    /**
     * Get user performance summary
     */
    public function getUserPerformance($userId)
    {
        return DB::table('user_loan_summary_view')
            ->where('user_id', $userId)
            ->first();
    }
}