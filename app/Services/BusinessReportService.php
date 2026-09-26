<?php

namespace App\Services;

use App\Models\ClientVerification;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BusinessReportService
{
    public function generate(?Carbon $preparedAt = null): array
    {
        $preparedAt = ($preparedAt ?? now('Asia/Manila'))->copy()->timezone('Asia/Manila');
        $clients = User::query()
            ->where('role', 'client')
            ->with('clientVerification')
            ->get();
        $loans = Loan::query()
            ->with(['loanType', 'paymentSchedules', 'payments'])
            ->get();
        $financialLoans = $loans->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_PAID]);
        $payments = $loans->flatMap->payments;
        $approvedPayments = $payments->where('status', Payment::STATUS_APPROVED);
        $pendingPayments = $payments->where('status', Payment::STATUS_PENDING);

        $scheduledPayable = $financialLoans->sum(fn (Loan $loan) => (float) $loan->total_payable);
        $principalReleased = $financialLoans->sum(fn (Loan $loan) => (float) $loan->amount);
        $collections = $approvedPayments->sum(fn (Payment $payment) => (float) $payment->amount);
        $penalties = $financialLoans->sum(
            fn (Loan $loan) => $loan->paymentSchedules->sum(
                fn (PaymentSchedule $schedule) => (float) $schedule->penalty_amount
            )
        );
        $outstanding = $financialLoans->sum(
            fn (Loan $loan) => max(0, $this->loanTotalDue($loan) - $this->loanCollections($loan))
        );
        $overdueSchedules = $financialLoans
            ->flatMap->paymentSchedules
            ->filter(fn (PaymentSchedule $schedule) => $this->isOverdue($schedule, $preparedAt));
        $overdueLoanIds = $overdueSchedules->pluck('loan_id')->unique();
        $overdueAmount = $overdueSchedules->sum(fn (PaymentSchedule $schedule) => $this->scheduleOutstanding($schedule));
        $completedLoans = $financialLoans->where('status', Loan::STATUS_PAID)->count();

        $summary = [
            'clients' => $clients->count(),
            'verified_clients' => $clients->filter(
                fn (User $client) => $client->clientVerification?->status === ClientVerification::STATUS_APPROVED
            )->count(),
            'applications' => $loans->count(),
            'originated_loans' => $financialLoans->count(),
            'active_loans' => $financialLoans->where('status', Loan::STATUS_APPROVED)->count(),
            'completed_loans' => $completedLoans,
            'principal_released' => round($principalReleased, 2),
            'contract_interest' => round(max(0, $scheduledPayable - $principalReleased), 2),
            'scheduled_payable' => round($scheduledPayable, 2),
            'collections' => round($collections, 2),
            'outstanding' => round($outstanding, 2),
            'penalties' => round($penalties, 2),
            'overdue_loans' => $overdueLoanIds->count(),
            'overdue_installments' => $overdueSchedules->count(),
            'overdue_amount' => round($overdueAmount, 2),
            'pending_payment_count' => $pendingPayments->count(),
            'pending_payment_amount' => round($pendingPayments->sum(fn (Payment $payment) => (float) $payment->amount), 2),
            'collection_rate' => $scheduledPayable > 0 ? round(($collections / $scheduledPayable) * 100, 2) : 0.0,
            'completion_rate' => $financialLoans->count() > 0 ? round(($completedLoans / $financialLoans->count()) * 100, 2) : 0.0,
            'overdue_share' => $outstanding > 0 ? round(($overdueAmount / $outstanding) * 100, 2) : 0.0,
        ];

        return [
            'preparedAt' => $preparedAt,
            'summary' => $summary,
            'statuses' => $this->statusBreakdown($loans),
            'products' => $this->productBreakdown($loans, $preparedAt),
            'paymentMethods' => $this->paymentMethodBreakdown($payments),
            'monthlyTrend' => $this->monthlyTrend($loans, $approvedPayments, $preparedAt),
            'controls' => $this->dataControls($clients, $financialLoans),
        ];
    }

    private function statusBreakdown(Collection $loans): array
    {
        return collect([
            Loan::STATUS_PENDING => 'Pending review',
            Loan::STATUS_APPROVED => 'Active / approved',
            Loan::STATUS_PAID => 'Completed / paid',
            Loan::STATUS_REJECTED => 'Rejected',
        ])->map(function (string $label, string $status) use ($loans) {
            $matching = $loans->where('status', $status);

            return [
                'status' => $status,
                'label' => $label,
                'count' => $matching->count(),
                'principal' => round($matching->sum(fn (Loan $loan) => (float) $loan->amount), 2),
            ];
        })->values()->all();
    }

    private function productBreakdown(Collection $loans, Carbon $preparedAt): array
    {
        return $loans
            ->groupBy(fn (Loan $loan) => $loan->loanType?->display_name ?? 'Unassigned product')
            ->map(function (Collection $productLoans, string $productName) use ($preparedAt) {
                $financialLoans = $productLoans->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_PAID]);
                $scheduled = $financialLoans->sum(fn (Loan $loan) => (float) $loan->total_payable);
                $collected = $financialLoans->sum(fn (Loan $loan) => $this->loanCollections($loan));
                $outstanding = $financialLoans->sum(
                    fn (Loan $loan) => max(0, $this->loanTotalDue($loan) - $this->loanCollections($loan))
                );
                $overdueLoans = $financialLoans->filter(
                    fn (Loan $loan) => $loan->paymentSchedules->contains(
                        fn (PaymentSchedule $schedule) => $this->isOverdue($schedule, $preparedAt)
                    )
                )->count();

                return [
                    'name' => $productName,
                    'applications' => $productLoans->count(),
                    'originated' => $financialLoans->count(),
                    'active' => $financialLoans->where('status', Loan::STATUS_APPROVED)->count(),
                    'completed' => $financialLoans->where('status', Loan::STATUS_PAID)->count(),
                    'principal' => round($financialLoans->sum(fn (Loan $loan) => (float) $loan->amount), 2),
                    'scheduled' => round($scheduled, 2),
                    'collected' => round($collected, 2),
                    'outstanding' => round($outstanding, 2),
                    'overdue_loans' => $overdueLoans,
                    'collection_rate' => $scheduled > 0 ? round(($collected / $scheduled) * 100, 2) : 0.0,
                ];
            })
            ->sortByDesc('principal')
            ->values()
            ->all();
    }

    private function paymentMethodBreakdown(Collection $payments): array
    {
        return $payments
            ->groupBy(fn (Payment $payment) => $payment->method_group ?: 'not_recorded')
            ->map(function (Collection $methodPayments, string $method) {
                $approved = $methodPayments->where('status', Payment::STATUS_APPROVED);
                $pending = $methodPayments->where('status', Payment::STATUS_PENDING);

                return [
                    'method' => $method,
                    'label' => $methodPayments->first()?->method_label ?? str($method)->replace('_', ' ')->title()->toString(),
                    'approved_count' => $approved->count(),
                    'approved_amount' => round($approved->sum(fn (Payment $payment) => (float) $payment->amount), 2),
                    'pending_count' => $pending->count(),
                    'pending_amount' => round($pending->sum(fn (Payment $payment) => (float) $payment->amount), 2),
                    'rejected_count' => $methodPayments->where('status', Payment::STATUS_REJECTED)->count(),
                ];
            })
            ->sortByDesc('approved_amount')
            ->values()
            ->all();
    }

    private function monthlyTrend(Collection $loans, Collection $approvedPayments, Carbon $preparedAt): array
    {
        return collect(range(5, 0))->map(function (int $monthsAgo) use ($loans, $approvedPayments, $preparedAt) {
            $start = $preparedAt->copy()->startOfMonth()->subMonths($monthsAgo);
            $end = $start->copy()->endOfMonth();
            $applications = $loans->filter(fn (Loan $loan) => $loan->created_at?->between($start, $end))->count();
            $originated = $loans->filter(
                fn (Loan $loan) => $loan->approved_at?->between($start, $end)
                    && in_array($loan->status, [Loan::STATUS_APPROVED, Loan::STATUS_PAID], true)
            );
            $collections = $approvedPayments->filter(function (Payment $payment) use ($start, $end) {
                $receivedAt = $payment->paid_at ?? $payment->created_at;

                return $receivedAt?->between($start, $end);
            });

            return [
                'month' => $start->format('M Y'),
                'applications' => $applications,
                'originations' => $originated->count(),
                'principal_released' => round($originated->sum(fn (Loan $loan) => (float) $loan->amount), 2),
                'collections' => round($collections->sum(fn (Payment $payment) => (float) $payment->amount), 2),
            ];
        })->all();
    }

    private function dataControls(Collection $clients, Collection $financialLoans): array
    {
        return [
            [
                'label' => 'Active loans without a payment schedule',
                'count' => $financialLoans->filter(fn (Loan $loan) => $loan->paymentSchedules->isEmpty())->count(),
            ],
            [
                'label' => 'Active borrowers without approved verification',
                'count' => $financialLoans->pluck('user_id')->unique()->filter(function ($userId) use ($clients) {
                    return $clients->firstWhere('id', $userId)?->clientVerification?->status !== ClientVerification::STATUS_APPROVED;
                })->count(),
            ],
            [
                'label' => 'Loans without an assigned product',
                'count' => $financialLoans->whereNull('loan_type_id')->count(),
            ],
            [
                'label' => 'Loans with payment-ledger variance',
                'count' => $financialLoans->filter(function (Loan $loan) {
                    return abs($this->loanCollections($loan) - (float) $loan->paid_amount) > 0.01;
                })->count(),
            ],
        ];
    }

    private function loanCollections(Loan $loan): float
    {
        return (float) $loan->payments
            ->where('status', Payment::STATUS_APPROVED)
            ->sum(fn (Payment $payment) => (float) $payment->amount);
    }

    private function loanTotalDue(Loan $loan): float
    {
        return (float) $loan->total_payable + $loan->paymentSchedules->sum(
            fn (PaymentSchedule $schedule) => (float) $schedule->penalty_amount
        );
    }

    private function scheduleOutstanding(PaymentSchedule $schedule): float
    {
        return max(
            0,
            (float) $schedule->scheduled_amount
                + (float) $schedule->penalty_amount
                - (float) $schedule->paid_amount
        );
    }

    private function isOverdue(PaymentSchedule $schedule, Carbon $preparedAt): bool
    {
        return $this->scheduleOutstanding($schedule) > 0
            && ($schedule->status === PaymentSchedule::STATUS_OVERDUE
                || $schedule->due_date->isBefore($preparedAt->copy()->startOfDay()));
    }
}
