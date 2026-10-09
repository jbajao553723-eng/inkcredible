<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.dashboard-nav { display:flex; gap:7px; flex-wrap:wrap; padding:5px; width:fit-content; margin-bottom:24px; background:#eef0f5; border:1px solid #e4e7ec; border-radius:12px; }
.dashboard-nav a { padding:9px 14px; color:#475467; border-radius:8px; text-decoration:none; font-size:0.75rem; font-weight:600; }.dashboard-nav a:hover,.dashboard-nav a:first-child { background:#fff; color:#4338ca; box-shadow:0 1px 3px #1018280d; }
.dashboard-section-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin:26px 0 12px; }.dashboard-section-head h2 { margin:0; font-size:1rem; letter-spacing:-.02em; }.dashboard-section-head p { margin:5px 0 0; color:#667085; font-size:0.75rem; }.dashboard-date { color:#667085; font-size:0.75rem; }
.overview-stats { gap:14px; }.overview-stats .stat-card { padding:20px; box-shadow:0 2px 5px #10182805; }.overview-stats .stat-label { font-size:0.75rem; }.overview-stats .stat-value { font-size:clamp(1.25rem,2.2vw,1.8125rem); margin:12px 0 7px; overflow-wrap:anywhere; }.overview-stats .stat-note { line-height:1.5; }
.stat-kicker { display:flex; align-items:center; justify-content:space-between; gap:10px; }.stat-symbol { display:grid; place-items:center; width:32px; height:32px; color:#4f46e5; background:#eef2ff; border-radius:9px; font-size:1rem; }.stat-symbol.green { color:#067647; background:#ecfdf3; }
.operations-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.05fr); gap:20px; align-items:stretch; }.operations-grid .panel { margin:0; }
.income-card { display:flex; flex-direction:column; min-height:100%; padding:24px; background:#fff; border:1px solid #e4e7ec; border-radius:16px; box-shadow:0 2px 5px #10182805; }
.income-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; }.income-card-head h2 { margin:0; color:#101828; font-size:0.9375rem; }.income-card-head p { margin:5px 0 0; color:#667085; font-size:0.6875rem; line-height:1.45; }.income-badge { flex:0 0 auto; padding:5px 9px; color:#067647; background:#ecfdf3; border-radius:999px; font-size:0.625rem; font-weight:700; }
.income-total { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-top:20px; padding-bottom:18px; border-bottom:1px solid #f2f4f7; }.income-total span { display:block; color:#667085; font-size:0.6875rem; }.income-total strong { display:block; margin-top:5px; color:#101828; font-size:clamp(1.5rem,2.7vw,2.125rem); letter-spacing:-.04em; overflow-wrap:anywhere; }.income-rate { color:#4338ca; font-size:0.6875rem; font-weight:700; text-align:right; }
.income-chart { display:grid; gap:14px; margin-top:18px; }.income-row { display:grid; grid-template-columns:112px minmax(100px,1fr) auto; gap:11px; align-items:center; }.income-label { color:#475467; font-size:0.6875rem; }.income-track { height:10px; overflow:hidden; background:#f2f4f7; border-radius:999px; }.income-bar { display:block; min-width:0; height:100%; border-radius:inherit; background:linear-gradient(90deg,#4f46e5,#7c3aed); }.income-bar.previous { background:#a5b4fc; }.income-bar.projected { background:#12b76a; }.income-amount { min-width:84px; color:#344054; font-size:0.6875rem; font-weight:700; text-align:right; white-space:nowrap; }
.income-legend { display:flex; align-items:center; gap:6px; margin-top:14px; color:#667085; font-size:0.625rem; }.income-legend::before { content:''; width:7px; height:7px; background:#12b76a; border-radius:50%; }.income-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:auto; padding-top:20px; }.income-actions .button { flex:1 1 130px; }
.focus-list { padding:4px 20px 12px; }.focus-item { display:grid; grid-template-columns:40px minmax(0,1fr) auto 16px; gap:12px; align-items:center; min-height:82px; padding:13px 0; border-bottom:1px solid #f2f4f7; color:#344054; text-decoration:none; }.focus-item:last-child { border:0; }.focus-item:hover .focus-title { color:#4338ca; }.focus-icon { display:grid; place-items:center; width:40px; height:40px; border-radius:11px; color:#4f46e5; background:#eef2ff; }.focus-icon svg { width:20px; height:20px; }.focus-icon.amber { color:#b54708; background:#fffaeb; }.focus-icon.green { color:#067647; background:#ecfdf3; }.focus-title { display:block; font-size:0.8125rem; font-weight:600; }.focus-note { display:block; margin-top:5px; font-size:0.6875rem; color:#667085; line-height:1.4; }.focus-value { display:grid; place-items:center; min-width:33px; height:30px; padding:0 8px; color:#4338ca; background:#eef2ff; border-radius:8px; font-size:0.8125rem; font-weight:700; }.focus-arrow { width:16px; height:16px; color:#98a2b3; }.queue-count { display:inline-flex; padding:5px 9px; color:#b54708; background:#fffaeb; border-radius:999px; font-size:0.6875rem; font-weight:600; }.queue-count.clear { color:#067647; background:#ecfdf3; }
.earnings-detail { margin-top:20px; background:#fff; border:1px solid #e4e7ec; border-radius:14px; overflow:hidden; }.earnings-detail summary { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:19px 22px; cursor:pointer; list-style:none; }.earnings-detail summary::-webkit-details-marker { display:none; }.earnings-detail summary strong { display:block; font-size:0.8125rem; }.earnings-detail summary small { display:block; margin-top:4px; color:#667085; font-size:0.6875rem; }.earnings-detail summary > span:last-child { display:flex; align-items:center; gap:14px; color:#4338ca; font-size:1rem; font-weight:700; }.earnings-detail summary svg { width:16px; height:16px; transition:transform .2s; }.earnings-detail[open] summary svg { transform:rotate(180deg); }
.earnings-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin:0 22px 16px; padding-top:18px; border-top:1px solid #eaecf0; }.earnings-grid span { display:block; color:#667085; font-size:0.6875rem; }.earnings-grid strong { display:block; margin-top:7px; color:#344054; font-size:0.9375rem; overflow-wrap:anywhere; }.earnings-note { margin:0 22px 20px; color:#667085; font-size:0.6875rem; line-height:1.5; }
.dashboard-table { min-width:760px; }.dashboard-table td { padding-top:17px; padding-bottom:17px; }.recent-client .identity { align-items:flex-start; }.recent-client .cell-secondary { display:block; font-size:0.6875rem; overflow-wrap:anywhere; }.recent-action { color:#4338ca; background:#eef2ff; border-color:#d9d6fe; }.recent-action:hover { background:#e0e7ff; }.recent-code { font-family:ui-monospace,monospace; font-size:0.625rem; }.dashboard-table .amount { white-space:nowrap; }.empty-dashboard { padding:35px 20px; text-align:center; color:#667085; font-size:0.75rem; }.empty-dashboard strong { display:block; margin-bottom:7px; color:#344054; font-size:0.875rem; }
@media(max-width:1100px){.operations-grid{grid-template-columns:1fr}.overview-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.dashboard-table,.dashboard-table tbody,.dashboard-table tr,.dashboard-table td{display:block;width:100%;min-width:0}.dashboard-table thead{display:none}.dashboard-table tr{padding:8px 18px;border-bottom:1px solid #e4e7ec}.dashboard-table td{padding:10px 0;border:0}.dashboard-table td::before{content:attr(data-label);display:block;margin-bottom:6px;color:#667085;font-size:0.625rem;font-weight:600}.dashboard-table td:last-child .button{width:100%}.earnings-grid{grid-template-columns:1fr;gap:16px}.dashboard-section-head{align-items:flex-start}.dashboard-date{display:none}}
@media(max-width:480px){.overview-stats .stat-card{padding:15px}.stat-symbol{display:none}.income-card{padding:20px}.income-row{grid-template-columns:1fr auto}.income-track{grid-column:1/-1;grid-row:2}.income-actions .button{width:100%}.earnings-detail summary{align-items:flex-start;flex-direction:column}.focus-list{padding:4px 15px 12px}.focus-item{gap:9px}.dashboard-nav a{padding:8px 10px}.top-actions .button{width:100%}}
@media(prefers-reduced-motion:reduce){.earnings-detail summary svg{transition:none}}
</style>
</head>
<body>
@php
    $queueTotal = $stats['pending_loans'] + $stats['pending_cash_payments'] + $stats['pending_verifications'];
    $receivableTotal = $stats['scheduled_receivables'] + $stats['penalty_charges'];
    $collectionProgress = $receivableTotal > 0 ? min(100, ($stats['total_collected'] / $receivableTotal) * 100) : 0;
    $incomeMaximum = max(1, $stats['collected_this_month'], $stats['collected_last_month'], $stats['projected_profit']);
    $incomeSeries = [
        ['label' => 'This month', 'amount' => $stats['collected_this_month'], 'class' => ''],
        ['label' => 'Last month', 'amount' => $stats['collected_last_month'], 'class' => 'previous'],
        ['label' => 'Projected earnings', 'amount' => $stats['projected_profit'], 'class' => 'projected'],
    ];
@endphp
@include('partials.admin-sidebar', ['active' => 'dashboard'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Administrator workspace</div><h1>Dashboard</h1><p class="subtitle">Welcome back, {{ auth()->user()->first_name ?: auth()->user()->name }}. Here is your lending overview.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.reports.index') }}">Business reports</a><a class="button button-primary" href="{{ route('admin.loans') }}">Manage loans</a></div>
    </header>
    <x-flash-messages />
    <nav class="dashboard-nav" aria-label="Dashboard sections"><a href="#overview">Overview</a><a href="#work">Pending work</a><a href="#recent">Recent requests</a></nav>
    <div class="dashboard-section-head" id="overview"><div><h2>At a glance</h2><p>Your portfolio and activity this month.</p></div><span class="dashboard-date">{{ now('Asia/Manila')->format('l, F j, Y') }}</span></div>
    <section class="stats-grid overview-stats" aria-label="Administrative summary">
        <article class="stat-card"><div class="stat-kicker"><span class="stat-label">Loan requests</span><span class="stat-symbol" aria-hidden="true">↗</span></div><div class="stat-value">{{ number_format($stats['total_loans']) }}</div><div class="stat-note">{{ $stats['pending_loans'] }} awaiting review</div></article>
        <article class="stat-card"><div class="stat-kicker"><span class="stat-label">Released this month</span><span class="stat-symbol" aria-hidden="true">₱</span></div><div class="stat-value">₱{{ number_format($stats['released_this_month'], 2) }}</div><div class="stat-note">Last month · ₱{{ number_format($stats['released_last_month'], 2) }}</div></article>
        <article class="stat-card"><div class="stat-kicker"><span class="stat-label">Collected this month</span><span class="stat-symbol green" aria-hidden="true">✓</span></div><div class="stat-value">₱{{ number_format($stats['collected_this_month'], 2) }}</div><div class="stat-note">Last month · ₱{{ number_format($stats['collected_last_month'], 2) }}</div></article>
        <article class="stat-card"><div class="stat-kicker"><span class="stat-label">Registered clients</span><span class="stat-symbol" aria-hidden="true">◎</span></div><div class="stat-value">{{ number_format($stats['total_clients']) }}</div><div class="stat-note">{{ $stats['approved_loans'] }} active · {{ $stats['paid_loans'] }} paid loans</div></article>
    </section>
    <div class="dashboard-section-head" id="work"><div><h2>Keep things moving</h2><p>Review submissions and monitor your portfolio.</p></div><span class="queue-count {{ $queueTotal ? '' : 'clear' }}">{{ $queueTotal ? $queueTotal.' awaiting review' : 'All caught up' }}</span></div>
    <section class="operations-grid">
        <article class="panel"><div class="panel-header"><div><h2 class="panel-title">Pending work</h2><p class="panel-description">Choose a queue to review its submissions.</p></div></div>
            <div class="focus-list">
                <a class="focus-item" href="{{ route('admin.loans', ['status' => 'pending']) }}"><span class="focus-icon amber"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg></span><span><span class="focus-title">Loan applications</span><span class="focus-note">Review requests and record a decision.</span></span><span class="focus-value">{{ $stats['pending_loans'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
                <a class="focus-item" href="{{ route('admin.verifications.index') }}"><span class="focus-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" stroke-linejoin="round" d="m12 3 7 3v5c0 4-3 8-7 10-4-2-7-6-7-10V6zM9 12l2 2 4-5"/></svg></span><span><span class="focus-title">Client verifications</span><span class="focus-note">Check identity and employment records.</span></span><span class="focus-value">{{ $stats['pending_verifications'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
                <a class="focus-item" href="{{ route('admin.payments.index', ['status' => 'pending', 'method' => 'cash']) }}"><span class="focus-icon green"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" stroke-linecap="round" d="M4 7h16v11H4zM4 11h16M7 15h3"/></svg></span><span><span class="focus-title">Cash payment proofs</span><span class="focus-note">Confirm submitted payments.</span></span><span class="focus-value">{{ $stats['pending_cash_payments'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
            </div>
        </article>
        <article class="income-card" aria-labelledby="income-chart-title">
            <div class="income-card-head"><div><h2 id="income-chart-title">Collections &amp; income</h2><p>Approved cash received compared with projected gross earnings.</p></div><span class="income-badge">Income summary</span></div>
            <div class="income-total"><div><span>Total approved collections</span><strong>₱{{ number_format($stats['total_collected'], 2) }}</strong></div><div class="income-rate">{{ number_format($collectionProgress, 1) }}%<br>of receivables</div></div>
            <div class="income-chart" role="img" aria-label="Bar chart comparing collections this month, collections last month, and projected earnings">
                @foreach($incomeSeries as $income)
                    @php
                        $incomeWidth = $income['amount'] > 0 ? max(3, ($income['amount'] / $incomeMaximum) * 100) : 0;
                    @endphp
                    <div class="income-row"><span class="income-label">{{ $income['label'] }}</span><span class="income-track"><span class="income-bar {{ $income['class'] }}" style="width:{{ number_format($incomeWidth, 2, '.', '') }}%"></span></span><strong class="income-amount">₱{{ number_format($income['amount'], 2) }}</strong></div>
                @endforeach
            </div>
            <div class="income-legend">Projected earnings include contract interest and recorded penalties.</div>
            <div class="income-actions"><a class="button button-secondary" href="{{ route('admin.payments.index') }}">View payments</a><a class="button button-primary" href="{{ route('admin.reports.index') }}">Open reports</a></div>
        </article>
    </section>
    <details class="earnings-detail"><summary><span><strong>Projected earnings</strong><small>Expand to see interest, penalties, and the projected margin.</small></span><span>₱{{ number_format($stats['projected_profit'], 2) }}<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="1.8" d="m6 9 6 6 6-6"/></svg></span></summary>
        <div class="earnings-grid"><div><span>Contract interest</span><strong>₱{{ number_format($stats['contract_interest'], 2) }}</strong></div><div><span>Penalty charges</span><strong>₱{{ number_format($stats['penalty_charges'], 2) }}</strong></div><div><span>Projected margin</span><strong>{{ number_format($stats['profit_margin'], 1) }}%</strong></div></div><p class="earnings-note">Projected gross earnings before defaults, operating costs, and expenses. Scheduled receivables: ₱{{ number_format($stats['scheduled_receivables'], 2) }}.</p>
    </details>
    <div class="dashboard-section-head" id="recent"><div><h2>Recent loan requests</h2><p>The six newest applications, in Philippine Time.</p></div><a class="button button-secondary button-small" href="{{ route('admin.loans') }}">View all requests</a></div>
    <section class="panel" aria-label="Recent loan requests">
    @if($recentLoans->isEmpty())
        <div class="empty-dashboard"><strong>No loan requests yet</strong>New client applications will appear here.</div>
    @else
        <div class="table-wrap"><table class="dashboard-table"><thead><tr><th>Client</th><th>Loan</th><th>Amount</th><th>Submitted</th><th>Status</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
        @foreach($recentLoans as $loan)
            @php $submitted = $loan->created_at?->copy()->timezone('Asia/Manila'); @endphp
            <tr><td class="recent-client" data-label="Client"><div class="identity"><span class="avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><span><span class="cell-title">{{ $loan->user?->name ?? 'Unknown client' }}</span><span class="cell-secondary">{{ $loan->user?->email ?? 'No email address' }}</span></span></div></td>
                <td data-label="Loan"><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary recent-code">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></td><td class="amount" data-label="Amount">₱{{ number_format($loan->amount, 2) }}</td>
                <td data-label="Submitted">{{ $submitted?->format('M d, Y') }}<div class="cell-secondary">{{ $submitted?->format('h:i A') }} PHT</div></td><td data-label="Status"><x-status-badge :status="$loan->status" /></td><td data-label="Action"><a class="button button-small recent-action" href="{{ route('admin.loan.show', $loan->id) }}">View details</a></td></tr>
        @endforeach
        </tbody></table></div>
    @endif
    </section>
</div></main>
</body></html>
