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
.dashboard-nav a { padding:9px 14px; color:#475467; border-radius:8px; text-decoration:none; font-size:12px; font-weight:600; }.dashboard-nav a:hover,.dashboard-nav a:first-child { background:#fff; color:#4338ca; box-shadow:0 1px 3px #1018280d; }
.dashboard-section-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin:26px 0 12px; }.dashboard-section-head h2 { margin:0; font-size:16px; letter-spacing:-.02em; }.dashboard-section-head p { margin:5px 0 0; color:#667085; font-size:12px; }.dashboard-date { color:#667085; font-size:12px; }
.overview-stats { gap:14px; }.overview-stats .stat-card { padding:20px; box-shadow:0 2px 5px #10182805; }.overview-stats .stat-label { font-size:12px; }.overview-stats .stat-value { font-size:clamp(20px,2.2vw,29px); margin:12px 0 7px; overflow-wrap:anywhere; }.overview-stats .stat-note { line-height:1.5; }
.stat-kicker { display:flex; align-items:center; justify-content:space-between; gap:10px; }.stat-symbol { display:grid; place-items:center; width:32px; height:32px; color:#4f46e5; background:#eef2ff; border-radius:9px; font-size:16px; }.stat-symbol.green { color:#067647; background:#ecfdf3; }
.operations-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1.05fr); gap:20px; align-items:stretch; }.operations-grid .panel { margin:0; }
.portfolio-card { position:relative; overflow:hidden; padding:26px; background:linear-gradient(135deg,#27256f,#4f46e5 65%,#7c3aed); color:#fff; border-radius:18px; box-shadow:0 12px 30px #4f46e52b; }.portfolio-card::after { content:''; position:absolute; right:-65px; bottom:-100px; width:250px; height:250px; border:44px solid #ffffff0a; border-radius:50%; pointer-events:none; }.portfolio-card > * { position:relative; z-index:1; }
.portfolio-label { display:flex; align-items:center; justify-content:space-between; gap:12px; color:#e0e7ff; font-size:13px; }.portfolio-chip { color:#fff; padding:5px 9px; background:#ffffff1a; border:1px solid #ffffff26; border-radius:999px; font-size:10px; }.portfolio-value { margin:13px 0 7px; font-size:clamp(28px,3vw,38px); font-weight:700; letter-spacing:-.04em; overflow-wrap:anywhere; }.portfolio-note { margin:0; color:#c7d2fe; font-size:12px; line-height:1.5; }
.collection-copy { display:flex; justify-content:space-between; margin:24px 0 8px; color:#e0e7ff; font-size:11px; }.collection-track { height:7px; background:#ffffff26; border-radius:999px; overflow:hidden; }.collection-fill { height:100%; background:#a5f3fc; border-radius:inherit; }
.portfolio-meta { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:22px; }.portfolio-meta span { display:block; color:#c7d2fe; font-size:11px; }.portfolio-meta strong { display:block; margin-top:6px; font-size:14px; font-weight:600; overflow-wrap:anywhere; }.portfolio-actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:23px; }.portfolio-actions .button { background:#fff; color:#3730a3; border-color:#fff; }.portfolio-actions .button-glass { color:#fff; background:#ffffff12; border-color:#ffffff33; }
.focus-list { padding:4px 20px 12px; }.focus-item { display:grid; grid-template-columns:40px minmax(0,1fr) auto 16px; gap:12px; align-items:center; min-height:82px; padding:13px 0; border-bottom:1px solid #f2f4f7; color:#344054; text-decoration:none; }.focus-item:last-child { border:0; }.focus-item:hover .focus-title { color:#4338ca; }.focus-icon { display:grid; place-items:center; width:40px; height:40px; border-radius:11px; color:#4f46e5; background:#eef2ff; }.focus-icon svg { width:20px; height:20px; }.focus-icon.amber { color:#b54708; background:#fffaeb; }.focus-icon.green { color:#067647; background:#ecfdf3; }.focus-title { display:block; font-size:13px; font-weight:600; }.focus-note { display:block; margin-top:5px; font-size:11px; color:#667085; line-height:1.4; }.focus-value { display:grid; place-items:center; min-width:33px; height:30px; padding:0 8px; color:#4338ca; background:#eef2ff; border-radius:8px; font-size:13px; font-weight:700; }.focus-arrow { width:16px; height:16px; color:#98a2b3; }.queue-count { display:inline-flex; padding:5px 9px; color:#b54708; background:#fffaeb; border-radius:999px; font-size:11px; font-weight:600; }.queue-count.clear { color:#067647; background:#ecfdf3; }
.earnings-detail { margin-top:20px; background:#fff; border:1px solid #e4e7ec; border-radius:14px; overflow:hidden; }.earnings-detail summary { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:19px 22px; cursor:pointer; list-style:none; }.earnings-detail summary::-webkit-details-marker { display:none; }.earnings-detail summary strong { display:block; font-size:13px; }.earnings-detail summary small { display:block; margin-top:4px; color:#667085; font-size:11px; }.earnings-detail summary > span:last-child { display:flex; align-items:center; gap:14px; color:#4338ca; font-size:16px; font-weight:700; }.earnings-detail summary svg { width:16px; height:16px; transition:transform .2s; }.earnings-detail[open] summary svg { transform:rotate(180deg); }
.earnings-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin:0 22px 16px; padding-top:18px; border-top:1px solid #eaecf0; }.earnings-grid span { display:block; color:#667085; font-size:11px; }.earnings-grid strong { display:block; margin-top:7px; color:#344054; font-size:15px; overflow-wrap:anywhere; }.earnings-note { margin:0 22px 20px; color:#667085; font-size:11px; line-height:1.5; }
.dashboard-table { min-width:760px; }.dashboard-table td { padding-top:17px; padding-bottom:17px; }.recent-client .identity { align-items:flex-start; }.recent-client .cell-secondary { display:block; font-size:11px; overflow-wrap:anywhere; }.recent-action { color:#4338ca; background:#eef2ff; border-color:#d9d6fe; }.recent-action:hover { background:#e0e7ff; }.recent-code { font-family:ui-monospace,monospace; font-size:10px; }.dashboard-table .amount { white-space:nowrap; }.empty-dashboard { padding:35px 20px; text-align:center; color:#667085; font-size:12px; }.empty-dashboard strong { display:block; margin-bottom:7px; color:#344054; font-size:14px; }
@media(max-width:1100px){.operations-grid{grid-template-columns:1fr}.overview-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.dashboard-table,.dashboard-table tbody,.dashboard-table tr,.dashboard-table td{display:block;width:100%;min-width:0}.dashboard-table thead{display:none}.dashboard-table tr{padding:8px 18px;border-bottom:1px solid #e4e7ec}.dashboard-table td{padding:10px 0;border:0}.dashboard-table td::before{content:attr(data-label);display:block;margin-bottom:6px;color:#667085;font-size:10px;font-weight:600}.dashboard-table td:last-child .button{width:100%}.earnings-grid{grid-template-columns:1fr;gap:16px}.dashboard-section-head{align-items:flex-start}.dashboard-date{display:none}}
@media(max-width:480px){.overview-stats .stat-card{padding:15px}.stat-symbol{display:none}.portfolio-card{padding:21px}.portfolio-actions .button{width:100%}.earnings-detail summary{align-items:flex-start;flex-direction:column}.focus-list{padding:4px 15px 12px}.focus-item{gap:9px}.dashboard-nav a{padding:8px 10px}.top-actions .button{width:100%}}
@media(prefers-reduced-motion:reduce){.earnings-detail summary svg{transition:none}}
</style>
</head>
<body>
@php
    $queueTotal = $stats['pending_loans'] + $stats['pending_cash_payments'] + $stats['pending_verifications'];
    $receivableTotal = $stats['scheduled_receivables'] + $stats['penalty_charges'];
    $remainingBalance = max(0, $receivableTotal - $stats['total_collected']);
    $collectionProgress = $receivableTotal > 0 ? min(100, ($stats['total_collected'] / $receivableTotal) * 100) : 0;
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
        <article class="portfolio-card" aria-label="Loan portfolio">
            <div class="portfolio-label"><span>Portfolio balance</span><span class="portfolio-chip">Approved &amp; paid loans</span></div>
            <div class="portfolio-value">₱{{ number_format($remainingBalance, 2) }}</div><p class="portfolio-note">Remaining scheduled receivables, including recorded penalties.</p>
            <div class="collection-copy"><span>Collection progress</span><strong>{{ number_format($collectionProgress, 1) }}%</strong></div><div class="collection-track" role="progressbar" aria-label="Portfolio collection progress" aria-valuenow="{{ round($collectionProgress) }}" aria-valuemin="0" aria-valuemax="100"><div class="collection-fill" style="width:{{ number_format($collectionProgress, 2, '.', '') }}%"></div></div>
            <div class="portfolio-meta"><div><span>Principal released</span><strong>₱{{ number_format($stats['total_released'], 2) }}</strong></div><div><span>Total collected</span><strong>₱{{ number_format($stats['total_collected'], 2) }}</strong></div></div>
            <div class="portfolio-actions"><a class="button" href="{{ route('admin.payments.index') }}">View payments</a><a class="button button-glass" href="{{ route('admin.reports.index') }}">View reports</a></div>
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
