<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')

@property --completion {
    syntax: '<percentage>';
    inherits: false;
    initial-value: 0%;
}

@property --profit-interest {
    syntax: '<percentage>';
    inherits: false;
    initial-value: 0%;
}

@property --profit-total {
    syntax: '<percentage>';
    inherits: false;
    initial-value: 0%;
}

.main { background:radial-gradient(circle at 92% 0, rgba(79,70,229,.06), transparent 25%), var(--canvas); }
.dashboard-heading { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.attention-pill { display:inline-flex; align-items:center; gap:7px; padding:6px 10px; color:#92400e; background:#fffbeb; border:1px solid #fde68a; border-radius:999px; font-size:10px; font-weight:700; }
.attention-pill::before { content:''; width:7px; height:7px; background:#f59e0b; border-radius:50%; box-shadow:0 0 0 4px rgba(245,158,11,.13); }
.overview-stats { gap:14px; }
.overview-card { position:relative; min-height:142px; overflow:hidden; padding:19px; border-color:#e1e5eb; transition:transform .16s ease, box-shadow .16s ease; }
.overview-card:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(16,24,40,.06); }
.overview-card::after { content:''; position:absolute; top:-34px; right:-30px; width:102px; height:102px; background:var(--card-tint,#eef2ff); border-radius:50%; opacity:.78; }
.overview-head { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:12px; }
.overview-icon { display:grid; place-items:center; width:39px; height:39px; color:var(--card-color,#4f46e5); background:var(--card-tint,#eef2ff); border-radius:11px; }
.overview-icon svg { width:19px; height:19px; }
.overview-card .stat-value,.overview-card .stat-note { position:relative; z-index:1; }
.overview-card .stat-value { margin-top:13px; font-size:25px; }
.overview-released { --card-color:#6941c6; --card-tint:#f4f3ff; }
.overview-collected { --card-color:#067647; --card-tint:#ecfdf3; }
.overview-clients { --card-color:#0e7490; --card-tint:#ecfeff; }
.operations-grid { display:grid; grid-template-columns:minmax(0,1.18fr) minmax(320px,.82fr); gap:18px; margin-bottom:18px; align-items:stretch; }
.operations-grid .panel { height:100%; }
.operations-grid .panel + .panel { margin-top:0; }
.focus-panel { border-color:#dfe3ea; box-shadow:0 8px 24px rgba(16,24,40,.045); }
.panel-count { display:inline-flex; align-items:center; justify-content:center; min-width:29px; height:29px; padding:0 9px; color:#4338ca; background:#eef2ff; border-radius:999px; font-size:11px; font-weight:700; }
.focus-list { display:grid; padding:6px 20px 12px; }
.focus-item { display:grid; grid-template-columns:42px minmax(0,1fr) auto 18px; gap:12px; align-items:center; min-height:74px; padding:12px 2px; color:#344054; border-bottom:1px solid #f2f4f7; text-decoration:none; }
.focus-item:last-child { border-bottom:0; }
.focus-item:hover .focus-title { color:#4338ca; }
.focus-icon { display:grid; place-items:center; width:40px; height:40px; color:var(--focus-color,#b54708); background:var(--focus-bg,#fffaeb); border-radius:11px; }
.focus-icon svg { width:19px; height:19px; }
.focus-verification { --focus-color:#4338ca; --focus-bg:#eef2ff; }
.focus-payment { --focus-color:#0e7490; --focus-bg:#ecfeff; }
.focus-title { display:block; font-size:12px; font-weight:700; transition:color .15s ease; }
.focus-note { display:block; margin-top:4px; color:#667085; font-size:10px; line-height:1.4; }
.focus-value { min-width:32px; color:#101828; font-size:20px; font-weight:700; text-align:right; }
.focus-arrow { width:16px; height:16px; color:#98a2b3; }
.profit-panel { border-color:#d9d6fe; box-shadow:0 10px 28px rgba(79,70,229,.07); }
.profit-panel .panel-header { background:linear-gradient(145deg,#fff,#fafaff); }
.profit-margin-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 9px; color:#067647; background:#ecfdf3; border:1px solid #abefc6; border-radius:999px; font-size:9px; font-weight:700; white-space:nowrap; }
.profit-margin-badge::before { width:6px; height:6px; background:#12b76a; border-radius:50%; content:''; }
.profit-body { display:grid; grid-template-columns:146px minmax(0,1fr); gap:22px; align-items:center; padding:22px; }
.profit-chart-wrap { display:grid; place-items:center; }
.profit-ring { --profit-interest:var(--target-interest); --profit-total:var(--target-total); position:relative; display:grid; place-items:center; width:138px; height:138px; background:conic-gradient(#12b76a 0 var(--profit-interest),#f79009 var(--profit-interest) var(--profit-total),#eaecf0 var(--profit-total) 100%); border:1px solid rgba(255,255,255,.8); border-radius:50%; box-shadow:0 14px 30px rgba(16,24,40,.09),inset 0 0 0 1px rgba(255,255,255,.55); transition:--profit-interest 1.05s cubic-bezier(.22,1,.36,1),--profit-total 1.05s cubic-bezier(.22,1,.36,1); }
.profit-ring::before { position:absolute; width:94px; height:94px; background:linear-gradient(145deg,#fff,#f8fafc); border-radius:50%; box-shadow:0 0 0 1px rgba(228,231,236,.8),0 8px 18px rgba(16,24,40,.06); content:''; }
.profit-ring-copy { position:relative; max-width:80px; text-align:center; }
.profit-ring-copy strong { display:block; color:#101828; font-size:16px; letter-spacing:-.04em; overflow-wrap:anywhere; }
.profit-ring-copy span { display:block; margin-top:3px; color:#667085; font-size:8px; line-height:1.3; }
.profit-legend { display:grid; gap:12px; }
.profit-row { display:grid; grid-template-columns:10px minmax(0,1fr) auto; gap:9px; align-items:center; padding-bottom:11px; border-bottom:1px solid #f2f4f7; }
.profit-row:last-child { padding-bottom:0; border-bottom:0; }
.profit-dot { width:9px; height:9px; background:var(--profit-color); border-radius:3px; box-shadow:0 0 0 4px color-mix(in srgb,var(--profit-color) 12%,transparent); }
.profit-row-copy span { display:block; color:#475467; font-size:10px; font-weight:600; }.profit-row-copy small { display:block; margin-top:3px; color:#98a2b3; font-size:8px; }
.profit-row strong { color:#101828; font-size:11px; text-align:right; white-space:nowrap; }
.profit-footer { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); margin:0 22px; padding:16px 0 20px; border-top:1px solid #f2f4f7; }
.profit-meta { min-width:0; padding:0 12px; border-right:1px solid #f2f4f7; }.profit-meta:first-child { padding-left:0; }.profit-meta:last-child { padding-right:0; border-right:0; }
.profit-meta span { display:block; color:#98a2b3; font-size:8px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; }
.profit-meta strong { display:block; margin-top:5px; color:#344054; font-size:10px; overflow-wrap:anywhere; }
.profit-note { margin:0 22px 18px; padding:9px 11px; color:#475467; background:#f8fafc; border-radius:9px; font-size:8px; line-height:1.45; }
html.motion-enabled .profit-ring { --profit-interest:0%; --profit-total:0%; }
html.motion-enabled.motion-in .profit-ring { --profit-interest:var(--target-interest); --profit-total:var(--target-total); }
.recent-panel { border-color:#dfe3ea; box-shadow:0 8px 24px rgba(16,24,40,.045); }
.recent-table { min-width:980px; }
.recent-table th { padding-top:13px; padding-bottom:13px; background:#f8fafc; }
.recent-table td { padding-top:16px; padding-bottom:16px; }
.recent-client { min-width:155px; }
.recent-loan { min-width:150px; }
.recent-status { gap:6px; padding:6px 10px; }
.recent-status::before { content:''; width:6px; height:6px; background:currentColor; border-radius:50%; }
.recent-action { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; }
.recent-action:hover { color:#3730a3; background:#e0e7ff; }
.empty-dashboard { display:grid; place-items:center; min-height:190px; padding:34px; }
.empty-illustration { display:grid; place-items:center; width:48px; height:48px; margin-bottom:13px; color:#4f46e5; background:#eef2ff; border-radius:14px; }
.empty-illustration svg { width:23px; height:23px; }
.empty-dashboard strong { color:#344054; font-size:13px; }
.empty-dashboard span { margin-top:6px; color:#667085; font-size:11px; }
@media (max-width:1050px) { .operations-grid { grid-template-columns:1fr; } }
@media (max-width:760px) {
    .dashboard-heading { align-items:flex-start; flex-direction:column; }
    .overview-card { min-height:132px; }
    .profit-body { padding:18px; }
    .recent-panel { overflow:visible; background:transparent; border:0; box-shadow:none; }
    .recent-panel .panel-header { margin-bottom:10px; padding:18px; background:#fff; border:1px solid var(--border); border-radius:14px; }
    .recent-panel .table-wrap { overflow:visible; }
    .recent-table { min-width:0; }
    .recent-table thead { display:none; }
    .recent-table,.recent-table tbody,.recent-table tr,.recent-table td { display:block; width:100%; }
    .recent-table tbody { display:grid; gap:12px; }
    .recent-table tr { padding:6px 16px; background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:0 2px 8px rgba(16,24,40,.04); }
    .recent-table td { min-width:0; padding:11px 0; border-bottom:1px solid #f2f4f7; }
    .recent-table td::before { content:attr(data-label); display:block; margin-bottom:7px; color:#98a2b3; font-size:9px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .recent-table td:last-child { border-bottom:0; }
    .recent-action { width:100%; }
}
@media (max-width:470px) {
    .overview-stats { grid-template-columns:1fr 1fr; }
    .overview-card { min-height:128px; padding:15px; }
    .overview-card .stat-value { font-size:19px; }
    .overview-icon { width:34px; height:34px; }
    .focus-item { grid-template-columns:38px minmax(0,1fr) auto 14px; gap:9px; }
    .focus-icon { width:36px; height:36px; }
    .profit-body { grid-template-columns:1fr; justify-items:center; }
    .profit-legend { width:100%; }
    .profit-footer { grid-template-columns:1fr; gap:10px; }
    .profit-meta { padding:0 0 10px; border-right:0; border-bottom:1px solid #f2f4f7; }
    .profit-meta:last-child { padding-bottom:0; border-bottom:0; }
}
</style>
</head>
<body>
@php
    $queueTotal = $stats['pending_loans'] + $stats['pending_cash_payments'] + $stats['pending_verifications'];
    $profitTotal = max(0, $stats['projected_profit']);
    $interestShare = $profitTotal > 0 ? ($stats['contract_interest'] / $profitTotal) * 100 : 0;
    $penaltyShare = $profitTotal > 0 ? ($stats['penalty_charges'] / $profitTotal) * 100 : 0;
@endphp

@include('partials.admin-sidebar', ['active' => 'dashboard'])

<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div>
            <div class="eyebrow">Administration</div>
            <div class="dashboard-heading"><h1>Operations dashboard</h1>@if($queueTotal)<span class="attention-pill">{{ $queueTotal }} {{ Str::plural('item', $queueTotal) }} need attention</span>@endif</div>
            <p class="subtitle">A focused view of lending activity for {{ now()->timezone('Asia/Manila')->format('F j, Y') }}.</p>
        </div>
        <div class="top-actions">
            <a class="button button-primary" href="{{ route('admin.loans') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6"/></svg>Review requests</a>
        </div>
    </header>

    <x-flash-messages />

    <section class="stats-grid overview-stats" aria-label="Administrative summary">
        <article class="stat-card overview-card"><div class="overview-head"><span class="stat-label">Loan requests</span><span class="overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg></span></div><div class="stat-value">{{ $stats['total_loans'] }}</div><div class="stat-note">{{ $stats['pending_loans'] }} awaiting an admin decision</div></article>
        <article class="stat-card overview-card overview-released"><div class="overview-head"><span class="stat-label">Total released</span><span class="overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 6h8.5a3.5 3.5 0 0 1 0 7H7m0-4h9M10 4v16"/></svg></span></div><div class="stat-value">&#8369;{{ number_format($stats['total_released'], 2) }}</div><div class="stat-note">Approved and completed principal</div></article>
        <article class="stat-card overview-card overview-collected"><div class="overview-head"><span class="stat-label">Total collected</span><span class="overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span></div><div class="stat-value">&#8369;{{ number_format($stats['total_collected'], 2) }}</div><div class="stat-note">Approved payment amount</div></article>
        <article class="stat-card overview-card overview-clients"><div class="overview-head"><span class="stat-label">Registered clients</span><span class="overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3.5 19c.5-4 2.3-6 5.5-6s5 2 5.5 6M15 5.5a3 3 0 0 1 0 5.5M16 13c2.6.5 4 2.5 4.5 6"/></svg></span></div><div class="stat-value">{{ $stats['total_clients'] }}</div><div class="stat-note">Client accounts in the workspace</div></article>
    </section>

    <section class="operations-grid">
        <article class="panel focus-panel">
            <div class="panel-header"><div><h2 class="panel-title">Action center</h2><p class="panel-description">Work that is ready for your review.</p></div><span class="panel-count">{{ $queueTotal }}</span></div>
            <div class="focus-list">
                <a class="focus-item" href="{{ route('admin.loans') }}"><span class="focus-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg></span><span><span class="focus-title">Loan applications</span><span class="focus-note">Review submitted requests and record a decision.</span></span><span class="focus-value">{{ $stats['pending_loans'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
                <a class="focus-item focus-verification" href="{{ route('admin.verifications.index') }}"><span class="focus-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.7 7.5-7 9.5C7.7 18.5 5 15.5 5 11V6zM9 12l2 2 4-5"/></svg></span><span><span class="focus-title">Client verifications</span><span class="focus-note">Validate identity and employment submissions.</span></span><span class="focus-value">{{ $stats['pending_verifications'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
                <a class="focus-item focus-payment" href="{{ route('admin.payments.index') }}"><span class="focus-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg></span><span><span class="focus-title">Cash payment proofs</span><span class="focus-note">Confirm or reject manually submitted payments.</span></span><span class="focus-value">{{ $stats['pending_cash_payments'] }}</span><svg class="focus-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
            </div>
        </article>

        <aside class="panel profit-panel">
            <div class="panel-header"><div><h2 class="panel-title">Loan profit analytics</h2><p class="panel-description">Projected gross earnings from originated loans.</p></div><span class="profit-margin-badge">{{ number_format($stats['profit_margin'], 1) }}% margin</span></div>
            <div class="profit-body">
                <div class="profit-chart-wrap"><div class="profit-ring" role="img" aria-label="Projected profit PHP {{ number_format($profitTotal, 2) }}: {{ number_format($interestShare, 1) }} percent contract interest and {{ number_format($penaltyShare, 1) }} percent penalty charges" style="--target-interest:{{ number_format($interestShare, 2, '.', '') }}%;--target-total:{{ $profitTotal > 0 ? '100%' : '0%' }}"><div class="profit-ring-copy"><strong>&#8369;{{ number_format($profitTotal, 0) }}</strong><span>Projected gross profit</span></div></div></div>
                <div class="profit-legend">
                    <div class="profit-row" style="--profit-color:#12b76a"><span class="profit-dot"></span><div class="profit-row-copy"><span>Contract interest</span><small>{{ number_format($interestShare, 1) }}% of projected profit</small></div><strong>&#8369;{{ number_format($stats['contract_interest'], 2) }}</strong></div>
                    <div class="profit-row" style="--profit-color:#f79009"><span class="profit-dot"></span><div class="profit-row-copy"><span>Penalty charges</span><small>{{ number_format($penaltyShare, 1) }}% of projected profit</small></div><strong>&#8369;{{ number_format($stats['penalty_charges'], 2) }}</strong></div>
                </div>
            </div>
            <div class="profit-footer"><div class="profit-meta"><span>Principal released</span><strong>&#8369;{{ number_format($stats['total_released'], 2) }}</strong></div><div class="profit-meta"><span>Scheduled receivables</span><strong>&#8369;{{ number_format($stats['scheduled_receivables'], 2) }}</strong></div><div class="profit-meta"><span>Collected</span><strong>&#8369;{{ number_format($stats['total_collected'], 2) }}</strong></div></div>
            <p class="profit-note">Projected gross profit equals contractual interest plus recorded penalty charges. It is shown before defaults, operating costs, and other expenses.</p>
        </aside>
    </section>

    <section class="panel recent-panel">
        <div class="panel-header"><div><h2 class="panel-title">Recent loan requests</h2><p class="panel-description">The newest applications across all clients, shown in Philippine Time.</p></div><a class="button button-secondary button-small" href="{{ route('admin.loans') }}">View all requests</a></div>
        @if($recentLoans->isEmpty())
            <div class="empty-dashboard"><span class="empty-illustration"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg></span><strong>No loan requests yet</strong><span>New applications will appear here when clients submit them.</span></div>
        @else
            <div class="table-wrap"><table class="recent-table">
                <thead><tr><th>Client</th><th>Email</th><th>Loan</th><th>Requested</th><th>Submitted</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($recentLoans as $loan)
                    @php $submitted = $loan->created_at?->copy()->timezone('Asia/Manila'); @endphp
                    <tr>
                        <td class="recent-client" data-label="Client"><div class="identity"><span class="avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><span class="cell-title">{{ $loan->user?->name ?? 'Unknown client' }}</span></div></td>
                        <td class="email-cell" data-label="Email"><span class="email-value">{{ $loan->user?->email ?? 'No email address' }}</span></td>
                        <td class="recent-loan" data-label="Loan"><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></td>
                        <td class="amount" data-label="Requested">&#8369;{{ number_format($loan->amount, 2) }}</td>
                        <td data-label="Submitted"><div>{{ $submitted?->format('M d, Y') }}</div><div class="cell-secondary">{{ $submitted?->format('h:i A') }} PHT</div></td>
                        <td data-label="Status"><x-status-badge :status="$loan->status" class="recent-status" /></td>
                        <td data-label="Details"><a class="button button-small recent-action" href="{{ route('admin.loan.show', $loan->id) }}">View details</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        @endif
    </section>
</div></main>
</body>
</html>
