<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Requests | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')

.main { background: radial-gradient(circle at 90% 0, rgba(79, 70, 229, .065), transparent 26%), var(--canvas); }
.queue-heading { display:flex; align-items:center; gap:10px; }
.queue-pulse { display:inline-flex; align-items:center; gap:7px; padding:6px 10px; color:#92400e; background:#fffbeb; border:1px solid #fde68a; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
.queue-pulse::before { content:''; width:7px; height:7px; background:#f59e0b; border-radius:50%; box-shadow:0 0 0 4px rgba(245,158,11,.13); }
.loan-stats { gap:14px; }
.loan-stat { position:relative; min-height:138px; overflow:hidden; padding:19px; border-color:#e1e5eb; transition:transform .16s ease, box-shadow .16s ease; }
.loan-stat:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(16,24,40,.06); }
.loan-stat::after { content:''; position:absolute; top:-28px; right:-28px; width:90px; height:90px; background:var(--stat-tint, #eef2ff); border-radius:50%; opacity:.7; }
.loan-stat-head { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.loan-stat-icon { display:grid; place-items:center; width:38px; height:38px; color:var(--stat-color, var(--primary)); background:var(--stat-tint, #eef2ff); border-radius:11px; }
.loan-stat-icon svg { width:19px; height:19px; }
.loan-stat .stat-value { position:relative; z-index:1; margin-top:13px; font-size:25px; }
.loan-stat .stat-note { position:relative; z-index:1; }
.stat-pending { --stat-color:#b54708; --stat-tint:#fffaeb; }
.stat-active { --stat-color:#067647; --stat-tint:#ecfdf3; }
.stat-value-card { --stat-color:#6941c6; --stat-tint:#f4f3ff; }
.queue-panel { border-color:#dfe3ea; box-shadow:0 10px 30px rgba(16,24,40,.055); }
.queue-panel .panel-header { padding:22px 24px; }
.queue-count { display:flex; align-items:center; gap:7px; padding:7px 11px; color:#344054; background:#f8fafc; border:1px solid #e4e7ec; border-radius:999px; font-size:11px; font-weight:700; }
.queue-count::before { content:''; width:7px; height:7px; background:#6366f1; border-radius:50%; }
.status-tabs { display:flex; gap:7px; padding:13px 18px 0; overflow-x:auto; scrollbar-width:none; }
.status-tabs::-webkit-scrollbar { display:none; }
.status-tab { display:inline-flex; align-items:center; gap:7px; min-height:34px; padding:7px 11px; color:#667085; background:#fff; border:1px solid #e4e7ec; border-radius:9px; font-size:11px; font-weight:600; white-space:nowrap; cursor:pointer; transition:.15s ease; }
.status-tab:hover { color:#344054; background:#f9fafb; }
.status-tab.active { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; box-shadow:0 1px 2px rgba(79,70,229,.08); }
.tab-count { display:grid; place-items:center; min-width:19px; height:19px; padding:0 5px; color:inherit; background:rgba(255,255,255,.75); border-radius:999px; font-size:9px; }
.queue-toolbar { padding:12px 18px 15px; background:#fff; }
.search-field { position:relative; width:min(390px, 100%); }
.search-field svg { position:absolute; top:50%; left:12px; width:16px; height:16px; color:#98a2b3; transform:translateY(-50%); pointer-events:none; }
.search-field .search-input { width:100%; padding-left:36px; }
.filter-select { min-width:145px; }
.keyboard-hint { margin-left:-2px; padding:4px 7px; color:#98a2b3; background:#f8fafc; border:1px solid #eaecf0; border-radius:6px; font-size:10px; }
.loan-table { min-width:1080px; }
.loan-table th { padding-top:13px; padding-bottom:13px; background:#f8fafc; }
.loan-table td { padding-top:17px; padding-bottom:17px; }
.loan-table tbody tr { transition:background .15s ease; }
.loan-table tbody tr.pending-row { background:#fffdfa; }
.loan-table tbody tr.pending-row:hover { background:#fffbf3; }
.loan-table tbody tr.rejected-row { background:#fffdfd; }
.client-cell { min-width:205px; }
.client-cell .avatar { color:#4338ca; background:linear-gradient(145deg, #eef2ff, #e0e7ff); }
.request-cell { min-width:220px; }
.request-code { display:inline-flex; margin-top:5px; padding:3px 6px; color:#475467; background:#f2f4f7; border-radius:5px; font-size:9px; font-weight:600; letter-spacing:.02em; }
.purpose-line { display:flex; align-items:flex-start; gap:5px; max-width:230px; margin-top:7px; color:#667085; font-size:10px; line-height:1.4; }
.purpose-line svg { width:12px; height:12px; flex:0 0 12px; margin-top:1px; }
.money-cell { min-width:165px; }
.money-primary { color:#101828; font-size:13px; font-weight:700; white-space:nowrap; }
.money-breakdown { display:grid; gap:5px; margin-top:8px; padding-top:8px; border-top:1px dashed #e4e7ec; }
.money-line { display:flex; justify-content:space-between; gap:12px; color:#667085; font-size:10px; white-space:nowrap; }
.money-line strong { color:#475467; font-weight:600; }
.money-line.penalty, .money-line.penalty strong { color:#b42318; }
.submitted-cell { min-width:130px; }
.date-primary { color:#344054; font-weight:600; }
.status-badge { gap:6px; padding:6px 10px; }
.status-badge::before { content:''; width:6px; height:6px; background:currentColor; border-radius:50%; }
.loan-action-cell { width:128px; min-width:128px; }
.action-view { width:100%; color:#4338ca; background:#eef2ff; border-color:#c7d2fe; }
.action-view:hover { color:#3730a3; background:#e0e7ff; border-color:#a5b4fc; }
@media (max-width:760px) {
    .queue-heading { align-items:flex-start; flex-direction:column; }
    .queue-pulse { white-space:normal; }
    .queue-panel { overflow:visible; background:transparent; border:0; box-shadow:none; }
    .queue-panel .panel-header { margin-bottom:10px; padding:18px; background:#fff; border:1px solid var(--border); border-radius:14px; }
    .status-tabs { padding:4px 0 10px; }
    .queue-toolbar { padding:12px; border:1px solid var(--border); border-radius:13px; }
    .search-field { width:100%; }
    .filter-select { flex:1 1 145px; }
    .keyboard-hint { display:none; }
    .table-wrap { overflow:visible; }
    .loan-table { min-width:0; }
    .loan-table thead { display:none; }
    .loan-table, .loan-table tbody, .loan-table tr, .loan-table td { display:block; width:100%; }
    .loan-table tbody { display:grid; gap:12px; margin-top:12px; }
    .loan-table tr { padding:6px 16px; background:#fff !important; border:1px solid var(--border); border-radius:14px; box-shadow:0 2px 8px rgba(16,24,40,.04); }
    .loan-table td { min-width:0; padding:12px 0; border-bottom:1px solid #f2f4f7; }
    .loan-table td::before { content:attr(data-label); display:block; margin-bottom:8px; color:#98a2b3; font-size:9px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
    .loan-table td:last-child { border-bottom:0; }
    .loan-action-cell { width:100%; }
}
@media (max-width:470px) {
    .loan-stats { grid-template-columns:1fr 1fr; }
    .loan-stat { min-height:126px; padding:15px; }
    .loan-stat .stat-value { font-size:20px; }
    .queue-panel .panel-header { align-items:flex-start; }
}
</style>
</head>
<body class="admin-loans-page admin-loan-queue-page">
@php
    $requestCount = $loans->count();
    $pendingCount = $loans->where('status', 'pending')->count();
    $approvedCount = $loans->where('status', 'approved')->count();
    $paidCount = $loans->where('status', 'paid')->count();
    $rejectedCount = $loans->where('status', 'rejected')->count();
@endphp
@include('partials.admin-sidebar', ['active' => 'loans'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Loan operations</div><div class="queue-heading"><h1>Loan requests</h1>@if($pendingCount)<span class="queue-pulse">{{ $pendingCount }} waiting for review</span>@endif</div><p class="subtitle">Review and compare applications, then open the details page to record a decision.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    </header>
    <x-flash-messages />
    @if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

    <section class="stats-grid loan-stats" aria-label="Loan request summary">
        <article class="stat-card loan-stat"><div class="loan-stat-head"><div class="stat-label">All requests</div><span class="loan-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg></span></div><div class="stat-value">{{ $requestCount }}</div><div class="stat-note">Complete application history</div></article>
        <article class="stat-card loan-stat stat-pending"><div class="loan-stat-head"><div class="stat-label">Pending review</div><span class="loan-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v4l2.5 1.5"/></svg></span></div><div class="stat-value">{{ $pendingCount }}</div><div class="stat-note">Requires an admin decision</div></article>
        <article class="stat-card loan-stat stat-active"><div class="loan-stat-head"><div class="stat-label">Active loans</div><span class="loan-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12.5 9.5 17 19 7.5"/></svg></span></div><div class="stat-value">{{ $approvedCount }}</div><div class="stat-note">Approved outstanding loans</div></article>
        <article class="stat-card loan-stat stat-value-card"><div class="loan-stat-head"><div class="stat-label">Requested value</div><span class="loan-stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 6h8.5a3.5 3.5 0 0 1 0 7H7m0-4h9M10 4v16"/></svg></span></div><div class="stat-value">₱{{ number_format($loans->sum('amount'), 2) }}</div><div class="stat-note">Combined principal requested</div></article>
    </section>

    <section class="panel queue-panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Application queue</h2><p class="panel-description">Use the status shortcuts or search to find the request that needs attention.</p></div><span class="queue-count">{{ $requestCount }} {{ Str::plural('record', $requestCount) }}</span></div>
        <div class="status-tabs" role="group" aria-label="Quick status filters">
            <button class="status-tab active" type="button" data-status-filter="">All <span class="tab-count">{{ $requestCount }}</span></button>
            <button class="status-tab" type="button" data-status-filter="pending">Pending <span class="tab-count">{{ $pendingCount }}</span></button>
            <button class="status-tab" type="button" data-status-filter="approved">Approved <span class="tab-count">{{ $approvedCount }}</span></button>
            <button class="status-tab" type="button" data-status-filter="paid">Paid <span class="tab-count">{{ $paidCount }}</span></button>
            <button class="status-tab" type="button" data-status-filter="rejected">Rejected <span class="tab-count">{{ $rejectedCount }}</span></button>
        </div>
        <div class="toolbar queue-toolbar">
            <label class="search-field" for="loan-search"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/></svg><input class="search-input" id="loan-search" type="search" placeholder="Search client, email, loan code, or purpose…" aria-label="Search loans"></label>
            <select class="filter-select" id="loan-status" aria-label="Filter loan status"><option value="">All statuses</option><option value="pending" @selected(request('status') === 'pending')>Pending</option><option value="approved" @selected(request('status') === 'approved')>Approved</option><option value="paid" @selected(request('status') === 'paid')>Paid</option><option value="rejected" @selected(request('status') === 'rejected')>Rejected</option></select>
            <span class="keyboard-hint">Press / to search</span>
        </div>

        @if($loans->isEmpty())
            <div class="empty-state"><strong>No loan requests yet</strong>New client applications will appear here for review.</div>
        @else
            <div class="table-wrap"><table class="loan-table">
                <thead><tr><th>Client</th><th>Email</th><th>Request</th><th>Financials</th><th>Submitted</th><th>Status</th><th>Details</th></tr></thead>
                <tbody id="loan-table">
                @foreach($loans as $loan)
                    @php
                        $overdueDays = $loan->getOverdueDays();
                        $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
                        $loanName = $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan';
                    @endphp
                    <tr class="{{ $loan->status }}-row" data-status="{{ $loan->status }}" data-search="{{ strtolower(($loan->user?->name ?? '').' '.($loan->user?->email ?? '').' '.$loan->loan_code.' '.$loanName.' '.$loan->purpose) }}">
                        <td class="client-cell" data-label="Client"><div class="identity"><span class="avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><span class="cell-title">{{ $loan->user?->name ?? 'Unknown client' }}</span></div></td>
                        <td class="email-cell" data-label="Email"><span class="email-value">{{ $loan->user?->email ?? 'No email address' }}</span></td>
                        <td class="request-cell" data-label="Request"><div class="cell-title">{{ $loanName }}</div><span class="request-code">{{ $loan->loan_code ?: 'LOAN-'.$loan->id }}</span>@if($loan->purpose)<div class="purpose-line"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.7 7-11a7 7 0 1 0-14 0c0 6.3 7 11 7 11Zm0-8.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg><span>{{ $loan->purpose }}</span></div>@endif</td>
                        <td class="money-cell" data-label="Financials"><div class="money-primary">₱{{ number_format($loan->amount, 2) }}</div><div class="cell-secondary">Requested principal</div><div class="money-breakdown"><div class="money-line"><span>Total due</span><strong>₱{{ number_format($loan->getTotalWithPenalty(), 2) }}</strong></div>@if($overdueDays > 0)<div class="money-line penalty"><span>Penalty</span><strong>₱{{ number_format($loan->penalty_amount, 2) }}</strong></div>@endif</div></td>
                        <td class="submitted-cell" data-label="Submitted"><div class="date-primary">{{ $submitted?->format('M d, Y') ?? 'Unavailable' }}</div><div class="cell-secondary">{{ $submitted?->format('h:i A') }}{{ $submitted ? ' PHT' : '' }}</div>@if($overdueDays > 0)<div class="cell-secondary danger-text">{{ $overdueDays }} {{ Str::plural('day', $overdueDays) }} overdue</div>@endif</td>
                        <td data-label="Status"><x-status-badge :status="$loan->status" class="status-badge" /></td>
                        <td class="loan-action-cell" data-label="Details"><a class="button button-small action-view" href="{{ route('admin.loan.show', $loan->id) }}">View details</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="loan-empty" hidden><strong>No matching loan requests</strong>Try a different search term or clear the selected status.</div>
        @endif
    </section>
</div></main>
@include('partials.admin-confirmation')
<script>
const loanStatusFilter = document.getElementById('loan-status');
const statusTabs = document.querySelectorAll('[data-status-filter]');
statusTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.statusFilter === loanStatusFilter.value));

statusTabs.forEach((tab) => tab.addEventListener('click', () => {
    loanStatusFilter.value = tab.dataset.statusFilter;
    loanStatusFilter.dispatchEvent(new Event('change', { bubbles: true }));
    statusTabs.forEach((item) => item.classList.toggle('active', item === tab));
}));

loanStatusFilter?.addEventListener('change', () => {
    statusTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.statusFilter === loanStatusFilter.value));
});

document.addEventListener('click', (event) => {
    if (!event.target.closest('.queue-toolbar .btn-outline-secondary')) return;
    statusTabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.statusFilter === ''));
});

</script>
</body>
</html>
