<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@php
    $confirmed = $payments->whereIn('status', ['paid', 'approved']);
    $pendingCash = $payments->where('status', 'pending')->where('method', 'cash');
@endphp
@include('partials.admin-sidebar', ['active' => 'payments'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Payment operations</div><h1>Payment management</h1><p class="subtitle">Monitor online transactions and review submitted cash payments.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Transactions</div><div class="stat-value">{{ $payments->count() }}</div><div class="stat-note">Complete payment history</div></article>
        <article class="stat-card"><div class="stat-label">Total collected</div><div class="stat-value">₱{{ number_format($confirmed->sum('amount'), 2) }}</div><div class="stat-note">Confirmed payment total</div></article>
        <article class="stat-card"><div class="stat-label">Pending cash</div><div class="stat-value">{{ $pendingCash->count() }}</div><div class="stat-note">Requires manual verification</div></article>
        <article class="stat-card"><div class="stat-label">Online payments</div><div class="stat-value">{{ $payments->where('provider', 'paymongo')->count() }}</div><div class="stat-note">Processed through PayMongo</div></article>
    </section>

    <section class="panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Transactions</h2><p class="panel-description">Newest payment activity appears first.</p></div><span class="badge badge-neutral">{{ $payments->count() }} records</span></div>
        <div class="toolbar">
            <input class="search-input" id="payment-search" type="search" placeholder="Search client, reference, or loan…" aria-label="Search payments">
            <select class="filter-select" id="payment-status" aria-label="Filter payment status"><option value="">All statuses</option><option value="pending">Pending</option><option value="paid">Paid</option><option value="approved">Approved</option><option value="failed">Failed</option><option value="rejected">Rejected</option><option value="refunded">Refunded</option></select>
            <select class="filter-select" id="payment-method" aria-label="Filter payment method"><option value="">All methods</option><option value="gcash">GCash / QR Ph</option><option value="cash">Cash</option></select>
        </div>
        @if($payments->isEmpty())
            <div class="empty-state"><strong>No payments found</strong>Client transactions will appear here.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Client</th><th>Reference</th><th>Loan</th><th>Date &amp; time</th><th>Method</th><th>Status</th><th>Amount</th><th>Actions</th></tr></thead>
                <tbody id="payment-table">
                @foreach($payments as $payment)
                    @php
                        $statusClass = match ($payment->status) { 'paid', 'approved' => 'badge-success', 'pending' => 'badge-warning', 'failed', 'rejected' => 'badge-danger', 'refunded' => 'badge-purple', default => 'badge-neutral' };
                        $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                        $methodLabel = $payment->method === 'gcash' ? 'GCash / QR Ph' : ucfirst($payment->method);
                    @endphp
                    <tr data-status="{{ $payment->status }}" data-method="{{ $payment->method }}" data-search="{{ strtolower(($payment->loan?->user?->name ?? '').' '.$payment->reference.' '.($payment->loan?->loan_code ?? '')) }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($payment->loan?->user?->name ?? 'U', 0, 1)) }}</span><span><span class="cell-title">{{ $payment->loan?->user?->name ?? 'Unknown client' }}</span><span class="cell-secondary">{{ $payment->loan?->user?->email ?? 'No email' }}</span></span></div></td>
                        <td><div class="cell-title">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}</div></td>
                        <td><div>{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</div></td>
                        <td><div>{{ $paymentTime?->format('M d, Y') }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') }} PHT</div></td>
                        <td>{{ $methodLabel }}</td>
                        <td><span class="badge {{ $statusClass }}">{{ in_array($payment->status, ['paid', 'approved'], true) ? 'Paid' : ucfirst($payment->status) }}</span></td>
                        <td class="amount">₱{{ number_format($payment->amount, 2) }}</td>
                        <td><div class="action-group">
                            <a class="button button-secondary button-small" href="{{ route('admin.payment.show', $payment->id) }}">Details</a>
                            @if($payment->status === 'pending' && $payment->method === 'cash')
                                <form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}" data-confirm="Approve this cash payment?">@csrf<button class="button button-success button-small" type="submit">Approve</button></form>
                                <form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}" data-confirm="Reject this cash payment?">@csrf<button class="button button-danger button-small" type="submit">Reject</button></form>
                            @endif
                        </div></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="payment-empty" hidden><strong>No matching payments</strong>Adjust the search or filters.</div>
        @endif
    </section>
</div></main>
@include('partials.admin-confirmation')
</body></html>
