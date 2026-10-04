<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
.topbar { position:relative; z-index:100; overflow:visible; }
.top-actions { position:relative; z-index:101; overflow:visible; }
.stats-grid,.overview-grid,.section-stack { position:relative; z-index:1; }
.stat-card { position: relative; overflow: hidden; }
.stat-card::after { position: absolute; top: -25px; right: -25px; width: 74px; height: 74px; background: #f5f3ff; border-radius: 50%; content: ''; opacity: .7; }
.stat-card .stat-head, .stat-card .stat-value, .stat-card .stat-note { position: relative; z-index: 1; }
.notification-menu { position:relative; z-index:102; flex:0 0 auto; }
.notification-menu[open] { z-index:103; }
.notification-trigger { position:relative; display:grid; place-items:center; width:42px; height:42px; padding:0; color:#344054; background:#fff; border:1px solid var(--border); border-radius:11px; box-shadow:0 1px 2px rgba(16,24,40,.04); cursor:pointer; list-style:none; transition:.15s ease; }
.notification-trigger::-webkit-details-marker { display:none; }.notification-trigger:hover,.notification-menu[open] .notification-trigger { color:var(--primary); background:#f5f3ff; border-color:#c7d2fe; }
.notification-trigger svg { width:19px; height:19px; }.notification-badge { position:absolute; top:-5px; right:-5px; display:grid; place-items:center; min-width:19px; height:19px; padding:0 5px; color:#fff; background:#d92d20; border:2px solid #fff; border-radius:999px; font-size:9px; font-weight:700; }
.notification-panel { position:absolute; top:calc(100% + 10px); right:0; z-index:104; width:min(380px,calc(100vw - 32px)); overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:0 18px 45px rgba(16,24,40,.18); }
.notification-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:15px 16px; border-bottom:1px solid #eaecf0; }.notification-head strong { font-size:13px; }.notification-head span { color:#667085; font-size:10px; }
.notification-items { max-height:370px; overflow-y:auto; }.notification-item-form + .notification-item-form { border-top:1px solid #f2f4f7; }
.notification-item-button { display:flex; align-items:flex-start; gap:11px; width:100%; padding:14px 16px; color:inherit; background:#fff; border:0; text-align:left; cursor:pointer; transition:background-color .15s; }.notification-item-button:hover { background:#f9fafb; }
.notification-dot { width:8px; height:8px; margin-top:5px; flex:0 0 8px; background:#6366f1; border-radius:50%; }.notification-dot.warning { background:#f79009; }.notification-dot.success { background:#12b76a; }
.notification-copy { min-width:0; flex:1; color:#475467; font-size:11px; line-height:1.5; overflow-wrap:anywhere; }.notification-copy strong { display:block; margin-bottom:2px; color:#101828; font-size:12px; }.notification-time { display:block; margin-top:5px; color:#98a2b3; font-size:9px; }
.notification-arrow { flex:0 0 auto; color:#98a2b3; font-size:15px; }.notification-empty { padding:30px 18px; color:#667085; font-size:11px; line-height:1.5; text-align:center; }.notification-empty svg { display:block; width:28px; height:28px; margin:0 auto 9px; color:#98a2b3; }
.notification-item-button.is-read .notification-dot { background:#d0d5dd; }.notification-item-button.is-read .notification-copy { color:#667085; }
.notification-modal { width:min(500px,calc(100vw - 32px)); padding:0; color:#101828; background:#fff; border:0; border-radius:18px; box-shadow:0 24px 70px rgba(16,24,40,.28); }
.notification-modal::backdrop { background:rgba(16,24,40,.58); backdrop-filter:blur(3px); }
.notification-modal-card { padding:24px; }.notification-modal-head { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; }
.notification-modal-icon { display:grid; place-items:center; width:44px; height:44px; flex:0 0 44px; color:#4f46e5; background:#eef2ff; border-radius:13px; }.notification-modal-icon svg { width:22px; height:22px; }
.notification-modal-icon.warning { color:#b54708; background:#fffaeb; }.notification-modal-icon.success { color:#067647; background:#ecfdf3; }
.notification-modal-title-wrap { min-width:0; flex:1; }.notification-modal-kicker { color:#667085; font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }.notification-modal-title { margin:5px 0 0; font-size:18px; line-height:1.35; }
.notification-modal-close { display:grid; place-items:center; width:34px; height:34px; padding:0; color:#667085; background:#f9fafb; border:1px solid #eaecf0; border-radius:9px; cursor:pointer; }.notification-modal-close:hover { color:#101828; background:#f2f4f7; }
.notification-modal-message { margin:20px 0 0; color:#475467; font-size:13px; line-height:1.7; }.notification-modal-time { margin-top:18px; padding-top:16px; color:#98a2b3; border-top:1px solid #eaecf0; font-size:10px; }
.notification-modal-actions { display:flex; justify-content:flex-end; margin-top:22px; }.notification-modal-actions .button { min-width:100px; }
.contract-link { display:inline-block; margin-top:7px; color:var(--primary); font-size:10px; font-weight:700; text-decoration:none; }
.readiness-panel { display:grid; grid-template-columns:190px minmax(0,1fr); gap:24px; align-items:center; margin-bottom:22px; overflow:hidden; border-color:#d9d6fe; box-shadow:0 10px 28px rgba(79,70,229,.07); }
.readiness-score { display:grid; justify-items:center; padding:24px; background:linear-gradient(145deg,#312e81,#4f46e5); color:#fff; align-self:stretch; align-content:center; }
.score-ring { display:grid; place-items:center; width:106px; height:106px; background:conic-gradient(#a5f3fc calc(var(--score) * 1%),rgba(255,255,255,.16) 0); border-radius:50%; }
.score-ring-inner { display:grid; place-items:center; width:82px; height:82px; background:#3730a3; border-radius:50%; }
.score-ring strong { font-size:27px; line-height:1; }.score-ring span { margin-top:3px; color:#c7d2fe; font-size:9px; }
.readiness-score > strong { margin-top:11px; font-size:12px; }.readiness-score > span { margin-top:4px; color:#c7d2fe; font-size:9px; }
.readiness-content { padding:22px 22px 22px 0; }
.readiness-title { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }.readiness-title h2 { margin:0; font-size:16px; }.readiness-title p { margin:6px 0 0; color:#667085; font-size:10px; line-height:1.5; }
.readiness-factors { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:9px; margin-top:16px; }
.readiness-factor { padding:11px; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; }.readiness-factor strong { display:block; color:#344054; font-size:10px; }.readiness-factor span { display:block; margin-top:4px; color:#667085; font-size:9px; line-height:1.4; }.readiness-factor.positive { background:#f6fef9; border-color:#abefc6; }.readiness-factor.negative { background:#fef3f2; border-color:#fecdca; }
.readiness-actions { display:flex; gap:10px; margin-top:15px; }.readiness-actions .button { min-height:36px; }
.dashboard-section-head { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin:28px 0 12px; }
.dashboard-section-head:first-of-type { margin-top:0; }
.dashboard-section-head h2 { margin:0; font-size:15px; letter-spacing:-.015em; }
.dashboard-section-head p { margin:5px 0 0; color:var(--muted); font-size:11px; }
.dashboard-section-tag { padding:5px 9px; color:#4338ca; background:#eef2ff; border:1px solid #d9d6fe; border-radius:999px; font-size:9px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }

@media (max-width: 980px) { .overview-grid { grid-template-columns: 1fr; } }
@media (max-width:900px){.readiness-panel{grid-template-columns:1fr}.readiness-score{padding:20px}.readiness-content{padding:0 20px 20px}.readiness-factors{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width: 560px) {
    .balance-value { font-size: 31px; }
    .balance-meta { gap: 22px; flex-wrap: wrap; }
    .balance-actions { align-items: stretch; flex-direction: column; }
    .balance-head { align-items: flex-start; }
    .next-card { min-height: 270px; }
    .readiness-factors{grid-template-columns:1fr}.readiness-title,.readiness-actions{align-items:stretch;flex-direction:column}.readiness-actions .button{width:100%}
}
@media (max-width: 760px) {
    .notification-panel { right:auto; left:0; }
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
    $totalPaid = $loans->sum('paid_amount');
    $totalRemaining = $loans->where('status', 'approved')->sum(fn ($loan) => $loan->getRemainingBalance());
    $approvedLoans = $loans->where('status', 'approved');
    $pendingLoanCount = $loans->where('status', 'pending')->count();
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
    $notifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
    $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
    $verificationStatus = auth()->user()->clientVerification?->status ?? 'not submitted';
    $verificationLabel = match ($verificationStatus) {
        'approved' => 'Verified',
        'pending' => 'In review',
        'rejected' => 'Needs update',
        default => 'Incomplete',
    };
@endphp

@include('partials.client-sidebar', ['active' => 'dashboard'])

<main class="main" id="main-content" tabindex="-1">
    <div class="page-shell">
        <header class="topbar">
            <div>
                <div class="eyebrow">Account overview</div>
                <div class="welcome-line"><h1>Welcome back, {{ $firstName }}</h1><span class="today-pill">{{ now()->timezone('Asia/Manila')->format('M d, Y') }}</span></div>
                <p class="subtitle">Here is a clear view of your loans, balances, and recent activity.</p>
            </div>
            <div class="top-actions">
                <a class="button button-secondary" href="{{ route('profile.edit') }}">Account settings</a>
                <details class="notification-menu">
                    <summary class="notification-trigger" aria-label="Open notifications">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M9.8 20h4.4"/></svg>
                        @if($unreadNotificationCount > 0)<span class="notification-badge">{{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}</span>@endif
                    </summary>
                    <div class="notification-panel">
                        <div class="notification-head"><strong>Notifications</strong><span>{{ $unreadNotificationCount }} unread</span></div>
                        @if($notifications->isEmpty())
                            <div class="notification-empty"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M9.8 20h4.4"/></svg>You have no unread notifications.</div>
                        @else
                            <div class="notification-items">
                                @foreach($notifications as $notification)
                                    <div class="notification-item-form"><button class="notification-item-button" type="button" data-notification-open data-read-url="{{ route('notifications.read', $notification->id) }}" data-notification-title="{{ data_get($notification->data, 'title', 'Account update') }}" data-notification-message="{{ data_get($notification->data, 'message') }}" data-notification-time="{{ $notification->created_at->timezone('Asia/Manila')->diffForHumans() }}" data-notification-severity="{{ data_get($notification->data, 'severity', 'info') }}"><span class="notification-dot {{ data_get($notification->data, 'severity', 'info') }}" aria-hidden="true"></span><span class="notification-copy"><strong>{{ data_get($notification->data, 'title', 'Account update') }}</strong>{{ data_get($notification->data, 'message') }}<span class="notification-time">{{ $notification->created_at->timezone('Asia/Manila')->diffForHumans() }}</span></span><span class="notification-arrow" aria-hidden="true">&rsaquo;</span></button></div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </details>
                <a class="button button-primary" href="{{ route('loan.create') }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6"/></svg>
                    Request loan
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if(! auth()->user()->isClientVerified())
            <div class="verification-banner">
                <div><strong>Account verification required</strong>Complete your employment, income evidence, ID, selfie, and digital signature before requesting a loan.</div>
                <a href="{{ route('profile.verification.edit') }}">Complete verification</a>
            </div>
        @endif

        <div class="dashboard-section-head"><div><h2>Financial overview</h2><p>Live account information based on your current loan and payment records.</p></div><span class="dashboard-section-tag">Updated now</span></div>
        <section class="stats-grid" aria-label="Account summary">
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Account verification</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg></span></div>
                <div class="stat-value">{{ $verificationLabel }}</div>
                <div class="stat-note">{{ $verificationStatus === 'approved' ? 'Identity and income confirmed' : 'Required before loan approval' }}</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Active loans</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12.5 9 16l10-10M4 4h16v16H4z"/></svg></span></div>
                <div class="stat-value">{{ $approvedLoans->count() }}</div>
                <div class="stat-note">Currently in repayment</div>
            </article>
            <article class="stat-card">
                <div class="stat-head"><span class="stat-label">Pending requests</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v4l3 2"/></svg></span></div>
                <div class="stat-value">{{ $pendingLoanCount }}</div>
                <div class="stat-note">Awaiting contract or decision</div>
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

        <div class="dashboard-section-head"><div><h2>Credit profile</h2><p>See which verified behaviors improve your approval readiness.</p></div><span class="dashboard-section-tag">Behavior based</span></div>
        <section class="panel readiness-panel" aria-labelledby="readiness-title">
            <div class="readiness-score"><div class="score-ring" style="--score:{{ $riskProfile['readinessScore'] }}"><div class="score-ring-inner"><strong>{{ $riskProfile['readinessScore'] }}</strong><span>out of 100</span></div></div><strong>{{ $riskProfile['level'] }} risk</strong><span>{{ $riskProfile['approvalOutlook'] }} review outlook</span></div>
            <div class="readiness-content"><div class="readiness-title"><div><h2 id="readiness-title">Approval readiness</h2><p>A transparent guide based on affordability, verified income evidence, and repayment behavior. It helps review but never guarantees approval.</p></div><span class="badge {{ $riskProfile['tone'] === 'success' ? 'badge-success' : ($riskProfile['tone'] === 'warning' ? 'badge-warning' : 'badge-danger') }}">Live assessment</span></div>
                <div class="readiness-factors">
                    <div class="readiness-factor {{ $riskProfile['verifiedPayslip'] ? 'positive' : '' }}"><strong>Income evidence</strong><span>{{ $riskProfile['verifiedPayslip'] ? 'Verified payslip on file' : 'Add a payslip for review' }}</span></div>
                    <div class="readiness-factor {{ $riskProfile['earlyPayments'] > 0 ? 'positive' : '' }}"><strong>Early payments</strong><span>{{ $riskProfile['earlyPayments'] }} installment(s) paid early</span></div>
                    <div class="readiness-factor {{ $riskProfile['onTimePayments'] > 0 ? 'positive' : '' }}"><strong>On-time record</strong><span>{{ $riskProfile['onTimePayments'] }} installment(s) on time</span></div>
                    <div class="readiness-factor {{ $riskProfile['latePayments'] > 0 ? 'negative' : 'positive' }}"><strong>Late / overdue</strong><span>{{ $riskProfile['latePayments'] }} recorded issue(s)</span></div>
                </div>
                <div class="readiness-actions"><a class="button button-primary" href="{{ route('profile.verification.edit') }}">Manage income evidence</a><a class="button button-secondary" href="{{ route('profile.edit') }}">Update dashboard profile</a></div>
            </div>
        </section>

        <div class="dashboard-section-head"><div><h2>Account activity</h2><p>Track applications and confirmed payments without leaving your dashboard.</p></div><span class="dashboard-section-tag">Recent records</span></div>
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
                                        <td data-label="Status"><span class="badge {{ $loanStatusClass }}">{{ ucfirst($loan->status) }}</span>@if($loan->status === 'pending' && $loan->contract_sent_at)<a class="contract-link" href="{{ route('loan.contract.show', $loan) }}">{{ $loan->signed_contract_path ? 'View submitted contract' : 'Review and sign contract' }} &rarr;</a>@endif @if($loan->status === 'rejected' && $loan->rejection_reason)<div class="rejection-reason"><strong>Reason:</strong> {{ $loan->rejection_reason }}</div>@endif</td>
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
<dialog class="notification-modal" id="notification-detail-modal" aria-labelledby="notification-modal-title">
    <div class="notification-modal-card">
        <div class="notification-modal-head">
            <div class="notification-modal-icon" data-notification-modal-icon><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M9.8 20h4.4"/></svg></div>
            <div class="notification-modal-title-wrap"><div class="notification-modal-kicker">Notification</div><h2 class="notification-modal-title" id="notification-modal-title" data-notification-modal-title>Account update</h2></div>
            <button class="notification-modal-close" type="button" aria-label="Close notification" data-notification-close>&times;</button>
        </div>
        <p class="notification-modal-message" data-notification-modal-message></p>
        <div class="notification-modal-time" data-notification-modal-time></div>
        <div class="notification-modal-actions"><button class="button button-primary" type="button" data-notification-close>Done</button></div>
    </div>
</dialog>
<script>
(() => {
    const modal = document.getElementById('notification-detail-modal');
    if (!(modal instanceof HTMLDialogElement)) return;

    const title = modal.querySelector('[data-notification-modal-title]');
    const message = modal.querySelector('[data-notification-modal-message]');
    const time = modal.querySelector('[data-notification-modal-time]');
    const icon = modal.querySelector('[data-notification-modal-icon]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const unreadLabel = document.querySelector('.notification-head span');
    const badge = document.querySelector('.notification-badge');

    document.querySelectorAll('[data-notification-open]').forEach((button) => {
        button.addEventListener('click', async () => {
            title.textContent = button.dataset.notificationTitle || 'Account update';
            message.textContent = button.dataset.notificationMessage || '';
            time.textContent = button.dataset.notificationTime || '';
            icon.className = `notification-modal-icon ${button.dataset.notificationSeverity || 'info'}`;
            button.closest('.notification-menu')?.removeAttribute('open');
            modal.showModal();

            if (button.classList.contains('is-read')) return;

            try {
                const response = await fetch(button.dataset.readUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: '{}',
                });
                if (!response.ok) return;
                const result = await response.json();
                button.classList.add('is-read');
                if (unreadLabel) unreadLabel.textContent = `${result.unread_count} unread`;
                if (badge) {
                    if (result.unread_count === 0) badge.remove();
                    else badge.textContent = result.unread_count > 99 ? '99+' : result.unread_count;
                }
            } catch (_) {
                // The modal remains usable even if marking the item as read fails.
            }
        });
    });

    modal.querySelectorAll('[data-notification-close]').forEach((button) => {
        button.addEventListener('click', () => modal.close());
    });
    modal.addEventListener('click', (event) => {
        if (event.target === modal) modal.close();
    });
})();
</script>
</body>
</html>
