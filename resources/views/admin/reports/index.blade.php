<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.report-button { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; }
.report-button:hover { color:#3730a3; background:#e0e7ff; border-color:#a5b4fc; }
.report-button svg { width:15px; height:15px; }
.report-summary { min-width:180px; }
.business-report { border-color:#c7d2fe; background:linear-gradient(135deg,#ffffff 0%,#f5f3ff 100%); }
.business-report-body { display:grid; grid-template-columns:minmax(260px,.8fr) minmax(0,1.2fr); gap:24px; align-items:center; padding:22px; }
.business-report-copy h3 { margin:0; font-size:18px; }
.business-report-copy p { margin:8px 0 16px; max-width:600px; color:var(--muted); font-size:12px; line-height:1.6; }
.business-metrics { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
.business-metric { padding:13px 14px; background:rgba(255,255,255,.82); border:1px solid #e0e7ff; border-radius:11px; }
.business-metric span { display:block; color:var(--muted); font-size:10px; }
.business-metric strong { display:block; margin-top:5px; color:#312e81; font-size:16px; }
@media(max-width:850px){.business-report-body{grid-template-columns:1fr}}
@media(max-width:480px){.business-metrics{grid-template-columns:1fr}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'reports'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Records and exports</div><h1>Reports</h1><p class="subtitle">Download company-level performance reporting or detailed client account records.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></div>
    </header>

    <section class="stats-grid" aria-label="Report summary">
        <article class="stat-card"><div class="stat-label">Available reports</div><div class="stat-value">{{ $stats['clients'] }}</div><div class="stat-note">One report per client account</div></article>
        <article class="stat-card"><div class="stat-label">Verified clients</div><div class="stat-value">{{ $stats['verified'] }}</div><div class="stat-note">Approved identity submissions</div></article>
        <article class="stat-card"><div class="stat-label">Loan applications</div><div class="stat-value">{{ $stats['applications'] }}</div><div class="stat-note">Included across all reports</div></article>
        <article class="stat-card"><div class="stat-label">Active borrowers</div><div class="stat-value">{{ $stats['active_borrowers'] }}</div><div class="stat-note">Clients with approved balances</div></article>
    </section>

    <section class="panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Client reports directory</h2><p class="panel-description">Each PDF includes the client profile, verification, loans, schedules, and payments.</p></div><span class="badge badge-neutral">{{ $clients->count() }} reports</span></div>
        <div class="toolbar"><input class="search-input" id="report-search" type="search" placeholder="Search client or email..." aria-label="Search client reports"></div>
        @if($clients->isEmpty())
            <div class="empty-state"><strong>No client reports available</strong>Reports will appear after a client account is registered.</div>
        @else
            <div class="table-wrap"><table>
                <thead><tr><th>Client</th><th>Email</th><th>Verification</th><th>Loan summary</th><th>Current balance</th><th>Latest activity</th><th>PDF report</th></tr></thead>
                <tbody id="report-table">
                @foreach($clients as $client)
                    @php
                        $verificationStatus = $client->clientVerification?->status ?? 'not submitted';
                        $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                        $financialLoans = $client->loans->whereIn('status', ['approved', 'paid']);
                        $outstanding = $financialLoans->sum(function ($loan) {
                            $penalties = $loan->paymentSchedules->sum(fn ($schedule) => (float) $schedule->penalty_amount);
                            return max(0, (float) $loan->total_payable + $penalties - (float) $loan->paid_amount);
                        });
                        $latestLoanAt = $client->loans->max('updated_at');
                        $latestPaymentAt = $client->loans->flatMap->payments->max(fn ($payment) => $payment->paid_at ?? $payment->created_at);
                        $latestActivity = collect([$client->updated_at, $latestLoanAt, $latestPaymentAt])->filter()->max();
                    @endphp
                    <tr data-search="{{ strtolower($client->name.' '.$client->email.' '.$client->contact_number) }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span><span class="cell-title">{{ $client->name }}</span></div></td>
                        <td class="email-cell"><span class="email-value">{{ $client->email }}</span></td>
                        <td><span class="badge {{ $verificationClass }}">{{ ucfirst($verificationStatus) }}</span></td>
                        <td class="report-summary"><div class="cell-title">{{ $client->loans->count() }} {{ Str::plural('application', $client->loans->count()) }}</div><div class="cell-secondary">{{ $client->loans->where('status', 'approved')->count() }} active · {{ $client->loans->where('status', 'paid')->count() }} paid</div></td>
                        <td class="amount">PHP {{ number_format($outstanding, 2) }}</td>
                        <td><div>{{ $latestActivity?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'No activity' }}</div><div class="cell-secondary">{{ $latestActivity?->copy()->timezone('Asia/Manila')->format('h:i A') }} PHT</div></td>
                        <td><a class="button button-small report-button" href="{{ route('admin.reports.download', $client) }}" aria-label="Download PDF report for {{ $client->name }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>Download PDF</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="report-empty" hidden><strong>No matching reports</strong>Try a different client name or email.</div>
        @endif
    </section>

    <section class="panel business-report">
        <div class="business-report-body">
            <div class="business-report-copy">
                <div class="eyebrow">Management reporting</div>
                <h3>Business performance report</h3>
                <p>A structured company-wide PDF covering portfolio size, releases, collections, outstanding and overdue balances, product performance, payment channels, six-month trends, and data-quality controls.</p>
                <a class="button button-primary" href="{{ route('admin.reports.business.download') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>Download business PDF</a>
            </div>
            <div class="business-metrics" aria-label="Current business report snapshot">
                <div class="business-metric"><span>Principal released</span><strong>PHP {{ number_format($business['summary']['principal_released'], 2) }}</strong></div>
                <div class="business-metric"><span>Approved collections</span><strong>PHP {{ number_format($business['summary']['collections'], 2) }}</strong></div>
                <div class="business-metric"><span>Outstanding portfolio</span><strong>PHP {{ number_format($business['summary']['outstanding'], 2) }}</strong></div>
                <div class="business-metric"><span>Portfolio at risk</span><strong>{{ $business['summary']['overdue_loans'] }} overdue {{ Str::plural('loan', $business['summary']['overdue_loans']) }}</strong></div>
            </div>
        </div>
    </section>
</div></main>
</body></html>
