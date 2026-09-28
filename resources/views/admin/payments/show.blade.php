<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Details | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@php
    $loan = $payment->loan;
    $submitted = $payment->created_at?->copy()->timezone('Asia/Manila');
    $paidAt = $payment->paid_at?->copy()->timezone('Asia/Manila');
    $methodLabel = $payment->method_label;
@endphp
@include('partials.admin-sidebar', ['active' => 'payments'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Payment review</div><h1>Transaction #{{ $payment->id }}</h1><p class="subtitle">Review the transaction, client, provider, and proof information.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.payments.index') }}">Back to payments</a><x-status-badge :status="$payment->status" /></div>
    </header>
    <x-flash-messages />

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Payment amount</div><div class="stat-value">₱{{ number_format($payment->amount, 2) }}</div><div class="stat-note">{{ $payment->currency ?? 'PHP' }} transaction</div></article>
        <article class="stat-card"><div class="stat-label">Loan balance</div><div class="stat-value">₱{{ number_format($loan->getRemainingBalance(), 2) }}</div><div class="stat-note">Current outstanding amount</div></article>
        <article class="stat-card"><div class="stat-label">Payment method</div><div class="stat-value" style="font-size:19px">{{ $methodLabel }}</div><div class="stat-note">{{ $payment->provider ? ucfirst($payment->provider).' provider' : 'Manual submission' }}</div></article>
        <article class="stat-card"><div class="stat-label">Payment status</div><div class="stat-value" style="font-size:19px">{{ ucfirst($payment->status) }}</div><div class="stat-note">Last updated {{ $payment->updated_at?->diffForHumans() }}</div></article>
    </section>

    <div class="detail-layout">
        <div>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Transaction information</h2><p class="panel-description">Identifiers and processing timestamps.</p></div></div>
                <div class="panel-body detail-grid">
                    <div class="detail-item"><div class="detail-label">Reference</div><div class="detail-value">{{ $payment->reference ?: 'Not assigned' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Provider reference</div><div class="detail-value">{{ $payment->provider_reference ?: 'Not available' }}</div></div>
                    <div class="detail-item"><div class="detail-label">PayMongo session</div><div class="detail-value">{{ $payment->paymongo_session_id ?: 'Not applicable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">PayMongo payment</div><div class="detail-value">{{ $payment->paymongo_payment_id ?: 'Not available' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Submitted</div><div class="detail-value">{{ $submitted?->format('M d, Y · h:i A') }} PHT</div></div>
                    <div class="detail-item"><div class="detail-label">Confirmed</div><div class="detail-value">{{ $paidAt ? $paidAt->format('M d, Y · h:i A').' PHT' : 'Not confirmed' }}</div></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Client and loan</h2><p class="panel-description">The account and balance connected to this transaction.</p></div><a class="button button-secondary button-small" href="{{ route('admin.loan.show', $loan->id) }}">View loan</a></div>
                <div class="panel-body detail-grid">
                    <div class="detail-item"><div class="detail-label">Client</div><div class="detail-value">{{ $loan->user?->name ?? 'Unknown client' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">{{ $loan->user?->email ?? 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Loan product</div><div class="detail-value">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Loan code</div><div class="detail-value">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></div>
                    <div class="detail-item"><div class="detail-label">Principal</div><div class="detail-value">₱{{ number_format($loan->amount, 2) }}</div></div>
                    <div class="detail-item"><div class="detail-label">Total payable</div><div class="detail-value">₱{{ number_format($loan->getTotalWithPenalty(), 2) }}</div></div>
                </div>
            </section>
        </div>

        <aside>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Payment proof</h2><p class="panel-description">Uploaded evidence for manual cash payments.</p></div></div>
                <div class="panel-body">
                    @if($payment->proof)
                        <a href="{{ asset('storage/'.$payment->proof) }}" target="_blank" rel="noopener"><img class="proof-image" src="{{ asset('storage/'.$payment->proof) }}" alt="Payment proof for transaction {{ $payment->id }}"></a>
                        <a class="button button-secondary" style="margin-top:14px" href="{{ asset('storage/'.$payment->proof) }}" target="_blank" rel="noopener">Open full size</a>
                    @else
                        <div class="empty-state" style="padding:22px 0"><strong>No proof uploaded</strong>{{ $payment->method === 'cash' ? 'This cash payment has no attachment.' : 'Online payments are verified through PayMongo.' }}</div>
                    @endif
                </div>
            </section>

            @if($loan->getOverdueDays() > 0)
                <section class="panel"><div class="panel-body"><div class="notice"><strong>Overdue loan</strong><br>{{ $loan->getOverdueDays() }} days overdue with ₱{{ number_format($loan->penalty_amount, 2) }} in penalties.</div></div></section>
            @endif

            @if($payment->status === 'pending' && $payment->method === 'cash')
                <section class="panel"><div class="panel-header"><div><h2 class="panel-title">Verification decision</h2><p class="panel-description">Confirm the proof before taking action.</p></div></div><div class="panel-body action-group">
                    <form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}" data-confirm="Approve this cash payment?">@csrf<button class="button button-success" type="submit">Approve payment</button></form>
                    <form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}" data-confirm="Reject this cash payment?">@csrf<button class="button button-danger" type="submit">Reject payment</button></form>
                </div></section>
            @elseif($payment->method !== 'cash')
                <section class="panel"><div class="panel-body"><div class="alert alert-success" style="margin:0">Online payment status is controlled automatically by verified PayMongo data.</div></div></section>
            @endif
        </aside>
    </div>
</div></main>
@include('partials.admin-confirmation')
</body></html>
