<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Requests | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'loans'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Loan operations</div><h1>Loan requests</h1><p class="subtitle">Review applications, documents, balances, and approval status.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">All requests</div><div class="stat-value">{{ $loans->count() }}</div><div class="stat-note">Complete application history</div></article>
        <article class="stat-card"><div class="stat-label">Pending review</div><div class="stat-value">{{ $loans->where('status', 'pending')->count() }}</div><div class="stat-note">Requires an admin decision</div></article>
        <article class="stat-card"><div class="stat-label">Active loans</div><div class="stat-value">{{ $loans->where('status', 'approved')->count() }}</div><div class="stat-note">Approved balances</div></article>
        <article class="stat-card"><div class="stat-label">Requested value</div><div class="stat-value">₱{{ number_format($loans->sum('amount'), 2) }}</div><div class="stat-note">Across every request</div></article>
    </section>

    <section class="panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Application queue</h2><p class="panel-description">Newest applications appear first.</p></div><span class="badge badge-neutral">{{ $loans->count() }} records</span></div>
        <div class="toolbar">
            <input class="search-input" id="loan-search" type="search" placeholder="Search client, email, or loan code…" aria-label="Search loans">
            <select class="filter-select" id="loan-status" aria-label="Filter loan status"><option value="">All statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="paid">Paid</option><option value="rejected">Rejected</option></select>
        </div>
        @if($loans->isEmpty())
            <div class="empty-state"><strong>No loan requests</strong>Submitted applications will appear here.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Client</th><th>Loan</th><th>Requested</th><th>Total due</th><th>Penalty</th><th>Submitted</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="loan-table">
                @foreach($loans as $loan)
                    @php
                        $overdueDays = $loan->getOverdueDays();
                        $statusClass = match ($loan->status) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'paid' => 'badge-purple', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                        $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
                    @endphp
                    <tr data-status="{{ $loan->status }}" data-search="{{ strtolower(($loan->user?->name ?? '').' '.($loan->user?->email ?? '').' '.$loan->loan_code) }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><span><span class="cell-title">{{ $loan->user?->name ?? 'Unknown client' }}</span><span class="cell-secondary">{{ $loan->user?->email ?? 'No email' }}</span></span></div></td>
                        <td><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></td>
                        <td class="amount">₱{{ number_format($loan->amount, 2) }}</td>
                        <td>₱{{ number_format($loan->getTotalWithPenalty(), 2) }}</td>
                        <td class="{{ $overdueDays > 0 ? 'danger-text' : '' }}">{{ $overdueDays > 0 ? '₱'.number_format($loan->penalty_amount, 2) : 'None' }}@if($overdueDays > 0)<div class="cell-secondary danger-text">{{ $overdueDays }} days overdue</div>@endif</td>
                        <td><div>{{ $submitted?->format('M d, Y') }}</div><div class="cell-secondary">{{ $submitted?->format('h:i A') }} PHT</div></td>
                        <td><span class="badge {{ $statusClass }}">{{ ucfirst($loan->status) }}</span></td>
                        <td><div class="action-group">
                            <a class="button button-secondary button-small" href="{{ route('admin.loan.show', $loan->id) }}">Details</a>
                            @if($loan->status === 'pending')
                                <form method="POST" action="{{ route('admin.loan.approve', $loan->id) }}" data-confirm="Approve this loan request?">@csrf<button class="button button-success button-small" type="submit">Approve</button></form>
                                <form method="POST" action="{{ route('admin.loan.reject', $loan->id) }}" data-confirm="Reject this loan request?">@csrf<button class="button button-danger button-small" type="submit">Reject</button></form>
                            @endif
                        </div></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="loan-empty" hidden><strong>No matching loans</strong>Adjust the search or status filter.</div>
        @endif
    </section>
</div></main>
@include('partials.admin-confirmation')
</body></html>
