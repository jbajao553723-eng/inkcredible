<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.payments-panel { overflow:visible; }
.payment-stat-alert { border-color:#fedf89; background:linear-gradient(145deg,#fff,#fffcf5); }
.payment-stat-alert .stat-value { color:#b54708; }
.review-banner { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; padding:14px 16px; color:#854a0e; background:#fffaeb; border:1px solid #fedf89; border-radius:12px; }
.review-banner-copy { display:flex; align-items:center; gap:11px; min-width:0; }
.review-banner-icon { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; color:#b54708; background:#fef0c7; border-radius:9px; }
.review-banner-icon svg { width:17px; height:17px; }
.review-banner strong { display:block; font-size:12px; }.review-banner p { margin:3px 0 0; font-size:10px; line-height:1.4; }
.payment-filters { position:relative; display:flex; align-items:center; gap:8px; padding:12px 18px; border-bottom:1px solid var(--border); }
.filter-field { position:relative; min-width:0; }
.filter-field .search-input, .filter-field .filter-select { width:100%; min-height:36px; border-color:#e4e7ec; border-radius:7px; background-color:#fff; }
.filter-search { flex:1 1 280px; min-width:220px; }
.filter-search svg { position:absolute; top:50%; left:11px; width:15px; height:15px; color:#98a2b3; transform:translateY(-50%); pointer-events:none; }
.filter-search .search-input { padding-left:34px; }
.search-submit { min-width:36px; min-height:36px; padding:7px; color:#475467; background:#fff; border-color:#e4e7ec; }
.search-submit svg { width:15px; height:15px; }
.filter-popover { position:relative; flex:0 0 auto; }
.filter-popover > summary { list-style:none; }
.filter-popover > summary::-webkit-details-marker { display:none; }
.filter-trigger { min-height:36px; padding:7px 12px; color:#344054; background:#fff; border-color:#e4e7ec; white-space:nowrap; }
.filter-popover[open] .filter-trigger { color:#4338ca; background:#f5f3ff; border-color:#c7d2fe; }
.filter-count-badge { display:grid; place-items:center; min-width:17px; height:17px; padding:0 5px; color:#fff; background:#6366f1; border-radius:999px; font-size:9px; }
.filter-popover-panel { position:absolute; top:calc(100% + 9px); right:0; z-index:30; width:min(520px, calc(100vw - 36px)); padding:16px; background:#fff; border:1px solid #e4e7ec; border-radius:12px; box-shadow:0 14px 35px rgba(16,24,40,.14); }
.filter-popover-head { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
.filter-popover-title { margin:0; font-size:13px; font-weight:700; }
.filter-popover-note { color:#98a2b3; font-size:10px; }
.filter-options { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.filter-option label { display:block; margin-bottom:6px; color:#667085; font-size:10px; font-weight:600; }
.filter-actions { display:flex; justify-content:flex-end; align-items:center; gap:6px; margin-top:16px; padding-top:12px; border-top:1px solid #f2f4f7; }
.filter-actions .button { min-height:34px; padding:6px 11px; white-space:nowrap; }
.clear-filter { color:#667085; border-color:transparent; background:transparent; }
.quick-filters { display:flex; gap:22px; padding:0 18px; border-bottom:1px solid var(--border); overflow-x:auto; }
.quick-filter { flex:0 0 auto; padding:11px 0 9px; color:#667085; border-bottom:2px solid transparent; font-size:10px; font-weight:600; text-decoration:none; white-space:nowrap; }
.quick-filter:hover, .quick-filter.active { color:#4338ca; border-bottom-color:#6366f1; }
.client-profile { min-width:250px; }
.client-meta { min-width:0; }
.client-meta .cell-title, .client-meta .email-value { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.client-tags { display:flex; gap:10px; margin-top:6px; color:#98a2b3; font-size:9px; flex-wrap:wrap; }
.client-tag { white-space:nowrap; }
.payment-reference { min-width:190px; }
.loan-inline { display:flex; align-items:center; gap:6px; margin-top:7px; color:#667085; font-size:10px; }
.loan-inline strong { color:#475467; font-weight:600; }
.payment-meta { min-width:145px; }
.payment-meta .method { margin-top:5px; color:#475467; font-size:10px; font-weight:600; }
.amount-actions { min-width:205px; }
.amount-actions .amount { display:block; margin-bottom:7px; color:#101828; font-size:13px; }
.amount-actions .action-group { gap:5px; }
.review-row { background:#fffcf5; }
.review-row:hover { background:#fffaeb; }
.pagination-bar { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:14px 18px; color:#667085; border-top:1px solid var(--border); font-size:11px; }
.pagination-actions { display:flex; gap:7px; align-items:center; }
.pagination-actions .button.disabled { color:#98a2b3; background:#f9fafb; pointer-events:none; }
@media(max-width:700px){.filter-popover-panel{width:calc(100vw - 28px)}.filter-options{grid-template-columns:1fr}.filter-actions .button-primary{flex:1}.pagination-bar{align-items:stretch;flex-direction:column}.pagination-actions .button{flex:1}.review-banner{align-items:flex-start;flex-direction:column}.review-banner>a{width:100%}}
@media(max-width:440px){.payment-filters{padding:10px 12px}.filter-search{min-width:0}.search-submit{display:none}.filter-trigger{padding-inline:10px}.quick-filters{gap:18px}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'payments'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Payment operations</div><h1>Payments</h1><p class="subtitle">Review cash submissions and track every client transaction.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    </header>
    <x-flash-messages />

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Transactions</div><div class="stat-value">{{ number_format($stats['transactions']) }}</div><div class="stat-note">Complete payment history</div></article>
        <article class="stat-card"><div class="stat-label">Total collected</div><div class="stat-value">&#8369;{{ number_format($stats['collected'], 2) }}</div><div class="stat-note">Approved payment total</div></article>
        <article class="stat-card payment-stat-alert"><div class="stat-label">Needs review</div><div class="stat-value">{{ number_format($stats['pending_cash']) }}</div><div class="stat-note">Pending cash submissions</div></article>
        <article class="stat-card"><div class="stat-label">Clients represented</div><div class="stat-value">{{ number_format($stats['clients']) }}</div><div class="stat-note">Clients with payment records</div></article>
    </section>

    @if($stats['pending_cash'] > 0)
        <div class="review-banner"><div class="review-banner-copy"><span class="review-banner-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z"/></svg></span><div><strong>{{ $stats['pending_cash'] }} cash {{ $stats['pending_cash'] === 1 ? 'payment' : 'payments' }} waiting</strong><p>Review the uploaded proof before approving or rejecting each submission.</p></div></div><a class="button button-secondary button-small" href="{{ route('admin.payments.index', ['status' => 'pending', 'method' => 'cash']) }}">Open review queue</a></div>
    @endif

    <section class="panel payments-panel async-filter-region" id="payment-directory" data-async-filter-region data-async-filter-target="#payment-directory" aria-live="polite">
        <div class="panel-header"><div><h2 class="panel-title">All transactions</h2><p class="panel-description">Newest and review-ready payments appear first.</p></div><span class="badge badge-neutral">{{ number_format($payments->total()) }} results</span></div>
        @php
            $hasFilters = (bool) ($filters['q'] || $filters['status'] || $filters['method'] || $filters['date_from'] || $filters['date_to']);
            $advancedFilterCount = (int) filled($filters['status']) + (int) filled($filters['method']) + (int) filled($filters['date_from']) + (int) filled($filters['date_to']);
        @endphp
        <form class="payment-filters" method="GET" action="{{ route('admin.payments.index') }}" data-async-filter data-async-filter-target="#payment-directory" data-no-transition>
            <div class="filter-field filter-search"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/></svg><input class="search-input" id="payment-search" name="q" type="search" value="{{ $filters['q'] }}" placeholder="Search client, contact, reference, or loan" aria-label="Search payments"></div>
            <button class="button search-submit" type="submit" aria-label="Search payments" title="Search"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/></svg></button>
            <details class="filter-popover">
                <summary class="button filter-trigger"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M7 12h10m-7 6h4"/></svg>Filters @if($advancedFilterCount)<span class="filter-count-badge">{{ $advancedFilterCount }}</span>@endif</summary>
                <div class="filter-popover-panel">
                    <div class="filter-popover-head"><h3 class="filter-popover-title">Filter payments</h3><span class="filter-popover-note">Narrow the payment list</span></div>
                    <div class="filter-options">
                        <div class="filter-field filter-option"><label for="payment-status">Status</label><select class="filter-select" id="payment-status" name="status"><option value="">All statuses</option><option value="pending" @selected($filters['status'] === 'pending')>Pending</option><option value="approved" @selected($filters['status'] === 'approved')>Approved</option><option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option></select></div>
                        <div class="filter-field filter-option"><label for="payment-method">Method</label><select class="filter-select" id="payment-method" name="method"><option value="">All methods</option><option value="gcash" @selected($filters['method'] === 'gcash')>GCash / QR Ph</option><option value="cash" @selected($filters['method'] === 'cash')>Cash</option></select></div>
                        <div class="filter-field filter-option"><label for="payment-date-from">From date</label><input class="search-input" id="payment-date-from" name="date_from" type="date" value="{{ $filters['date_from'] }}"></div>
                        <div class="filter-field filter-option"><label for="payment-date-to">To date</label><input class="search-input" id="payment-date-to" name="date_to" type="date" value="{{ $filters['date_to'] }}"></div>
                    </div>
                    <div class="filter-actions">@if($hasFilters)<a class="button clear-filter" href="{{ route('admin.payments.index') }}" data-async-filter-link data-async-filter-target="#payment-directory" data-no-transition>Clear all</a>@endif<button class="button button-primary" type="submit">Apply filters</button></div>
                </div>
            </details>
            <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
        </form>
        <div class="quick-filters" aria-label="Quick payment filters">
            <a class="quick-filter {{ ! $filters['status'] && ! $filters['method'] ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" data-async-filter-link data-async-filter-target="#payment-directory" data-no-transition>All payments</a>
            <a class="quick-filter {{ $filters['status'] === 'pending' && $filters['method'] === 'cash' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'pending', 'method' => 'cash']) }}" data-async-filter-link data-async-filter-target="#payment-directory" data-no-transition>Pending cash review ({{ $stats['pending_cash'] }})</a>
            <a class="quick-filter {{ $filters['status'] === 'approved' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'approved']) }}" data-async-filter-link data-async-filter-target="#payment-directory" data-no-transition>Approved</a>
            <a class="quick-filter {{ $filters['status'] === 'rejected' ? 'active' : '' }}" href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" data-async-filter-link data-async-filter-target="#payment-directory" data-no-transition>Rejected</a>
        </div>
        @if($payments->isEmpty())
            <div class="empty-state"><strong>No matching payments</strong>Try another client name, reference, status, method, or date range.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Client &amp; loan</th><th>Transaction</th><th>Payment</th><th>Status</th><th>Amount &amp; actions</th></tr></thead>
                <tbody id="payment-table">
                @foreach($payments as $payment)
                    @php
                        $statusClass = match ($payment->status) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                        $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                        $client = $payment->loan?->user;
                        $clientSummary = $client ? $clientSummaries->get($client->id) : null;
                        $needsReview = $payment->status === 'pending' && $payment->method === 'cash';
                    @endphp
                    <tr class="{{ $needsReview ? 'review-row' : '' }}">
                        <td class="client-profile"><div class="identity"><span class="avatar">{{ strtoupper(substr($client?->name ?? 'U', 0, 1)) }}</span><span class="client-meta"><span class="cell-title">{{ $client?->name ?? 'Unknown client' }}</span><span class="email-value">{{ $client?->email ?? 'No email address' }}</span></span></div><div class="loan-inline"><strong>{{ $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</strong><span>&middot;</span><span>{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</span></div><div class="client-tags"><span class="client-tag">{{ number_format($clientSummary?->payment_count ?? 0) }} payments</span><span class="client-tag">&#8369;{{ number_format((float) ($clientSummary?->collected ?? 0), 2) }} collected</span></div></td>
                        <td class="payment-reference"><div class="cell-title">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}@if($payment->provider_reference) &middot; Provider {{ $payment->provider_reference }}@endif</div></td>
                        <td class="payment-meta"><div>{{ $paymentTime?->format('M d, Y') }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') }} PHT</div><div class="method">{{ $payment->method_label }}</div></td>
                        <td><span class="badge {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>@if($needsReview)<div class="cell-secondary">Needs review</div>@endif</td>
                        <td class="amount-actions"><span class="amount">&#8369;{{ number_format($payment->amount, 2) }}</span><div class="action-group">
                            <a class="button button-secondary button-small" href="{{ route('admin.payment.show', $payment->id) }}">Details</a>
                            <a class="button button-secondary button-small" href="{{ route('admin.payment.receipt', $payment->id) }}" download="payment-receipt-{{ $payment->id }}.png" data-no-transition>Receipt PNG</a>
                            @if($needsReview)
                                <form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}" data-confirm="Approve this cash payment?">@csrf<button class="button button-success button-small" type="submit">Approve</button></form>
                                <form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}" data-confirm="Reject this cash payment?">@csrf<button class="button button-danger button-small" type="submit">Reject</button></form>
                            @endif
                        </div></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="pagination-bar">
                <span>Showing {{ number_format($payments->firstItem()) }}-{{ number_format($payments->lastItem()) }} of {{ number_format($payments->total()) }} matching payments</span>
                <div class="pagination-actions">
                    <a class="button button-secondary button-small {{ $payments->onFirstPage() ? 'disabled' : '' }}" href="{{ $payments->previousPageUrl() ?: '#' }}">Previous</a>
                    <span class="button button-secondary button-small">Page {{ $payments->currentPage() }} of {{ $payments->lastPage() }}</span>
                    <a class="button button-secondary button-small {{ $payments->hasMorePages() ? '' : 'disabled' }}" href="{{ $payments->nextPageUrl() ?: '#' }}">Next</a>
                </div>
            </div>
        @endif
    </section>
</div></main>
@include('partials.admin-confirmation')
<script>
document.addEventListener('click', (event) => {
    const paymentFilterPopover = document.querySelector('.filter-popover[open]');
    if (paymentFilterPopover && !paymentFilterPopover.contains(event.target)) {
        paymentFilterPopover.removeAttribute('open');
    }
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') document.querySelector('.filter-popover[open]')?.removeAttribute('open');
});
</script>
</body></html>
