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
.portfolio-body { display:grid; grid-template-columns:118px minmax(0,1fr); gap:22px; align-items:center; padding:22px; }
.portfolio-ring { --ring:#4f46e5; --completion:var(--target-completion); position:relative; display:grid; place-items:center; width:112px; height:112px; background:conic-gradient(var(--ring) var(--completion),#eaecf0 0); border-radius:50%; transition:--completion 1.1s cubic-bezier(.22,1,.36,1); }
.portfolio-ring::before { content:''; position:absolute; width:82px; height:82px; background:#fff; border-radius:50%; }
.ring-copy { position:relative; text-align:center; }
.ring-copy strong { display:block; font-size:23px; letter-spacing:-.04em; }
.ring-copy span { display:block; margin-top:2px; color:#667085; font-size:9px; }
.portfolio-list { display:grid; gap:13px; }
.portfolio-row { display:grid; grid-template-columns:70px minmax(80px,1fr) 24px; gap:9px; align-items:center; color:#475467; font-size:10px; }
.portfolio-row strong { color:#101828; text-align:right; }
.portfolio-track { height:6px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.portfolio-fill { width:var(--target-width); height:100%; border-radius:inherit; transition:width .9s cubic-bezier(.22,1,.36,1); transition-delay:var(--progress-delay,260ms); }
html.motion-enabled .portfolio-ring { --completion:0%; }
html.motion-enabled.motion-in .portfolio-ring { --completion:var(--target-completion); }
html.motion-enabled .portfolio-fill { width:0; }
html.motion-enabled.motion-in .portfolio-fill { width:var(--target-width); }
.portfolio-footer { display:flex; justify-content:space-between; gap:12px; margin:0 22px; padding:15px 0 20px; border-top:1px solid #f2f4f7; }
.portfolio-meta span { display:block; color:#98a2b3; font-size:9px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; }
.portfolio-meta strong { display:block; margin-top:5px; color:#344054; font-size:12px; }
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
    .portfolio-body { grid-template-columns:100px minmax(0,1fr); gap:16px; padding:18px; }
    .portfolio-ring { width:96px; height:96px; }
    .portfolio-ring::before { width:70px; height:70px; }
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
    .portfolio-body { grid-template-columns:1fr; justify-items:center; }
    .portfolio-list { width:100%; }
}
</style>
</head>
<body>
@php
    $totalForDistribution = max(1, $stats['total_loans']);
    $statusRows = [
        ['label' => 'Pending', 'count' => $stats['pending_loans'], 'color' => '#f59e0b'],
        ['label' => 'Approved', 'count' => $stats['approved_loans'], 'color' => '#079455'],
        ['label' => 'Paid', 'count' => $stats['paid_loans'], 'color' => '#7c3aed'],
        ['label' => 'Rejected', 'count' => $stats['rejected_loans'], 'color' => '#d92d20'],
    ];
    $queueTotal = $stats['pending_loans'] + $stats['pending_cash_payments'] + $stats['pending_verifications'];
    $resolvedLoans = $stats['approved_loans'] + $stats['paid_loans'] + $stats['rejected_loans'];
    $resolvedPercent = $stats['total_loans'] > 0 ? round(($resolvedLoans / $stats['total_loans']) * 100) : 0;
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
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form>
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

        <aside class="panel">
            <div class="panel-header"><div><h2 class="panel-title">Portfolio overview</h2><p class="panel-description">Current application distribution.</p></div><span class="badge badge-neutral">{{ $resolvedPercent }}% resolved</span></div>
            <div class="portfolio-body">
                <div class="portfolio-ring" style="--target-completion: {{ $resolvedPercent }}%"><div class="ring-copy"><strong>{{ $stats['total_loans'] }}</strong><span>Total requests</span></div></div>
                <div class="portfolio-list">
                    @foreach($statusRows as $row)
                        <div class="portfolio-row"><span>{{ $row['label'] }}</span><div class="portfolio-track"><div class="portfolio-fill" style="--target-width: {{ ($row['count'] / $totalForDistribution) * 100 }}%; --progress-delay: {{ 260 + ($loop->index * 85) }}ms; background: {{ $row['color'] }}"></div></div><strong>{{ $row['count'] }}</strong></div>
                    @endforeach
                </div>
            </div>
            <div class="portfolio-footer"><div class="portfolio-meta"><span>Active loans</span><strong>{{ $stats['approved_loans'] }}</strong></div><div class="portfolio-meta"><span>Completed</span><strong>{{ $stats['paid_loans'] }}</strong></div><div class="portfolio-meta"><span>Overdue</span><strong class="{{ $overdueLoanCount > 0 ? 'danger-text' : '' }}">{{ $overdueLoanCount }}</strong></div></div>
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
