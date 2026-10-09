<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments | Inkcredible</title>
<link rel="icon" type="image/png" href="{{ asset('images/inkcredible-logo.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')

<style>
@include('partials.client-portal-styles')

.workspace-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(340px, .65fr); gap: 22px; align-items: start; }
.panel-body { padding: 22px 24px 24px; }

.loan-list { display: grid; gap: 12px; }
.balances-panel { position: sticky; top: 24px; }
.loan-card { width: 100%; padding: 16px; color: inherit; background: #fafafa; border: 1px solid var(--border); border-radius: 13px; text-align: left; cursor: pointer; transition: .16s ease; }
.loan-card:hover { background: #fff; border-color: #a5b4fc; box-shadow: 0 8px 20px rgba(16, 24, 40, .06); transform: translateY(-1px); }
.loan-card.active { background: linear-gradient(145deg, #fafaff, #f4f3ff); border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); }
.loan-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.loan-name { font-size: 0.875rem; font-weight: 600; }
.loan-code { margin-top: 3px; color: var(--muted); font-size: 0.75rem; }
.loan-balance { margin-top: 14px; font-size: 1.3125rem; font-weight: 700; letter-spacing: -.02em; }
.loan-caption { margin-top: 3px; color: var(--muted); font-size: 0.75rem; }
.loan-meta { display: flex; justify-content: space-between; gap: 12px; margin-top: 13px; padding-top: 12px; border-top: 1px solid #eaecf0; color: var(--muted); font-size: 0.6875rem; }
.loan-meta strong { display: block; margin-top: 3px; color: #344054; font-size: 0.75rem; font-weight: 600; }
.progress-track { height: 5px; margin-top: 13px; overflow: hidden; background: #e9eaf0; border-radius: 999px; }
.progress-bar { height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: inherit; }

.form-group { margin-bottom: 18px; }
.form-label { display: block; margin-bottom: 7px; color: #344054; font-size: 0.8125rem; font-weight: 600; }
.form-control { width: 100%; min-height: 45px; padding: 10px 12px; color: var(--navy); background: #fff; border: 1px solid #d0d5dd; border-radius: 10px; outline: none; transition: border-color .15s, box-shadow .15s; }
.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
.form-help { margin: 7px 0 0; color: var(--muted); font-size: 0.75rem; line-height: 1.45; }
.payment-section + .payment-section { margin-top: 22px; padding-top: 22px; border-top: 1px solid #eaecf0; }
.payment-step { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.payment-step-number { display: grid; place-items: center; width: 27px; height: 27px; flex: 0 0 27px; color: #fff; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 8px; font-size: 0.6875rem; font-weight: 700; }
.payment-step-title { font-size: 0.8125rem; font-weight: 700; }
.account-select { position: relative; display: grid; grid-template-columns: auto minmax(0, 1fr) auto; gap: 12px; align-items: center; min-height: 70px; padding: 11px 13px; background: linear-gradient(145deg, #fff, #fafaff); border: 1px solid #d0d5dd; border-radius: 12px; transition: .16s ease; }
.account-select:hover { border-color: #a5b4fc; }
.account-select:focus-within { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
.account-icon { display: grid; place-items: center; width: 40px; height: 40px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.account-icon svg { width: 18px; height: 18px; }
.account-select-main { min-width: 0; }
.account-select-label { display: block; margin-bottom: 3px; color: var(--muted); font-size: 0.625rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
.account-select-control { width: 100%; padding: 0 4px 0 0; color: #1f2a44; background: transparent; border: 0; outline: 0; appearance: none; font-size: 0.875rem; font-weight: 700; cursor: pointer; }
.account-select-arrow { display: grid; place-items: center; width: 28px; height: 28px; color: #667085; background: #f2f4f7; border-radius: 8px; pointer-events: none; }
.account-select-arrow svg { width: 15px; height: 15px; }
.account-preview { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; gap: 18px; align-items: center; margin-top: 9px; padding: 11px 13px; background: #f8f9ff; border: 1px solid #e0e4f5; border-radius: 11px; }
.account-preview[hidden] { display: none; }
.account-preview-label { color: var(--muted); font-size: 0.625rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
.account-preview-value { display: block; margin-top: 3px; color: #344054; font-size: 0.75rem; font-weight: 700; }
.amount-control { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 9px; }
.amount-input { position: relative; }
.amount-input span { position: absolute; top: 50%; left: 13px; color: #344054; transform: translateY(-50%); font-weight: 700; }
.amount-input .form-control { padding-left: 35px; font-size: 0.9375rem; font-weight: 600; }
.full-balance-button { padding: 0 14px; color: #4338ca; background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer; }
.full-balance-button:hover { background: #e0e7ff; }
.full-balance-button:disabled { opacity: .5; cursor: not-allowed; }

.method-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
.method-option { position: relative; }
.method-option input { position: absolute; opacity: 0; pointer-events: none; }
.method-card { display: flex; align-items: center; gap: 10px; min-height: 72px; padding: 12px; border: 1px solid #d0d5dd; border-radius: 11px; cursor: pointer; transition: .15s ease; }
.method-card:hover { border-color: #a5b4fc; }
.method-option input:checked + .method-card { border-color: var(--primary); background: #f5f3ff; box-shadow: 0 0 0 1px var(--primary); }
.method-option input:focus + .method-card { box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
.method-logo { display: grid; place-items: center; width: 36px; height: 36px; flex: 0 0 36px; color: #fff; background: var(--primary); border-radius: 9px; font-size: 0.75rem; font-weight: 700; }
.method-logo.cash { color: #067647; background: #d1fadf; }
.method-name { display: block; font-size: 0.8125rem; font-weight: 600; }
.method-note { display: block; margin-top: 2px; color: var(--muted); font-size: 0.6875rem; }
.proof-section { display: none; }
.proof-upload { display: flex; align-items: center; gap: 12px; min-height: 78px; padding: 14px; background: #fafbff; border: 1px dashed #aeb7c5; border-radius: 11px; cursor: pointer; transition: .15s ease; }
.proof-upload:hover { background: #f5f3ff; border-color: #818cf8; }
.proof-upload-icon { display: grid; place-items: center; width: 37px; height: 37px; flex: 0 0 37px; color: var(--primary); background: #eef2ff; border-radius: 9px; }
.proof-upload-icon svg { width: 18px; height: 18px; }
.proof-upload-title { display: block; color: #344054; font-size: 0.75rem; font-weight: 700; }
.proof-file-name { display: block; margin-top: 3px; color: var(--muted); font-size: 0.6875rem; }
.proof-input { position: absolute; width: 1px; height: 1px; overflow: hidden; opacity: 0; }

.submit-button { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 47px; margin-top: 4px; color: #fff; background: var(--primary); border: 0; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 20px rgba(79, 70, 229, .18); transition: .15s ease; }
.submit-button:hover { background: var(--primary-dark); transform: translateY(-1px); }
.submit-button:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.submit-button svg { width: 18px; height: 18px; }

.payment-progress { width:min(460px,calc(100% - 32px)); padding:0; overflow:hidden; color:var(--navy); background:#fff; border:0; border-radius:18px; box-shadow:0 30px 80px rgba(16,24,40,.24); }
.payment-progress::backdrop { background:rgba(16,24,40,.55); backdrop-filter:blur(3px); }
.payment-progress-card { padding:30px; text-align:center; }
.payment-progress-spinner { width:48px; height:48px; margin:0 auto 18px; border:4px solid #e0e7ff; border-top-color:var(--primary); border-radius:50%; animation:payment-progress-spin .8s linear infinite; }
.payment-progress h2 { margin:0; font-size:1.375rem; letter-spacing:-.025em; }
.payment-progress-copy { margin:10px auto 0; color:var(--muted); font-size:0.8125rem; line-height:1.6; }
.payment-progress-state { margin:18px 0; padding:12px 14px; color:#344054; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; font-size:0.75rem; line-height:1.5; }
.payment-progress-route { display:flex; align-items:center; justify-content:center; gap:7px; margin-top:16px; color:#667085; font-size:0.6875rem; font-weight:600; }
.payment-progress-route svg { width:15px; height:15px; color:var(--primary); }
@keyframes payment-progress-spin { to { transform:rotate(360deg); } }

.history-panel { margin-top: 22px; }
.history-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 22px 24px; border-bottom: 1px solid var(--border); }
.history-count { color: var(--muted); font-size: 0.75rem; }
.transaction-ref { max-width: 190px; overflow: hidden; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
.receipt-link { display:inline-flex; align-items:center; justify-content:center; min-height:32px; padding:7px 10px; color:#4338ca; background:#f5f3ff; border:1px solid #ddd6fe; border-radius:8px; font-size:0.6875rem; font-weight:700; text-decoration:none; white-space:nowrap; }
.receipt-link:hover { color:#3730a3; background:#ede9fe; border-color:#c4b5fd; }
@media (max-width: 1080px) {
    .workspace-grid { grid-template-columns: 1fr; }
    .balances-panel { position: static; }
}

@media (max-width: 760px) {
    .method-grid { grid-template-columns: 1fr; }
}

@media (max-width: 460px) {
    .panel-header, .panel-body, .history-header { padding-left: 18px; padding-right: 18px; }
    .account-preview { grid-template-columns: 1fr 1fr; gap: 10px; }
    .account-preview > :first-child { grid-column: 1 / -1; }
    .amount-control { grid-template-columns: 1fr; }
    .full-balance-button { min-height: 42px; }
}

@include('partials.client-workspace-styles')
.workspace-grid { grid-template-columns:minmax(0,1.25fr) minmax(300px,.75fr); }
.payment-summary-grid .stat-card { box-shadow:none; }
.loan-card.active { background:#f5f7ff; }
.balances-panel .loan-meta { line-height:1.5; }
.history-header { padding:20px 22px; }
.method-card { background:#fff; }
@media(max-width:1080px) { .workspace-grid { grid-template-columns:1fr; } }
</style>
</head>

<body>
@php
    $payments = $payments ?? collect();
    $loans = $loans->filter(fn ($loan) => $loan->status === 'approved' && $loan->getRemainingBalance() > 0)->values();
    $totalOutstanding = $loans->sum(fn ($loan) => $loan->getRemainingBalance());
    $totalPaid = $payments->where('status', 'approved')->sum('amount');
    $pendingCount = $payments->where('status', 'pending')->count();
@endphp

@include('partials.client-sidebar', ['active' => 'payments'])

<main class="main" id="main-content" tabindex="-1">
    <div class="page-shell">
        <header class="topbar">
            <div>
                <div class="eyebrow">Loan servicing</div>
                <h1>Payments &amp; billing</h1>
                <p class="subtitle">Pay securely and keep track of every transaction in one place.</p>
            </div>
        </header>

        <nav class="workspace-nav" aria-label="Payment sections">
            <a class="nav-current" href="#make-payment" data-no-transition>Make a payment</a>
            <a href="#active-balances" data-no-transition>Loan balances <span>{{ $loans->count() }}</span></a>
            <a href="#payment-history" data-no-transition>History &amp; receipts <span>{{ $payments->count() }}</span></a>
            <a href="{{ route('dashboard') }}">Back to overview &rarr;</a>
        </nav>

        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="stats-grid payment-summary-grid" aria-label="Payment summary">
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Outstanding balance</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v11H4zM4 10h16M8 15h3"/></svg></span></div>
                <div class="stat-value">&#8369;{{ number_format($totalOutstanding, 2) }}</div>
                <div class="stat-note">Across {{ $loans->count() }} active {{ Str::plural('loan', $loans->count()) }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Successfully paid</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span></div>
                <div class="stat-value">&#8369;{{ number_format($totalPaid, 2) }}</div>
                <div class="stat-note">Confirmed payment total</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Pending review</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v4l2.5 1.5"/></svg></span></div>
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-note">Awaiting confirmation</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Transactions</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16l-3-2-3 2-3-2-3 2zM9 9h6M9 13h6"/></svg></span></div>
                <div class="stat-value">{{ $payments->count() }}</div>
                <div class="stat-note">Complete payment history</div>
            </article>
        </section>

        <section class="workspace-grid">
            <article class="panel" id="make-payment" style="scroll-margin-top:20px">
                <div class="panel-header">
                    <div><h2 class="panel-title">Make a payment</h2><p class="panel-description">Choose an account, enter an amount, and select how you want to pay.</p></div>
                    <span class="badge badge-purple">Secure payment</span>
                </div>
                <div class="panel-body">
                    @if($loans->isEmpty())
                        <div class="empty-state"><strong>No balance is currently due</strong>Approved loans with an outstanding balance will appear here.</div>
                    @else
                        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" id="payment-form" data-no-transition>
                            @csrf
                            <section class="payment-section">
                                <div class="payment-step"><span class="payment-step-number">1</span><span class="payment-step-title">Loan and payment amount</span></div>
                                <div class="form-group">
                                    <label class="form-label" for="loan-id">Loan account</label>
                                    <div class="account-select">
                                        <span class="account-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v11H4zM4 10h16M8 15h3"/></svg></span>
                                        <span class="account-select-main">
                                            <span class="account-select-label">Choose an account</span>
                                            <select name="loan_id" class="account-select-control" required id="loan-id">
                                                <option value="">Select a loan to pay</option>
                                        @foreach($loans as $loan)
                                            @php
                                                $loanName = $loan->loanType->display_name ?? $loan->loanType->name ?? 'Loan';
                                                $nextSchedule = $loan->paymentSchedules->sortBy('due_date')->first(fn ($schedule) => $schedule->status !== 'paid' && (float) $schedule->scheduled_amount + (float) $schedule->penalty_amount > (float) $schedule->paid_amount);
                                                $nextAmount = $nextSchedule
                                                    ? max(0, (float) $nextSchedule->scheduled_amount + (float) $nextSchedule->penalty_amount - (float) $nextSchedule->paid_amount)
                                                    : $loan->getRemainingBalance();
                                            @endphp
                                                    <option value="{{ $loan->id }}"
                                                            data-balance="{{ number_format($loan->getRemainingBalance(), 2, '.', '') }}"
                                                            data-installment="{{ number_format(min($nextAmount, $loan->getRemainingBalance()), 2, '.', '') }}"
                                                            data-code="{{ $loan->loan_code ?: 'Loan #'.$loan->id }}"
                                                            data-due="{{ $nextSchedule?->due_date?->format('M d, Y') ?? 'Not scheduled' }}"
                                    @selected((string) old('loan_id', request()->query('loan')) === (string) $loan->id)>
                                                        {{ $loanName }} — &#8369;{{ number_format($loan->getRemainingBalance(), 2) }} remaining
                                                    </option>
                                        @endforeach
                                            </select>
                                        </span>
                                        <span class="account-select-arrow"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 10 5 5 5-5"/></svg></span>
                                    </div>
                                    <div class="account-preview" id="account-preview" hidden>
                                        <div><span class="account-preview-label">Loan reference</span><strong class="account-preview-value" id="account-preview-code">&mdash;</strong></div>
                                        <div><span class="account-preview-label">Next due</span><strong class="account-preview-value" id="account-preview-due">&mdash;</strong></div>
                                        <div><span class="account-preview-label">Installment due</span><strong class="account-preview-value" id="account-preview-installment">&mdash;</strong></div>
                                        <div><span class="account-preview-label">Outstanding</span><strong class="account-preview-value" id="account-preview-balance">&mdash;</strong></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="payment-amount">Payment amount</label>
                                    <div class="amount-control">
                                        <div class="amount-input"><span>&#8369;</span><input type="number" step="0.01" min="0.01" name="amount" class="form-control" id="payment-amount" value="{{ old('amount') }}" placeholder="0.00" required></div>
                                        <button class="full-balance-button" type="button" id="full-balance-button">Full balance</button>
                                    </div>
                                    <p class="form-help" id="amount-help">Select a loan to load its current outstanding balance.</p>
                                </div>
                            </section>

                            <section class="payment-section">
                                <div class="payment-step"><span class="payment-step-number">2</span><span class="payment-step-title">Payment method</span></div>
                                <div class="method-grid">
                                    <label class="method-option">
                                        <input type="radio" name="method" value="qrph" @checked(old('method', 'qrph') === 'qrph')>
                                        <span class="method-card"><span class="method-logo"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M3 3h6v6H3zM15 3h6v6h-6zM3 15h6v6H3zM15 15h3v3h3v3h-6zM12 3v9H3M12 15v6M18 12h3"/></svg></span><span><span class="method-name">QR Ph</span><span class="method-note">Scan in your bank or e-wallet app. Confirmation is automatic.</span></span></span>
                                    </label>
                                    <label class="method-option">
                                        <input type="radio" name="method" value="cash" @checked(old('method') === 'cash')>
                                        <span class="method-card"><span class="method-logo cash">&#8369;</span><span><span class="method-name">Cash payment</span><span class="method-note">Upload your receipt. An administrator confirms the payment.</span></span></span>
                                    </label>
                                </div>
                            </section>

                            <section class="payment-section proof-section" id="proof-section">
                                <div class="payment-step"><span class="payment-step-number">3</span><span class="payment-step-title">Cash payment proof</span></div>
                                <label class="proof-upload" for="proof">
                                    <span class="proof-upload-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg></span>
                                    <span><span class="proof-upload-title">Upload payment receipt</span><span class="proof-file-name" id="proof-file-name">Choose a JPG or PNG image up to 2 MB</span></span>
                                </label>
                                <input type="file" name="proof" class="proof-input" id="proof" accept="image/jpeg,image/png">
                            </section>

                            <div class="payment-review" aria-live="polite"><div><div class="payment-review-label">You are submitting</div><strong id="review-payment-amount">&#8369;0.00</strong><small id="review-payment-account">Select a loan account</small></div><div><div class="payment-review-label">Payment method</div><strong id="review-payment-method">QR Ph</strong></div></div>
                            <div class="payment-error" id="payment-error" role="alert" hidden></div>
                            <button type="submit" class="submit-button" id="submit-payment">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                                <span>Continue to secure payment</span>
                            </button>
                            <p class="payment-guidance" id="payment-guidance">QR Ph continues to secure PayMongo checkout in this tab, then returns here automatically.</p>
                        </form>
                    @endif
                </div>
            </article>

            <aside class="panel balances-panel" id="active-balances" style="scroll-margin-top:20px">
                <div class="panel-header"><div><h2 class="panel-title">Active balances</h2><p class="panel-description">Select a balance to load it into the payment form.</p></div><span class="history-count">{{ $loans->count() }} active</span></div>
                <div class="panel-body">
                    <div class="loan-list">
                        @forelse($loans as $loan)
                            @php
                                $loanTotal = (float) $loan->getTotalWithPenalty();
                                $loanRemaining = (float) $loan->getRemainingBalance();
                                $loanProgress = $loanTotal > 0 ? min(100, max(0, ((float) $loan->paid_amount / $loanTotal) * 100)) : 100;
                                $nextSchedule = $loan->paymentSchedules->sortBy('due_date')->first(fn ($schedule) => $schedule->status !== 'paid' && (float) $schedule->scheduled_amount + (float) $schedule->penalty_amount > (float) $schedule->paid_amount);
                                $isOverdue = $loan->paymentSchedules->contains(fn ($schedule) => $schedule->status === 'overdue');
                            @endphp
                            <button class="loan-card" type="button" data-loan-choice="{{ $loan->id }}" aria-label="Select {{ $loan->loanType->display_name ?? $loan->loanType->name ?? 'loan' }} for payment">
                                <div class="loan-card-top">
                                    <div><div class="loan-name">{{ $loan->loanType->display_name ?? $loan->loanType->name ?? 'Loan' }}</div><div class="loan-code">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></div>
                                    <span class="badge {{ $isOverdue ? 'badge-danger' : 'badge-success' }}">{{ $isOverdue ? 'Overdue' : $loan->status }}</span>
                                </div>
                                <div class="loan-balance">&#8369;{{ number_format($loanRemaining, 2) }}</div>
                                <div class="loan-caption">Remaining of &#8369;{{ number_format($loanTotal, 2) }}</div>
                                <div class="progress-track" aria-hidden="true"><div class="progress-bar" style="width: {{ number_format($loanProgress, 2, '.', '') }}%"></div></div>
                                <div class="loan-meta">
                                    <span>Next due<strong>{{ $nextSchedule?->due_date?->format('M d, Y') ?? 'No due date' }}</strong></span>
                                    <span>Penalty<strong>&#8369;{{ number_format($loan->penalty_amount, 2) }}</strong></span>
                                </div>
                            </button>
                        @empty
                            <div class="empty-state"><strong>You're all caught up</strong>There are no active balances to display.</div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </section>

        <section class="panel history-panel payment-history" id="payment-history" data-record-list>
            <div class="history-header">
                <div><h2 class="panel-title">Payment history</h2><p class="panel-description">Newest transactions appear first. Times are shown in Philippine Time.</p></div>
                <span class="history-count">{{ $payments->count() }} {{ Str::plural('record', $payments->count()) }}</span>
            </div>

            @if($payments->isEmpty())
                <div class="empty-state"><strong>No payment history yet</strong>Your submitted payments will appear here with their date and time.</div>
            @else
                <div class="record-toolbar">
                    <div class="record-search"><label for="payment-history-search">Find a transaction</label><input id="payment-history-search" type="search" placeholder="Search reference, loan, date, or method" data-record-search></div>
                    <div class="record-status"><label for="payment-history-status">Status</label><select id="payment-history-status" data-record-status><option value="">All payments</option><option value="approved">Confirmed</option><option value="pending">Pending</option><option value="rejected">Rejected</option></select></div>
                    <p class="record-count" data-record-count role="status" aria-live="polite">{{ $payments->count() }} records</p>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Reference</th><th>Loan</th><th>Date &amp; time</th><th>Method</th><th>Status</th><th>Amount</th><th>Receipt</th></tr></thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @php
                                    $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                                    $statusClass = match ($payment->status) {
                                        'approved' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'rejected' => 'badge-danger',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <tr data-record-row data-record-status-value="{{ $payment->status }}">
                                    <td data-label="Reference"><div class="transaction-ref" title="{{ $payment->reference }}">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}</div></td>
                                    <td data-label="Loan"><div>{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</div></td>
                                    <td data-label="Date"><div>{{ $paymentTime?->format('M d, Y') ?? '—' }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') ?? '' }} PHT · {{ $payment->paid_at ? 'Confirmed' : 'Submitted' }}</div></td>
                                    <td data-label="Method">{{ $payment->method_label }}</td>
                                    <td data-label="Status"><span class="badge {{ $statusClass }}">{{ $payment->status === 'approved' ? 'Confirmed' : ucfirst($payment->status) }}</span></td>
                                    <td class="amount" data-label="Amount">&#8369;{{ number_format($payment->amount, 2) }}</td>
                                    <td data-label="Download"><a class="receipt-link" href="{{ route('payments.receipt', $payment) }}" download="payment-receipt-{{ $payment->id }}.pdf" data-no-transition>{{ $payment->status === 'approved' ? 'Receipt PDF' : 'Status PDF' }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="record-empty" data-record-empty hidden>No transactions match your search. Try another reference or status.</div>
            @endif
        </section>
    </div>
</main>

<dialog class="payment-progress" id="payment-progress" aria-labelledby="payment-progress-title">
    <div class="payment-progress-card">
        <div class="payment-progress-spinner" aria-hidden="true"></div>
        <h2 id="payment-progress-title">Connecting to PayMongo</h2>
        <p class="payment-progress-copy">Secure checkout will continue in this tab. After payment, PayMongo returns you to Inkcredible automatically.</p>
        <div class="payment-progress-state" id="payment-progress-state" role="status" aria-live="polite">Creating a secure checkout session&hellip;</div>
        <div class="payment-progress-route"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-5-5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>Inkcredible &rarr; PayMongo &rarr; Inkcredible</div>
    </div>
</dialog>

@if($loans->isNotEmpty())
<script>
const paymentForm = document.getElementById('payment-form');
const loanSelect = document.getElementById('loan-id');
const amountInput = document.getElementById('payment-amount');
const amountHelp = document.getElementById('amount-help');
const fullBalanceButton = document.getElementById('full-balance-button');
const methodInputs = document.querySelectorAll('input[name="method"]');
const proofSection = document.getElementById('proof-section');
const proofInput = document.getElementById('proof');
const proofFileName = document.getElementById('proof-file-name');
const submitButton = document.getElementById('submit-payment');
const loanCards = document.querySelectorAll('[data-loan-choice]');
const accountPreview = document.getElementById('account-preview');
const accountPreviewCode = document.getElementById('account-preview-code');
const accountPreviewDue = document.getElementById('account-preview-due');
const accountPreviewInstallment = document.getElementById('account-preview-installment');
const accountPreviewBalance = document.getElementById('account-preview-balance');
const paymentProgress = document.getElementById('payment-progress');
const paymentProgressState = document.getElementById('payment-progress-state');
const paymentError = document.getElementById('payment-error');
const reviewAmount = document.getElementById('review-payment-amount');
const reviewAccount = document.getElementById('review-payment-account');
const reviewMethod = document.getElementById('review-payment-method');
const paymentGuidance = document.getElementById('payment-guidance');

function peso(value) {
    return `₱${Number(value).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

function selectedMethod() {
    return document.querySelector('input[name="method"]:checked')?.value || 'qrph';
}

function selectedMethodLabel() {
    return {
        qrph: 'QR Ph'
    }[selectedMethod()] || 'secure checkout';
}

function selectedLoanOption() {
    return loanSelect.options[loanSelect.selectedIndex];
}

function syncSelectedLoan() {
    const selectedLoan = selectedLoanOption();
    const hasLoan = Boolean(selectedLoan?.dataset.balance);

    accountPreview.hidden = !hasLoan;
    loanCards.forEach((card) => {
        const selected = card.dataset.loanChoice === loanSelect.value;
        card.classList.toggle('active', selected);
        card.setAttribute('aria-pressed', String(selected));
    });

    if (!hasLoan) return;

    accountPreviewCode.textContent = selectedLoan.dataset.code;
    accountPreviewDue.textContent = selectedLoan.dataset.due;
    accountPreviewInstallment.textContent = peso(selectedLoan.dataset.installment);
    accountPreviewBalance.textContent = peso(selectedLoan.dataset.balance);
}

function syncPaymentAmount(resetAmount = false) {
    const selectedLoan = selectedLoanOption();
    const rawBalance = selectedLoan?.dataset.balance;

    if (!rawBalance) {
        amountInput.removeAttribute('max');
        fullBalanceButton.disabled = true;
        amountHelp.textContent = 'Select a loan to load its current outstanding balance.';
        syncPaymentReview();
        return;
    }

    const balance = Number(rawBalance);
    const maximum = selectedMethod() === 'cash' ? balance : Math.min(balance, 100000);
    amountInput.max = maximum.toFixed(2);
    fullBalanceButton.disabled = false;
    fullBalanceButton.textContent = maximum < balance ? 'Online maximum' : 'Full balance';

    if (resetAmount || !amountInput.value) {
        amountInput.value = Math.min(Number(selectedLoan.dataset.installment), maximum).toFixed(2);
    } else if (amountInput.value && Number(amountInput.value) > maximum) {
        amountInput.value = maximum.toFixed(2);
    }

    amountHelp.textContent = `Next scheduled payment: ${peso(selectedLoan.dataset.installment)} · Outstanding balance: ${peso(balance)}${maximum < balance ? ' · Online limit: ₱100,000.00 per transaction' : ''}`;
    syncPaymentReview();
}

function syncPaymentReview() {
    const option = selectedLoanOption();
    reviewAmount.textContent = peso(amountInput.value || 0);
    reviewAccount.textContent = option?.dataset.code || 'Select a loan account';
    reviewMethod.textContent = selectedMethod() === 'cash' ? 'Cash' : 'QR Ph';
    paymentGuidance.textContent = selectedMethod() === 'cash'
        ? 'Only submit a cash payment you have already made. Upload the receipt; your balance updates after administrator confirmation.'
        : 'QR Ph continues to secure PayMongo checkout in this tab, then returns here automatically.';
}
amountInput.addEventListener('input', syncPaymentReview);

function syncPaymentMethod() {
    const method = selectedMethod();
    const isCash = method === 'cash';
    proofSection.style.display = isCash ? 'block' : 'none';
    proofInput.required = isCash;
    submitButton.querySelector('span').textContent = isCash
        ? 'Submit cash payment'
        : `Continue with ${selectedMethodLabel()}`;
    syncPaymentAmount();
}

loanSelect.addEventListener('change', () => {
    syncSelectedLoan();
    syncPaymentAmount(true);
});
methodInputs.forEach((input) => input.addEventListener('change', syncPaymentMethod));
loanCards.forEach((card) => card.addEventListener('click', () => {
    loanSelect.value = card.dataset.loanChoice;
    loanSelect.dispatchEvent(new Event('change'));
    document.getElementById('make-payment').scrollIntoView({ block: 'start', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
    loanSelect.focus({ preventScroll: true });
}));
fullBalanceButton.addEventListener('click', () => {
    if (!amountInput.max) return;
    amountInput.value = Number(amountInput.max).toFixed(2);
    syncPaymentReview();
    amountInput.focus();
});
proofInput.addEventListener('change', () => {
    proofFileName.textContent = proofInput.files[0]?.name || 'Choose a JPG or PNG image up to 2 MB';
});
paymentProgress.addEventListener('cancel', (event) => event.preventDefault());

paymentForm.addEventListener('submit', async function (event) {
    paymentError.hidden = true;
    const isCash = selectedMethod() === 'cash';

    if (isCash) {
        submitButton.disabled = true;
        submitButton.querySelector('span').textContent = 'Submitting payment…';
        return;
    }

    event.preventDefault();
    if (!paymentForm.reportValidity()) return;

    submitButton.disabled = true;
    submitButton.querySelector('span').textContent = 'Opening secure checkout…';
    paymentProgressState.textContent = 'Creating a secure checkout session…';
    paymentProgress.showModal();

    try {
        const response = await fetch(paymentForm.action, {
            method: 'POST',
            body: new FormData(paymentForm),
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        if (!response.ok) {
            const firstError = Object.values(result.errors ?? {}).flat()[0];
            throw new Error(firstError || result.message || 'Unable to start the secure payment.');
        }

        paymentProgressState.textContent = 'Secure checkout is ready. Redirecting in this tab…';
        window.location.assign(result.checkout_url);
    } catch (error) {
        paymentProgress.close();
        submitButton.disabled = false;
        submitButton.querySelector('span').textContent = `Continue with ${selectedMethodLabel()}`;
        paymentError.textContent = error.message || 'Unable to start the secure payment. Please try again.';
        paymentError.hidden = false;
    }
});

if (loanSelect.options.length === 2 && !loanSelect.value) {
    loanSelect.selectedIndex = 1;
}
syncSelectedLoan();
syncPaymentMethod();
</script>
@endif
</body>
</html>
