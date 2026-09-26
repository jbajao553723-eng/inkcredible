<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BusinessReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientReportController extends Controller
{
    public function index(BusinessReportService $businessReport): View
    {
        $clients = User::query()
            ->where('role', 'client')
            ->with([
                'clientVerification',
                'loans' => fn ($query) => $query
                    ->with(['loanType', 'paymentSchedules', 'payments'])
                    ->latest(),
            ])
            ->latest()
            ->get();

        $stats = [
            'clients' => $clients->count(),
            'verified' => $clients->filter(fn ($client) => $client->clientVerification?->status === 'approved')->count(),
            'applications' => $clients->sum(fn ($client) => $client->loans->count()),
            'active_borrowers' => $clients->filter(
                fn ($client) => $client->loans->contains('status', 'approved')
            )->count(),
        ];
        $business = $businessReport->generate();

        return view('admin.reports.index', compact('clients', 'stats', 'business'));
    }

    public function downloadBusiness(BusinessReportService $businessReport): Response
    {
        $report = $businessReport->generate();
        $preparedAt = $report['preparedAt'];
        $filename = 'business-report-'.$preparedAt->format('Ymd').'.pdf';

        return Pdf::loadView('admin.reports.business', $report)
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function download(User $client): Response
    {
        abort_unless($client->role === 'client', 404);

        $client->load([
            'clientVerification.reviewer',
            'loans' => fn ($query) => $query
                ->with([
                    'loanType',
                    'paymentSchedules',
                    'loanDocuments',
                    'payments' => fn ($payments) => $payments->latest('paid_at'),
                ])
                ->latest(),
        ]);

        $loans = $client->loans;
        $financialLoans = $loans->whereIn('status', ['approved', 'paid']);
        $summary = [
            'applications' => $loans->count(),
            'pending' => $loans->where('status', 'pending')->count(),
            'approved' => $loans->where('status', 'approved')->count(),
            'paid_loans' => $loans->where('status', 'paid')->count(),
            'rejected' => $loans->where('status', 'rejected')->count(),
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
        $summary['payments'] = $loans->sum(fn ($loan) => $loan->payments->count());

        $preparedAt = now('Asia/Manila');
        $safeName = Str::slug($client->name) ?: 'client-'.$client->getKey();
        $filename = 'client-report-'.$safeName.'-'.$preparedAt->format('Ymd').'.pdf';

        return Pdf::loadView('admin.clients.report', compact('client', 'loans', 'summary', 'preparedAt'))
            ->setPaper('a4')
            ->download($filename);
    }
}
