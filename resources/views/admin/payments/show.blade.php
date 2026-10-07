<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Details | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.payment-hero { display:grid; grid-template-columns:minmax(240px,.75fr) minmax(0,1.25fr); margin-bottom:22px; overflow:hidden; border-color:#d9d6fe; box-shadow:0 10px 28px rgba(79,70,229,.07); }
.payment-hero-main { padding:26px; color:#fff; background:linear-gradient(135deg,#3730a3,#4f46e5 62%,#7c3aed); }
.payment-hero-label { color:#c7d2fe; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.payment-hero-amount { margin-top:8px; font-size:32px; font-weight:700; letter-spacing:-.04em; }
.payment-hero-status { display:inline-flex; align-items:center; gap:7px; margin-top:13px; padding:6px 9px; color:#fff; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.2); border-radius:999px; font-size:10px; font-weight:700; }
.payment-hero-status::before { width:7px; height:7px; background:currentColor; border-radius:50%; content:''; }
.payment-hero-facts { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); align-items:center; background:#fff; }
.payment-hero-fact { min-width:0; padding:20px; border-left:1px solid #eaecf0; }
.payment-hero-fact span { display:block; color:#667085; font-size:9px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
.payment-hero-fact strong { display:block; margin-top:7px; color:#344054; font-size:12px; line-height:1.4; overflow-wrap:anywhere; }
.payment-review-layout { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:22px; align-items:start; }
.payment-main-stack,.payment-side-stack { display:grid; gap:18px; }
.payment-side-stack { position:sticky; top:24px; }
.overview-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:0 24px; }
.client-card { display:flex; align-items:center; gap:13px; padding:15px; background:#f8fafc; border:1px solid #eaecf0; border-radius:12px; }
.client-avatar { display:grid; place-items:center; width:42px; height:42px; flex:0 0 42px; color:#4338ca; background:#eef2ff; border-radius:12px; font-size:14px; font-weight:700; }
.client-copy { min-width:0; }.client-copy strong { display:block; font-size:13px; }.client-copy span { display:block; margin-top:3px; color:#667085; font-size:10px; overflow-wrap:anywhere; }
.loan-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:9px; margin-top:12px; }
.loan-summary-item { min-width:0; padding:12px; background:#f8fafc; border-radius:10px; }
.loan-summary-item span { display:block; color:#667085; font-size:9px; text-transform:uppercase; }.loan-summary-item strong { display:block; margin-top:5px; color:#344054; font-size:11px; overflow-wrap:anywhere; }
.technical-details { border-top:1px solid #eaecf0; }
.technical-details summary { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 22px; color:#475467; cursor:pointer; font-size:11px; font-weight:600; list-style:none; }
.technical-details summary::-webkit-details-marker { display:none; }.technical-details summary::after { content:'+'; font-size:17px; }.technical-details[open] summary::after { content:'−'; }
.technical-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; padding:0 22px 20px; }
.technical-item { min-width:0; padding:11px; background:#f8fafc; border-radius:9px; }.technical-item span { display:block; color:#667085; font-size:9px; }.technical-item code { display:block; margin-top:5px; color:#344054; font-family:inherit; font-size:10px; overflow-wrap:anywhere; }
.proof-panel .panel-body { padding:16px; }.proof-image { max-height:270px; }
.proof-actions { display:grid; margin-top:10px; }.proof-actions .button { width:100%; }
.decision-panel { border-color:#fedf89; box-shadow:0 9px 24px rgba(220,104,3,.08); }
.decision-panel .panel-header { background:#fffcf5; border-bottom-color:#fef0c7; }
.decision-note { display:flex; gap:9px; margin-bottom:14px; color:#854a0e; font-size:10px; line-height:1.5; }.decision-note svg { width:16px; height:16px; flex:0 0 16px; }
.decision-actions { display:grid; grid-template-columns:1fr 1fr; gap:9px; }.decision-actions .button { width:100%; min-height:42px; }
.automation-note { display:flex; gap:10px; padding:14px; color:#05603a; background:#ecfdf3; border:1px solid #abefc6; border-radius:10px; font-size:10px; line-height:1.5; }.automation-note svg { width:17px; height:17px; flex:0 0 17px; }
.status-timeline { display:grid; gap:13px; }.timeline-row { display:flex; gap:10px; color:#667085; font-size:10px; line-height:1.45; }.timeline-dot { width:9px; height:9px; flex:0 0 9px; margin-top:3px; background:#c7d2fe; border:2px solid #eef2ff; border-radius:50%; box-sizing:content-box; }.timeline-row.current .timeline-dot { background:#6366f1; }.timeline-row strong { display:block; color:#344054; font-size:11px; }
@media(max-width:1100px){.payment-review-layout{grid-template-columns:1fr}.payment-side-stack{position:static;grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.payment-hero{grid-template-columns:1fr}.payment-hero-facts{grid-template-columns:1fr 1fr 1fr}.payment-side-stack{grid-template-columns:1fr}.overview-grid,.technical-grid{grid-template-columns:1fr}.loan-summary{grid-template-columns:1fr 1fr 1fr}}
@media(max-width:520px){.payment-hero-facts,.loan-summary,.decision-actions{grid-template-columns:1fr}.payment-hero-fact{border-left:0;border-top:1px solid #eaecf0}}
</style>
</head>
<body>
@php
    $loan = $payment->loan;
    $submitted = $payment->created_at?->copy()->timezone('Asia/Manila');
    $paidAt = $payment->paid_at?->copy()->timezone('Asia/Manila');
    $methodLabel = $payment->method_label;
    $needsReview = $payment->status === 'pending' && $payment->method === 'cash';
@endphp
@include('partials.admin-sidebar', ['active' => 'payments'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Payment review</div><h1>Transaction #{{ $payment->id }}</h1><p class="subtitle">Everything needed to verify and process this payment.</p></div>
        <div class="top-actions"><a class="button button-primary" href="{{ route('admin.payment.receipt', $payment->id) }}" download="payment-receipt-{{ $payment->id }}.pdf" data-no-transition>Download PDF receipt</a><a class="button button-secondary" href="{{ route('admin.payments.index') }}">Back to payments</a></div>
    </header>
    <x-flash-messages />

    <section class="panel payment-hero">
        <div class="payment-hero-main"><div class="payment-hero-label">{{ $payment->currency ?? 'PHP' }} payment</div><div class="payment-hero-amount">&#8369;{{ number_format($payment->amount, 2) }}</div><div class="payment-hero-status">{{ ucfirst($payment->status) }}{{ $needsReview ? ' · Review needed' : '' }}</div></div>
        <div class="payment-hero-facts"><div class="payment-hero-fact"><span>Method</span><strong>{{ $methodLabel }}</strong></div><div class="payment-hero-fact"><span>Loan balance</span><strong>&#8369;{{ number_format($loan->getRemainingBalance(), 2) }}</strong></div><div class="payment-hero-fact"><span>Last updated</span><strong>{{ $payment->updated_at?->diffForHumans() }}</strong></div></div>
    </section>

    <div class="payment-review-layout">
        <div class="payment-main-stack">
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Payment overview</h2><p class="panel-description">Core details for this transaction.</p></div><x-status-badge :status="$payment->status" /></div>
                <div class="panel-body overview-grid">
                    <div class="detail-item"><div class="detail-label">Reference</div><div class="detail-value">{{ $payment->reference ?: 'Not assigned' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Payment method</div><div class="detail-value">{{ $methodLabel }}</div></div>
                    <div class="detail-item"><div class="detail-label">Submitted</div><div class="detail-value">{{ $submitted?->format('M d, Y · h:i A') }} PHT</div></div>
                    <div class="detail-item"><div class="detail-label">Confirmed</div><div class="detail-value">{{ $paidAt ? $paidAt->format('M d, Y · h:i A').' PHT' : 'Not confirmed' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Processing source</div><div class="detail-value">{{ $payment->provider ? ucfirst($payment->provider) : 'Manual submission' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Current status</div><div class="detail-value">{{ ucfirst($payment->status) }}</div></div>
                </div>
                <details class="technical-details"><summary>Technical provider details</summary><div class="technical-grid"><div class="technical-item"><span>Provider reference</span><code>{{ $payment->provider_reference ?: 'Not available' }}</code></div><div class="technical-item"><span>PayMongo session</span><code>{{ $payment->paymongo_session_id ?: 'Not applicable' }}</code></div><div class="technical-item"><span>PayMongo payment</span><code>{{ $payment->paymongo_payment_id ?: 'Not available' }}</code></div><div class="technical-item"><span>Internal transaction</span><code>#{{ $payment->id }}</code></div></div></details>
            </section>

            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Client and loan</h2><p class="panel-description">Account connected to this payment.</p></div><a class="button button-secondary button-small" href="{{ route('admin.loan.show', $loan->id) }}">Open loan</a></div>
                <div class="panel-body">
                    <div class="client-card"><span class="client-avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><div class="client-copy"><strong>{{ $loan->user?->name ?? 'Unknown client' }}</strong><span>{{ $loan->user?->email ?? 'No email address' }}</span></div></div>
                    <div class="loan-summary"><div class="loan-summary-item"><span>Loan</span><strong>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</strong></div><div class="loan-summary-item"><span>Product</span><strong>{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</strong></div><div class="loan-summary-item"><span>Principal</span><strong>&#8369;{{ number_format($loan->amount, 2) }}</strong></div><div class="loan-summary-item"><span>Total payable</span><strong>&#8369;{{ number_format($loan->getTotalWithPenalty(), 2) }}</strong></div><div class="loan-summary-item"><span>Current balance</span><strong>&#8369;{{ number_format($loan->getRemainingBalance(), 2) }}</strong></div><div class="loan-summary-item"><span>Loan status</span><strong>{{ ucfirst($loan->status) }}</strong></div></div>
                </div>
            </section>
        </div>

        <aside class="payment-side-stack">
            <section class="panel proof-panel">
                <div class="panel-header"><div><h2 class="panel-title">Payment proof</h2><p class="panel-description">{{ $payment->method === 'cash' ? 'Verify before deciding.' : 'Provider-verified payment.' }}</p></div></div>
                <div class="panel-body">
                    @if($payment->proof)
                        <a href="{{ route('admin.payment.proof', $payment) }}" target="_blank" rel="noopener"><img class="proof-image" src="{{ route('admin.payment.proof', $payment) }}" alt="Payment proof for transaction {{ $payment->id }}"></a>
                        <div class="proof-actions"><a class="button button-secondary" href="{{ route('admin.payment.proof', $payment) }}" target="_blank" rel="noopener">Open full-size proof</a></div>
                    @else
                        <div class="empty-state" style="padding:18px 0"><strong>No proof uploaded</strong>{{ $payment->method === 'cash' ? 'This cash payment has no attachment.' : 'Online payments are verified through PayMongo.' }}</div>
                    @endif
                </div>
            </section>

            @if($needsReview)
                <section class="panel decision-panel"><div class="panel-header"><div><h2 class="panel-title">Make a decision</h2><p class="panel-description">Cash payment awaiting review.</p></div></div><div class="panel-body"><div class="decision-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z"/></svg><span>Compare the amount, client, and uploaded proof before continuing.</span></div><div class="decision-actions"><form method="POST" action="{{ route('admin.payment.reject', $payment->id) }}" data-confirm="Reject this cash payment?">@csrf<button class="button button-danger" type="submit">Reject</button></form><form method="POST" action="{{ route('admin.payment.approve', $payment->id) }}" data-confirm="Approve this cash payment?">@csrf<button class="button button-success" type="submit">Approve</button></form></div></div></section>
            @elseif($payment->method !== 'cash')
                <section class="panel"><div class="panel-body"><div class="automation-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg><span>Online payment status is controlled automatically by verified PayMongo data. Manual approval is not required.</span></div></div></section>
            @endif

            <section class="panel"><div class="panel-header"><div><h2 class="panel-title">Activity</h2><p class="panel-description">Transaction timeline.</p></div></div><div class="panel-body status-timeline"><div class="timeline-row"><span class="timeline-dot"></span><div><strong>Submitted</strong>{{ $submitted?->format('M d, Y · h:i A') }} PHT</div></div><div class="timeline-row current"><span class="timeline-dot"></span><div><strong>{{ $paidAt ? 'Confirmed' : ucfirst($payment->status) }}</strong>{{ $paidAt ? $paidAt->format('M d, Y · h:i A').' PHT' : 'Last updated '.$payment->updated_at?->diffForHumans() }}</div></div></div></section>

            @if($loan->getOverdueDays() > 0)
                <section class="panel"><div class="panel-body"><div class="notice"><strong>Overdue loan</strong><br>{{ $loan->getOverdueDays() }} days overdue with &#8369;{{ number_format($loan->penalty_amount, 2) }} in penalties.</div></div></section>
            @endif
        </aside>
    </div>
</div></main>
@include('partials.admin-confirmation')
</body></html>
