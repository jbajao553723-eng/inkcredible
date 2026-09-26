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

.main { background: radial-gradient(circle at 92% 2%, rgba(99, 102, 241, .07), transparent 26%), var(--canvas); }
.welcome-line { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.today-pill { padding: 5px 9px; color: #475467; background: #fff; border: 1px solid var(--border); border-radius: 999px; font-size: 10px; font-weight: 600; }
.overview-grid { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(330px, .7fr); gap: 20px; margin-bottom: 22px; align-items: stretch; }
.balance-card { position: relative; min-height: 285px; padding: 28px; overflow: hidden; color: #fff; background: linear-gradient(135deg, #27256f, #4f46e5 58%, #7c3aed); border-radius: 18px; box-shadow: 0 18px 38px rgba(79, 70, 229, .2); }
.balance-card::after { position: absolute; right: -50px; bottom: -90px; width: 230px; height: 230px; pointer-events: none; border: 42px solid rgba(255, 255, 255, .07); border-radius: 50%; content: ''; }
.balance-card::before { position: absolute; top: -100px; right: 18%; width: 190px; height: 190px; pointer-events: none; background: rgba(255, 255, 255, .04); border-radius: 50%; content: ''; }
.balance-card > * { position: relative; z-index: 1; }
.balance-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.balance-label { color: #c7d2fe; font-size: 13px; font-weight: 500; }
.balance-chip { padding: 6px 9px; color: #fff; background: rgba(255, 255, 255, .12); border: 1px solid rgba(255, 255, 255, .16); border-radius: 999px; font-size: 10px; font-weight: 600; }
.balance-value { margin-top: 10px; overflow-wrap: anywhere; font-size: clamp(28px, 4vw, 38px); font-weight: 700; letter-spacing: -.04em; }
.balance-progress { margin-top: 22px; }
.balance-progress-copy { display: flex; justify-content: space-between; gap: 14px; margin-bottom: 8px; color: #e0e7ff; font-size: 10px; }
.balance-progress-track { height: 7px; overflow: hidden; background: rgba(255, 255, 255, .17); border-radius: 999px; }
.balance-progress-fill { height: 100%; background: #fff; border-radius: inherit; box-shadow: 0 0 16px rgba(255, 255, 255, .38); transition: width 1s cubic-bezier(.22, 1, .36, 1); }
.balance-meta { display: flex; gap: 34px; margin-top: 20px; }
.balance-meta-label { color: #c7d2fe; font-size: 11px; }
.balance-meta-value { margin-top: 5px; font-size: 14px; font-weight: 600; }
.balance-actions { position: relative; z-index: 1; display: flex; gap: 10px; margin-top: 22px; }
.balance-actions .button { border-color: rgba(255, 255, 255, .22); }
.button-white { color: #3730a3; background: #fff; }
.button-glass { color: #fff; background: rgba(255, 255, 255, .1); }
.button-glass:hover { background: rgba(255, 255, 255, .18); }
.next-card { display: flex; min-height: 285px; padding: 24px; flex-direction: column; }
.next-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.next-icon { display: grid; place-items: center; width: 44px; height: 44px; color: var(--primary); background: #eef2ff; border-radius: 12px; }
.next-icon svg { width: 22px; height: 22px; }
.due-pill { padding: 6px 9px; color: #067647; background: #ecfdf3; border-radius: 999px; font-size: 10px; font-weight: 700; white-space: nowrap; }
.due-pill.soon { color: #b54708; background: #fffaeb; }
.due-pill.overdue { color: #b42318; background: #fef3f2; }
.next-label { margin-top: 19px; color: var(--muted); font-size: 12px; }
.next-amount { margin-top: 6px; font-size: 28px; font-weight: 700; letter-spacing: -.035em; }
.next-date { margin-top: 5px; color: #344054; font-size: 13px; font-weight: 600; }
.next-note { margin-top: 7px; color: var(--muted); font-size: 12px; line-height: 1.5; }
.next-action { align-self: flex-start; margin-top: auto; padding-top: 18px; }
.section-stack { display: grid; gap: 22px; }
.progress-track { width: 150px; height: 7px; overflow: hidden; background: #eaecf0; border-radius: 999px; }
.progress-bar { height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: inherit; }
.progress-value { margin-top: 6px; color: var(--muted); font-size: 11px; }
.panel-link { color: var(--primary); font-size: 12px; font-weight: 600; text-decoration: none; }
.panel-link:hover { color: var(--primary-dark); }
.loan-code { display: inline-block; margin-top: 5px; padding: 3px 6px; color: #475467; background: #f2f4f7; border-radius: 5px; font-size: 9px; font-weight: 600; }
.dashboard-table tbody tr { transition: background-color .15s ease; }
.dashboard-table .progress-track { width: min(150px, 100%); }
.stat-card { position: relative; overflow: hidden; }
.stat-card::after { position: absolute; top: -25px; right: -25px; width: 74px; height: 74px; background: #f5f3ff; border-radius: 50%; content: ''; opacity: .7; }
.stat-card .stat-head, .stat-card .stat-value, .stat-card .stat-note { position: relative; z-index: 1; }

@media (max-width: 980px) { .overview-grid { grid-template-columns: 1fr; } }
@media (max-width: 560px) {
    .balance-value { font-size: 31px; }
    .balance-meta { gap: 22px; flex-wrap: wrap; }
    .balance-actions { align-items: stretch; flex-direction: column; }
    .balance-head { align-items: flex-start; }
    .next-card { min-height: 270px; }
}
@media (max-width: 760px) {
    .dashboard-panel { overflow: visible; background: transparent; border: 0; box-shadow: none; }
    .dashboard-panel .panel-header { margin-bottom: 10px; background: #fff; border: 1px solid var(--border); border-radius: 14px; }
    .dashboard-panel .table-wrap { overflow: visible; }
    .dashboard-table, .dashboard-table tbody, .dashboard-table tr, .dashboard-table td { display: block; width: 100%; }
    .dashboard-table thead { display: none; }
    .dashboard-table tbody { display: grid; gap: 11px; }
    .dashboard-table tr { padding: 6px 16px; background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 2px 8px rgba(16, 24, 40, .04); }
    .dashboard-table td { min-width: 0; padding: 11px 0; border-bottom: 1px solid #f2f4f7; }
    .dashboard-table td:last-child { border-bottom: 0; }
    .dashboard-table td::before { display: block; margin-bottom: 7px; color: #98a2b3; content: attr(data-label); font-size: 9px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
    .dashboard-table .progress-track { width: 100%; }
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
    $nextLoan = $nextSchedule ? $approvedLoans->firstWhere('id', $nextSchedule->loan_id) : null;
    $nextAmount = $nextSchedule
        ? max(0, (float) $nextSchedule->scheduled_amount - (float) $nextSchedule->paid_amount)
        : 0;
    $nextDueDate = $nextSchedule?->due_date
        ? \Carbon\Carbon::parse($nextSchedule->due_date)->timezone('Asia/Manila')->startOfDay()
        : null;
    $daysUntilDue = $nextDueDate
        ? (int) now('Asia/Manila')->startOfDay()->diffInDays($nextDueDate, false)
        : null;
    $dueTone = $daysUntilDue !== null && $daysUntilDue < 0 ? 'overdue' : (($daysUntilDue !== null && $daysUntilDue <= 3) ? 'soon' : '');
    $dueLabel = match (true) {
        $daysUntilDue === null => 'No payment due',
        $daysUntilDue < 0 => abs($daysUntilDue).' '.Str::plural('day', abs($daysUntilDue)).' overdue',
        $daysUntilDue === 0 => 'Due today',
        $daysUntilDue === 1 => 'Due tomorrow',
        default => 'Due in '.$daysUntilDue.' days',
    };
    $totalPayable = $loans->whereIn('status', ['approved', 'paid'])->sum(fn ($loan) => $loan->getTotalWithPenalty());
    $overallProgress = $totalPayable > 0 ? min(100, ($totalPaid / $totalPayable) * 100) : 0;
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
                <div class="welcome-line"><h1>Welcome back, {{ $firstName }}</h1><span class="today-pill">{{ now()->timezone('Asia/Manila')->format('M d, Y') }}</span></div>
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
                <div class="stat-value">&#8369;{{ number_format($totalBorrowed, 2) }}</div>
                <div class="stat-note">Approved principal amount</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Amount paid</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span></div>
                <div class="stat-value">&#8369;{{ number_format($totalPaid, 2) }}</div>
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
                <div class="balance-head"><div class="balance-label">Total outstanding balance</div><span class="balance-chip">{{ number_format($overallProgress) }}% repaid</span></div>
                <div class="balance-value">&#8369;{{ number_format($totalRemaining, 2) }}</div>
                <div class="balance-progress">
                    <div class="balance-progress-copy"><span>Overall repayment progress</span><strong>&#8369;{{ number_format($totalPaid, 2) }} paid</strong></div>
                    <div class="balance-progress-track"><div class="balance-progress-fill progress-bar" style="width: {{ $overallProgress }}%"></div></div>
                </div>
                <div class="balance-meta">
                    <div><div class="balance-meta-label">Active loans</div><div class="balance-meta-value">{{ $approvedLoans->count() }}</div></div>
                    <div><div class="balance-meta-label">Total payable</div><div class="balance-meta-value">&#8369;{{ number_format($totalPayable, 2) }}</div></div>
                    <div><div class="balance-meta-label">Status</div><div class="balance-meta-value">{{ $totalRemaining > 0 ? 'Payment active' : 'No balance due' }}</div></div>
                </div>
                <div class="balance-actions">
                    <a class="button button-white" href="{{ route('payments.index') }}">Make a payment</a>
                    <a class="button button-glass" href="{{ route('loan.create') }}">New loan request</a>
                </div>
            </article>

            <article class="panel next-card">
                <div class="next-head"><div class="next-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 3v4m8-4v4M4 10h16"/></svg></div><span class="due-pill {{ $dueTone }}">{{ $dueLabel }}</span></div>
                <div class="next-label">{{ $nextSchedule ? 'Next installment' : 'Next scheduled payment' }}</div>
                <div class="next-amount">@if($nextSchedule)&#8369;{{ number_format($nextAmount, 2) }}@else Nothing due @endif</div>
                <div class="next-date">{{ $nextDueDate?->format('M d, Y') ?? 'You are all caught up' }}</div>
                <div class="next-note">@if($nextSchedule){{ $nextLoan?->loanType?->display_name ?? $nextLoan?->loanType?->name ?? 'Loan' }} &middot; {{ $nextLoan?->loan_code ?: 'Loan #'.$nextSchedule->loan_id }} &middot; Installment {{ $nextSchedule->installment_number }}@else Your next due date will appear after a repayment schedule is created. @endif</div>
                @if($nextSchedule)<a class="panel-link next-action" href="{{ route('payments.index') }}">Pay this installment &rarr;</a>@endif
            </article>
        </section>

        <div class="section-stack">
            <section class="panel dashboard-panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">My loans</h2><p class="panel-description">Balances and repayment progress for every application.</p></div>
                    <a class="panel-link" href="{{ route('loan.create') }}">Request a loan &rarr;</a>
                </div>
                @if($loans->isEmpty())
                    <div class="empty-state"><strong>No loans yet</strong>Start a loan request when you are ready.</div>
                @else
                    <div class="table-wrap">
                        <table class="dashboard-table">
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
                                        <td data-label="Loan"><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><span class="loan-code">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</span>@if($loan->purpose)<div class="cell-secondary">Purpose: {{ $loan->purpose }}</div>@endif</td>
                                        <td class="amount" data-label="Principal">&#8369;{{ number_format($loan->amount, 2) }}</td>
                                        <td data-label="Total payable">&#8369;{{ number_format($total, 2) }}</td>
                                        <td data-label="Remaining">@if($remaining <= 0)Fully paid @else &#8369;{{ number_format($remaining, 2) }} @endif</td>
                                        <td data-label="Repayment progress"><div class="progress-track"><div class="progress-bar" style="width: {{ $progress }}%"></div></div><div class="progress-value">{{ number_format($progress) }}% paid</div></td>
                                        <td data-label="Status"><span class="badge {{ $loanStatusClass }}">{{ ucfirst($loan->status) }}</span>@if($loan->status === 'rejected' && $loan->rejection_reason)<div class="rejection-reason"><strong>Reason:</strong> {{ $loan->rejection_reason }}</div>@endif</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="panel dashboard-panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">Recent payments</h2><p class="panel-description">Your five latest payment transactions in Philippine Time.</p></div>
                    <a class="panel-link" href="{{ route('payments.index') }}">View all payments &rarr;</a>
                </div>
                @if($recentPayments->isEmpty())
                    <div class="empty-state"><strong>No payments yet</strong>Your recent transactions will appear here.</div>
                @else
                    <div class="table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>Reference</th><th>Loan</th><th>Date &amp; time</th><th>Method</th><th>Status</th><th>Amount</th></tr></thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                    @php
                                        $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
                                        $paymentStatusClass = match ($payment->status) {
                                            'approved' => 'badge-success',
                                            'pending' => 'badge-warning',
                                            'rejected' => 'badge-danger',
                                            default => 'badge-neutral',
                                        };
                                    @endphp
                                    <tr>
                                        <td data-label="Reference"><div class="cell-title">{{ $payment->reference ?: 'Pending reference' }}</div><div class="cell-secondary">Transaction #{{ $payment->id }}</div></td>
                                        <td data-label="Loan">{{ $payment->loan?->loanType?->display_name ?? $payment->loan?->loanType?->name ?? 'Loan' }}</td>
                                        <td data-label="Date & time"><div>{{ $paymentTime?->format('M d, Y') ?? '—' }}</div><div class="cell-secondary">{{ $paymentTime?->format('h:i A') ?? '' }} PHT</div></td>
                                        <td data-label="Method">{{ $payment->method_label }}</td>
                                        <td data-label="Status"><span class="badge {{ $paymentStatusClass }}">{{ ucfirst($payment->status) }}</span></td>
                                        <td class="amount" data-label="Amount">&#8369;{{ number_format($payment->amount, 2) }}</td>
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
