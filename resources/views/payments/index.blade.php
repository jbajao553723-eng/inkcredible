<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --navy: #101828;
    --muted: #667085;
    --border: #e4e7ec;
    --surface: #ffffff;
    --canvas: #f7f8fa;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
}

* { box-sizing: border-box; }
body { margin: 0; color: var(--navy); background: var(--canvas); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
button, input, select { font: inherit; }

.sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    width: 248px;
    padding: 28px 20px;
    color: #fff;
    background: #111827;
    z-index: 10;
}

.brand { display: flex; align-items: center; gap: 12px; margin: 0 8px 36px; font-size: 19px; font-weight: 700; }
.brand-mark { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 11px; background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 8px 20px rgba(99, 102, 241, .3); }
.nav-label { margin: 0 12px 10px; color: #667085; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
.nav-link { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; padding: 11px 12px; color: #98a2b3; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 500; transition: .18s ease; }
.nav-link:hover, .nav-link.active { color: #fff; background: #1f2937; }
.nav-link svg { width: 19px; height: 19px; }

.main { min-height: 100vh; margin-left: 248px; padding: 32px; }
.page-shell { max-width: 1320px; margin: 0 auto; }
.topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
.eyebrow { margin-bottom: 6px; color: var(--primary); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
h1 { margin: 0; font-size: 30px; letter-spacing: -.03em; }
.subtitle { margin: 8px 0 0; color: var(--muted); font-size: 14px; }
.logout-button { padding: 10px 16px; color: #344054; background: #fff; border: 1px solid var(--border); border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.logout-button:hover { background: #f9fafb; }

.alert { margin-bottom: 20px; padding: 14px 16px; border: 1px solid; border-radius: 12px; font-size: 14px; }
.alert-success { color: #05603a; background: #ecfdf3; border-color: #abefc6; }
.alert-error { color: #912018; background: #fef3f2; border-color: #fecdca; }
.alert ul { margin: 0; padding-left: 20px; }

.stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
.stat-card, .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 1px 3px rgba(16, 24, 40, .04); }
.stat-card { padding: 20px; }
.stat-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.stat-label { color: var(--muted); font-size: 13px; font-weight: 500; }
.stat-icon { display: grid; place-items: center; width: 36px; height: 36px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.stat-icon svg { width: 18px; height: 18px; }
.stat-value { margin-top: 15px; font-size: 24px; font-weight: 700; letter-spacing: -.03em; }
.stat-note { margin-top: 5px; color: #98a2b3; font-size: 12px; }

.workspace-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(330px, .75fr); gap: 22px; align-items: start; }
.panel { overflow: hidden; }
.panel-header { padding: 22px 24px 0; }
.panel-title { margin: 0; font-size: 18px; letter-spacing: -.015em; }
.panel-description { margin: 7px 0 0; color: var(--muted); font-size: 13px; }
.panel-body { padding: 22px 24px 24px; }

.loan-list { display: grid; gap: 12px; }
.loan-card { padding: 16px; background: #fafafa; border: 1px solid var(--border); border-radius: 13px; }
.loan-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.loan-name { font-size: 14px; font-weight: 600; }
.loan-code { margin-top: 3px; color: var(--muted); font-size: 12px; }
.loan-balance { margin-top: 14px; font-size: 21px; font-weight: 700; letter-spacing: -.02em; }
.loan-caption { margin-top: 3px; color: var(--muted); font-size: 12px; }

.badge { display: inline-flex; align-items: center; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: capitalize; white-space: nowrap; }
.badge-success { color: #067647; background: #ecfdf3; }
.badge-warning { color: #b54708; background: #fffaeb; }
.badge-danger { color: #b42318; background: #fef3f2; }
.badge-neutral { color: #475467; background: #f2f4f7; }
.badge-purple { color: #6941c6; background: #f4f3ff; }

.form-group { margin-bottom: 18px; }
.form-label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 600; }
.form-control { width: 100%; min-height: 45px; padding: 10px 12px; color: var(--navy); background: #fff; border: 1px solid #d0d5dd; border-radius: 10px; outline: none; transition: border-color .15s, box-shadow .15s; }
.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
.form-help { margin: 7px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }

.method-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.method-option { position: relative; }
.method-option input { position: absolute; opacity: 0; pointer-events: none; }
.method-card { display: flex; align-items: center; gap: 11px; min-height: 64px; padding: 12px; border: 1px solid #d0d5dd; border-radius: 11px; cursor: pointer; transition: .15s ease; }
.method-card:hover { border-color: #a5b4fc; }
.method-option input:checked + .method-card { border-color: var(--primary); background: #f5f3ff; box-shadow: 0 0 0 1px var(--primary); }
.method-logo { display: grid; place-items: center; width: 36px; height: 36px; flex: 0 0 36px; color: #fff; background: var(--primary); border-radius: 9px; font-size: 12px; font-weight: 700; }
.method-logo.cash { color: #067647; background: #d1fadf; }
.method-name { display: block; font-size: 13px; font-weight: 600; }
.method-note { display: block; margin-top: 2px; color: var(--muted); font-size: 11px; }
.proof-section { display: none; }

.submit-button { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 47px; margin-top: 4px; color: #fff; background: var(--primary); border: 0; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 20px rgba(79, 70, 229, .18); transition: .15s ease; }
.submit-button:hover { background: var(--primary-dark); transform: translateY(-1px); }
.submit-button:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.submit-button svg { width: 18px; height: 18px; }

.history-panel { margin-top: 22px; }
.history-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 22px 24px; border-bottom: 1px solid var(--border); }
.history-count { color: var(--muted); font-size: 12px; }
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { padding: 12px 18px; color: var(--muted); background: #fcfcfd; border-bottom: 1px solid var(--border); font-size: 11px; font-weight: 600; letter-spacing: .04em; text-align: left; text-transform: uppercase; white-space: nowrap; }
td { padding: 16px 18px; border-bottom: 1px solid #f2f4f7; font-size: 13px; vertical-align: middle; }
tbody tr:last-child td { border-bottom: 0; }
tbody tr:hover { background: #fcfcfd; }
.transaction-ref { max-width: 190px; overflow: hidden; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
.cell-secondary { margin-top: 4px; color: var(--muted); font-size: 11px; }
.amount { font-weight: 700; white-space: nowrap; }
.empty-state { padding: 48px 24px; color: var(--muted); text-align: center; }
.empty-state strong { display: block; margin-bottom: 7px; color: #344054; }

@media (max-width: 1080px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .workspace-grid { grid-template-columns: 1fr; }
}

@media (max-width: 760px) {
    .sidebar { position: static; width: 100%; height: auto; padding: 16px; }
    .brand { margin: 0 0 14px; }
    .nav-label { display: none; }
    .sidebar nav { display: flex; gap: 6px; overflow-x: auto; }
    .nav-link { flex: 0 0 auto; margin: 0; }
    .main { margin-left: 0; padding: 22px 16px; }
    .topbar { align-items: flex-start; }
    h1 { font-size: 25px; }
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-card { padding: 16px; }
    .stat-value { font-size: 19px; }
    .method-grid { grid-template-columns: 1fr; }
}

@media (max-width: 460px) {
    .stats-grid { grid-template-columns: 1fr; }
    .subtitle { max-width: 230px; }
    .panel-header, .panel-body, .history-header { padding-left: 18px; padding-right: 18px; }
}
</style>
</head>

<body>
@php
    $payments = $payments ?? collect();
    $totalOutstanding = $loans->sum(fn ($loan) => $loan->getRemainingBalance());
    $totalPaid = $payments->whereIn('status', ['paid', 'approved'])->sum('amount');
    $pendingCount = $payments->where('status', 'pending')->count();
@endphp

@include('partials.client-sidebar', ['active' => 'payments'])

<main class="main">
    <div class="page-shell">
        <header class="topbar">
            <div>
                <div class="eyebrow">Loan servicing</div>
                <h1>Payments &amp; billing</h1>
                <p class="subtitle">Pay securely and keep track of every transaction in one place.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit">Log out</button>
            </form>
        </header>

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

        <section class="stats-grid" aria-label="Payment summary">
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
            <article class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Make a payment</h2>
                    <p class="panel-description">Choose a loan and pay through PayMongo or submit a cash payment.</p>
                </div>
                <div class="panel-body">
                    @if($loans->isEmpty())
                        <div class="empty-state"><strong>No balance is currently due</strong>Approved loans with an outstanding balance will appear here.</div>
                    @else
                        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" id="payment-form">
                            @csrf
                            <div class="form-group">
                                <label class="form-label" for="loan-id">Loan account</label>
                                <select name="loan_id" class="form-control" required id="loan-id">
                                    <option value="">Select a loan to pay</option>
                                    @foreach($loans as $loan)
                                        <option value="{{ $loan->id }}" data-balance="{{ number_format($loan->getRemainingBalance(), 2, '.', '') }}" @selected((string) old('loan_id') === (string) $loan->id)>
                                            {{ $loan->loanType->display_name ?? $loan->loanType->name ?? 'Loan' }} &mdash; &#8369;{{ number_format($loan->getRemainingBalance(), 2) }} remaining
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="payment-amount">Payment amount</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" id="payment-amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                <p class="form-help" id="amount-help">Select a loan to load its current outstanding balance.</p>
                            </div>

                            <div class="form-group">
                                <span class="form-label">Payment method</span>
                                <div class="method-grid">
                                    <label class="method-option">
                                        <input type="radio" name="method" value="gcash" @checked(old('method', 'gcash') === 'gcash')>
                                        <span class="method-card"><span class="method-logo">QR</span><span><span class="method-name">GCash / QR Ph</span><span class="method-note">Secure PayMongo checkout</span></span></span>
                                    </label>
                                    <label class="method-option">
                                        <input type="radio" name="method" value="cash" @checked(old('method') === 'cash')>
                                        <span class="method-card"><span class="method-logo cash">&#8369;</span><span><span class="method-name">Cash payment</span><span class="method-note">Requires payment proof</span></span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group proof-section" id="proof-section">
                                <label class="form-label" for="proof">Cash payment proof</label>
                                <input type="file" name="proof" class="form-control" id="proof" accept="image/jpeg,image/png">
                                <p class="form-help">Upload a clear JPG or PNG image, up to 2 MB.</p>
                            </div>

                            <button type="submit" class="submit-button" id="submit-payment">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                                <span>Continue to secure payment</span>
                            </button>
                        </form>
                    @endif
                </div>
            </article>

            <aside class="panel">
                <div class="panel-header"><h2 class="panel-title">Active balances</h2><p class="panel-description">Loans currently available for payment.</p></div>
                <div class="panel-body">
                    <div class="loan-list">
                        @forelse($loans as $loan)
                            <div class="loan-card">
                                <div class="loan-card-top">
                                    <div><div class="loan-name">{{ $loan->loanType->display_name ?? $loan->loanType->name ?? 'Loan' }}</div><div class="loan-code">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></div>
                                    <span class="badge badge-success">{{ $loan->status }}</span>
                                </div>
                                <div class="loan-balance">&#8369;{{ number_format($loan->getRemainingBalance(), 2) }}</div>
                                <div class="loan-caption">Remaining of &#8369;{{ number_format($loan->getTotalWithPenalty(), 2) }}</div>
                            </div>
                        @empty
                            <div class="empty-state"><strong>You're all caught up</strong>There are no active balances to display.</div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </section>

        <section class="panel history-panel">
            <div class="history-header">
                <div><h2 class="panel-title">Payment history</h2><p class="panel-description">Newest transactions appear first. Times are shown in Philippine Time.</p></div>
                <span class="history-count">{{ $payments->count() }} {{ Str::plural('record', $payments->count()) }}</span>
            </div>

            @if($payments->isEmpty())
                <div class="empty-state"><strong>No payment history yet</strong>Your submitted payments will appear here with their date and time.</div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Reference</th><th>Loan</th><th>Date &amp; time</th><th>Method</th><th>Status</th><th>Amount</th></tr></thead>
                        <tbody>
                            @foreach($payments as $payment)
                                @php
                                    $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                                    $statusClass = match ($payment->status) {
                                        'paid', 'approved' => 'badge-success',
                                        'pending' => 'badge-warning',
                                        'failed', 'rejected' => 'badge-danger',
                                        'refunded' => 'badge-purple',
                                        default => 'badge-neutral',
                                    };
                                @endphp
                                <tr>
                                    <td><div class="transaction-ref" title="{{ $payment->reference }}">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}</div></td>
                                    <td><div>{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</div></td>
                                    <td><div>{{ $paymentTime?->format('M d, Y') ?? '—' }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') ?? '' }} PHT · {{ $payment->paid_at ? 'Paid' : 'Submitted' }}</div></td>
                                    <td>{{ $payment->method === 'gcash' ? 'GCash / QR Ph' : ucfirst($payment->method) }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ in_array($payment->status, ['paid', 'approved'], true) ? 'Paid' : ucfirst($payment->status) }}</span></td>
                                    <td class="amount">&#8369;{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</main>

@if($loans->isNotEmpty())
<script>
const paymentForm = document.getElementById('payment-form');
const loanSelect = document.getElementById('loan-id');
const amountInput = document.getElementById('payment-amount');
const amountHelp = document.getElementById('amount-help');
const methodInputs = document.querySelectorAll('input[name="method"]');
const proofSection = document.getElementById('proof-section');
const proofInput = document.getElementById('proof');
const submitButton = document.getElementById('submit-payment');

function selectedMethod() {
    return document.querySelector('input[name="method"]:checked').value;
}

function syncPaymentAmount() {
    const selectedLoan = loanSelect.options[loanSelect.selectedIndex];
    const rawBalance = selectedLoan.dataset.balance;

    if (!rawBalance) {
        amountInput.removeAttribute('max');
        amountHelp.textContent = 'Select a loan to load its current outstanding balance.';
        return;
    }

    const balance = Number(rawBalance);
    const maximum = selectedMethod() === 'cash' ? balance : Math.min(balance, 100000);
    amountInput.max = maximum.toFixed(2);

    if (!amountInput.value || Number(amountInput.value) > maximum) {
        amountInput.value = maximum.toFixed(2);
    }

    amountHelp.textContent = `Outstanding balance: \u20B1${balance.toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}${maximum < balance ? ' · Online limit: \u20B1100,000.00 per transaction' : ''}`;
}

function syncPaymentMethod() {
    const isCash = selectedMethod() === 'cash';
    proofSection.style.display = isCash ? 'block' : 'none';
    proofInput.required = isCash;
    submitButton.querySelector('span').textContent = isCash ? 'Submit cash payment' : 'Continue to secure payment';
    syncPaymentAmount();
}

loanSelect.addEventListener('change', syncPaymentAmount);
methodInputs.forEach((input) => input.addEventListener('change', syncPaymentMethod));
paymentForm.addEventListener('submit', function () {
    submitButton.disabled = true;
    submitButton.querySelector('span').textContent = selectedMethod() === 'cash' ? 'Submitting payment…' : 'Opening PayMongo…';
});

syncPaymentMethod();
syncPaymentAmount();
</script>
@endif
</body>
</html>
