<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ClientReportController extends Controller
{
    public function download(User $client): Response
    {
        abort_unless($client->role === 'client', 404);

        $client->load([
            'clientVerification',
            'loans' => fn ($query) => $query
                ->with([
                    'loanType',
                    'paymentSchedules',
                    'payments' => fn ($payments) => $payments->latest('paid_at'),
                ])
                ->latest(),
        ]);

        $loans = $client->loans;
        $financialLoans = $loans->whereIn('status', ['approved', 'paid']);
        $summary = [
            'applications' => $loans->count(),
            'approved_principal' => $financialLoans->sum(fn ($loan) => (float) $loan->amount),
            'scheduled_payable' => $financialLoans->sum(fn ($loan) => (float) $loan->total_payable),
            'paid' => $financialLoans->sum(fn ($loan) => (float) $loan->paid_amount),
            'penalties' => $financialLoans->sum(
                fn ($loan) => $loan->paymentSchedules->sum(fn ($schedule) => (float) $schedule->penalty_amount)
            ),
        ];
        $summary['outstanding'] = max(
            0,
            $summary['scheduled_payable'] + $summary['penalties'] - $summary['paid']
        );

        $preparedAt = now('Asia/Manila');
        $safeName = Str::slug($client->name) ?: 'client-'.$client->getKey();
        $filename = 'client-report-'.$safeName.'-'.$preparedAt->format('Ymd').'.pdf';

        return Pdf::loadView('admin.clients.report', compact('client', 'loans', 'summary', 'preparedAt'))
            ->setPaper('a4')
            ->download($filename);
    }
}
