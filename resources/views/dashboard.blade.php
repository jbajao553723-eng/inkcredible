<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.client-portal-styles')
@include('partials.motion-styles')

.overview-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(320px, .65fr); gap: 22px; margin-bottom: 22px; }
.balance-card { position: relative; min-height: 225px; padding: 28px; overflow: hidden; color: #fff; background: linear-gradient(135deg, #312e81, #4f46e5 58%, #7c3aed); border-radius: 16px; box-shadow: 0 18px 35px rgba(79, 70, 229, .2); }
.balance-card::after { position: absolute; right: -50px; bottom: -90px; width: 230px; height: 230px; pointer-events: none; border: 42px solid rgba(255, 255, 255, .07); border-radius: 50%; content: ''; }
.balance-card > * { position: relative; z-index: 1; }
.balance-label { color: #c7d2fe; font-size: 13px; font-weight: 500; }
.balance-value { margin-top: 10px; overflow-wrap: anywhere; font-size: clamp(28px, 4vw, 38px); font-weight: 700; letter-spacing: -.04em; }
.balance-meta { display: flex; gap: 36px; margin-top: 32px; }
.balance-meta-label { color: #c7d2fe; font-size: 11px; }
.balance-meta-value { margin-top: 5px; font-size: 14px; font-weight: 600; }
.balance-actions { position: relative; z-index: 1; display: flex; gap: 10px; margin-top: 24px; }
.balance-actions .button { border-color: rgba(255, 255, 255, .22); }
.button-white { color: #3730a3; background: #fff; }
.button-glass { color: #fff; background: rgba(255, 255, 255, .1); }
.button-glass:hover { background: rgba(255, 255, 255, .18); }
.next-card { padding: 24px; }
.next-icon { display: grid; place-items: center; width: 44px; height: 44px; color: var(--primary); background: #eef2ff; border-radius: 12px; }
.next-icon svg { width: 22px; height: 22px; }
.next-label { margin-top: 22px; color: var(--muted); font-size: 13px; }
.next-date { margin-top: 7px; font-size: 24px; font-weight: 700; letter-spacing: -.03em; }
.next-note { margin-top: 7px; color: var(--muted); font-size: 12px; line-height: 1.5; }
.section-stack { display: grid; gap: 22px; }
.progress-track { width: 150px; height: 7px; overflow: hidden; background: #eaecf0; border-radius: 999px; }
.progress-bar { height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: inherit; }
.progress-value { margin-top: 6px; color: var(--muted); font-size: 11px; }
.panel-link { color: var(--primary); font-size: 12px; font-weight: 600; text-decoration: none; }
.panel-link:hover { color: var(--primary-dark); }

@media (max-width: 980px) { .overview-grid { grid-template-columns: 1fr; } }
@media (max-width: 560px) {
    .balance-value { font-size: 31px; }
    .balance-meta { gap: 22px; flex-wrap: wrap; }
    .balance-actions { align-items: stretch; flex-direction: column; }
}
</style>
@vite('resources/js/app.js')
</head>

<body>
@php
    $loans = $loans ?? collect();
    $pendingPayments = $pendingPayments ?? collect();
    $totalBorrowed = $loans->whereIn('status', ['approved', 'paid'])->sum('amount');
    $totalPaid = $loans->sum('paid_amount');
    $totalRemaining = $loans->where('status', 'approved')->sum(fn ($loan) => $loan->getRemainingBalance());
    $approvedLoans = $loans->where('status', 'approved');
    $nextSchedule = $approvedLoans
        ->flatMap(fn ($loan) => $loan->paymentSchedules->where('status', '!=', 'paid'))
        ->sortBy('due_date')
        ->first();
    $recentPayments = $loans
        ->flatMap(fn ($loan) => $loan->payments->map(function ($payment) use ($loan) {
            $payment->setRelation('loan', $loan);
            return $payment;
        }))
        ->sortByDesc(fn ($payment) => $payment->paid_at ?? $payment->created_at)
        ->take(5);
    $firstName = explode(' ', trim(auth()->user()->name))[0] ?: 'there';
@endphp

@include('partials.client-sidebar', ['active' => 'dashboard'])

<main class="main">
    <div class="page-shell">
        <header class="topbar">
            <div>
                <div class="eyebrow">Account overview</div>
                <h1>Welcome back, {{ $firstName }}</h1>
                <p class="subtitle">Here is a clear view of your loans, balances, and recent activity.</p>
            </div>
            <div class="top-actions">
                <a class="button button-primary" href="{{ route('loan.create') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6"/></svg>
                    Request loan
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if(! auth()->user()->isClientVerified())
            <div class="verification-banner">
                <div><strong>Account verification required</strong>Complete your employment, income, ID, and selfie verification before requesting a loan.</div>
                <a href="{{ route('profile.verification.edit') }}">Complete verification</a>
            </div>
        @endif

        <section class="stats-grid" aria-label="Account summary">
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Total loans</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6"/></svg></span></div>
                <div class="stat-value">{{ $loans->count() }}</div>
                <div class="stat-note">All submitted applications</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Total borrowed</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M9 9.5c.5-1 1.5-1.5 3-1.5 2 0 3 1 3 2s-1 2-3 2-3 1-3 2 1 2 3 2c1.5 0 2.5-.5 3-1.5M12 6v12"/></svg></span></div>
                <div class="stat-value">₱{{ number_format($totalBorrowed, 2) }}</div>
                <div class="stat-note">Approved principal amount</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Amount paid</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span></div>
                <div class="stat-value">₱{{ number_format($totalPaid, 2) }}</div>
                <div class="stat-note">Confirmed across all loans</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Pending payments</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v4l2.5 1.5"/></svg></span></div>
                <div class="stat-value">{{ $pendingPayments->count() }}</div>
                <div class="stat-note">Awaiting confirmation</div>
            </article>
        </section>

        <section class="overview-grid">
            <article class="balance-card">
                <div class="balance-label">Total outstanding balance</div>
                <div class="balance-value">₱{{ number_format($totalRemaining, 2) }}</div>
                <div class="balance-meta">
                    <div><div class="balance-meta-label">Active loans</div><div class="balance-meta-value">{{ $approvedLoans->count() }}</div></div>
                    <div><div class="balance-meta-label">Repayment status</div><div class="balance-meta-value">{{ $totalRemaining > 0 ? 'Payment active' : 'No balance due' }}</div></div>
                </div>
                <div class="balance-actions">
                    <a class="button button-white" href="{{ route('payments.index') }}">Make a payment</a>
                    <a class="button button-glass" href="{{ route('loan.create') }}">New loan request</a>
                </div>
            </article>

            <article class="panel next-card">
                <div class="next-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 3v4m8-4v4M4 10h16"/></svg></div>
                <div class="next-label">Next scheduled payment</div>
                <div class="next-date">{{ $nextSchedule?->due_date ? \Carbon\Carbon::parse($nextSchedule->due_date)->format('M d, Y') : 'No payment due' }}</div>
                <div class="next-note">{{ $nextSchedule ? 'Stay on schedule to keep your account in good standing.' : 'Your next due date will appear after a repayment schedule is created.' }}</div>
            </article>
        </section>

        <div class="section-stack">
            <section class="panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">My loans</h2><p class="panel-description">Balances and repayment progress for every application.</p></div>
                    <a class="panel-link" href="{{ route('loan.create') }}">Request a loan →</a>
                </div>
                @if($loans->isEmpty())
                    <div class="empty-state"><strong>No loans yet</strong>Start a loan request when you are ready.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Loan</th><th>Principal</th><th>Total payable</th><th>Remaining</th><th>Progress</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($loans as $loan)
                                    @php
                                        $total = $loan->getTotalWithPenalty();
                                        $paid = (float) ($loan->paid_amount ?? 0);
                                        $remaining = $loan->getRemainingBalance();
                                        $progress = $total > 0 ? min(100, ($paid / $total) * 100) : 0;
                                        $loanStatusClass = match ($loan->status) {
                                            'approved' => 'badge-success',
                                            'pending' => 'badge-warning',
                                            'rejected' => 'badge-danger',
                                            'paid' => 'badge-purple',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></td>
                                        <td class="amount">₱{{ number_format($loan->amount, 2) }}</td>
                                        <td>₱{{ number_format($total, 2) }}</td>
                                        <td>{{ $remaining <= 0 ? 'Fully paid' : '₱'.number_format($remaining, 2) }}</td>
                                        <td><div class="progress-track"><div class="progress-bar" style="width: {{ $progress }}%"></div></div><div class="progress-value">{{ number_format($progress) }}% paid</div></td>
                                        <td><span class="badge {{ $loanStatusClass }}">{{ ucfirst($loan->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">Recent payments</h2><p class="panel-description">Your five latest payment transactions in Philippine Time.</p></div>
                    <a class="panel-link" href="{{ route('payments.index') }}">View all payments →</a>
                </div>
                @if($recentPayments->isEmpty())
                    <div class="empty-state"><strong>No payments yet</strong>Your recent transactions will appear here.</div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead><tr><th>Reference</th><th>Loan</th><th>Date &amp; time</th><th>Method</th><th>Status</th><th>Amount</th></tr></thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                    @php
                                        $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                                        $paymentStatusClass = match ($payment->status) {
                                            'paid', 'approved' => 'badge-success',
                                            'pending' => 'badge-warning',
                                            'failed', 'rejected' => 'badge-danger',
                                            'refunded' => 'badge-purple',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td><div class="cell-title">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}</div></td>
                                        <td>{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</td>
                                        <td><div>{{ $paymentTime?->format('M d, Y') ?? '—' }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') ?? '' }} PHT</div></td>
                                        <td>{{ $payment->method === 'gcash' ? 'GCash / QR Ph' : ucfirst($payment->method) }}</td>
                                        <td><span class="badge {{ $paymentStatusClass }}">{{ in_array($payment->status, ['paid', 'approved'], true) ? 'Paid' : ucfirst($payment->status) }}</span></td>
                                        <td class="amount">₱{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
</main>
</body>
</html>
