<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.admin-styles') @include('partials.motion-styles')</style>
@vite('resources/js/app.js')
</head>
<body>
@php
    $totalForDistribution = max(1, $stats['total_loans']);
    $statusRows = [
        ['label' => 'Pending', 'count' => $stats['pending_loans'], 'color' => '#f59e0b'],
        ['label' => 'Approved', 'count' => $stats['approved_loans'], 'color' => '#079455'],
        ['label' => 'Paid', 'count' => $stats['paid_loans'], 'color' => '#7c3aed'],
        ['label' => 'Rejected', 'count' => $stats['rejected_loans'], 'color' => '#d92d20'],
    ];
@endphp

@include('partials.admin-sidebar', ['active' => 'dashboard'])

<main class="main">
    <div class="page-shell">
        <header class="topbar">
            <div><div class="eyebrow">Administration</div><h1>Operations dashboard</h1><p class="subtitle">Monitor lending activity and review items that need attention.</p></div>
            <div class="top-actions">
                <a class="button button-primary" href="{{ route('admin.loans') }}">Review requests</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form>
            </div>
        </header>

        @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

        <section class="stats-grid" aria-label="Administrative summary">
            <article class="stat-card"><div class="stat-head"><span class="stat-label">Loan requests</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6"/></svg></span></div><div class="stat-value">{{ $stats['total_loans'] }}</div><div class="stat-note">{{ $stats['pending_loans'] }} awaiting review</div></article>
            <article class="stat-card"><div class="stat-head"><span class="stat-label">Total released</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M9 9.5c.5-1 1.5-1.5 3-1.5 2 0 3 1 3 2s-1 2-3 2-3 1-3 2 1 2 3 2M12 6v12"/></svg></span></div><div class="stat-value">₱{{ number_format($stats['total_released'], 2) }}</div><div class="stat-note">Approved and completed principal</div></article>
            <article class="stat-card"><div class="stat-head"><span class="stat-label">Total collected</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/></svg></span></div><div class="stat-value">₱{{ number_format($stats['total_collected'], 2) }}</div><div class="stat-note">Confirmed payment amount</div></article>
            <article class="stat-card"><div class="stat-head"><span class="stat-label">Registered clients</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3.5 19c.5-4 2.3-6 5.5-6s5 2 5.5 6"/></svg></span></div><div class="stat-value">{{ $stats['total_clients'] }}</div><div class="stat-note">Active client accounts</div></article>
        </section>

        <section class="metric-layout">
            <article class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Recent loan requests</h2><p class="panel-description">The latest applications across all clients.</p></div><a class="button button-secondary button-small" href="{{ route('admin.loans') }}">View all</a></div>
                @if($recentLoans->isEmpty())
                    <div class="empty-state"><strong>No loan requests</strong>New applications will appear here.</div>
                @else
                    <div class="table-wrap"><table>
                        <thead><tr><th>Client</th><th>Loan</th><th>Requested</th><th>Submitted</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($recentLoans as $loan)
                            @php
                                $statusClass = match ($loan->status) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'paid' => 'badge-purple', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                                $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
                            @endphp
                            <tr>
                                <td><div class="identity"><span class="avatar">{{ strtoupper(substr($loan->user?->name ?? 'U', 0, 1)) }}</span><span><span class="cell-title">{{ $loan->user?->name ?? 'Unknown client' }}</span><span class="cell-secondary">{{ $loan->user?->email ?? 'No email' }}</span></span></div></td>
                                <td><div class="cell-title">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div><div class="cell-secondary">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div></td>
                                <td class="amount">₱{{ number_format($loan->amount, 2) }}</td>
                                <td><div>{{ $submitted?->format('M d, Y') }}</div><div class="cell-secondary">{{ $submitted?->format('h:i A') }} PHT</div></td>
                                <td><span class="badge {{ $statusClass }}">{{ ucfirst($loan->status) }}</span></td>
                                <td><a class="button button-secondary button-small" href="{{ route('admin.loan.show', $loan->id) }}">Details</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table></div>
                @endif
            </article>

            <aside class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Loan status</h2><p class="panel-description">Distribution across all requests.</p></div></div>
                <div class="panel-body status-list">
                    @foreach($statusRows as $row)
                        <div class="status-row"><span>{{ $row['label'] }}</span><div class="status-track"><div class="status-fill" style="width: {{ ($row['count'] / $totalForDistribution) * 100 }}%; background: {{ $row['color'] }}"></div></div><strong>{{ $row['count'] }}</strong></div>
                    @endforeach
                    @if($overdueLoans->isNotEmpty())<div class="notice"><strong>{{ $overdueLoans->count() }} overdue {{ Str::plural('loan', $overdueLoans->count()) }}</strong><br>Review repayment schedules that require attention.</div>@endif
                </div>
            </aside>
        </section>

        <section class="quick-grid">
            <a class="quick-card" href="{{ route('admin.loans') }}"><span class="quick-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6"/></svg></span><div class="quick-title">Manage loan requests</div><div class="quick-note">{{ $stats['pending_loans'] }} requests currently pending</div></a>
            <a class="quick-card" href="{{ route('admin.payments.index') }}"><span class="quick-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17"/></svg></span><div class="quick-title">Review payments</div><div class="quick-note">{{ $stats['pending_cash_payments'] }} cash payments need review</div></a>
            <a class="quick-card" href="{{ route('admin.verifications.index') }}"><span class="quick-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.7 7.5-7 9.5C7.7 18.5 5 15.5 5 11V6zM9 12l2 2 4-5"/></svg></span><div class="quick-title">Client verifications</div><div class="quick-note">{{ $stats['pending_verifications'] }} submissions need review</div></a>
        </section>
    </div>
</main>
</body>
</html>
