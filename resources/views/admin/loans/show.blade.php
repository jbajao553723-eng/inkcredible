<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Details | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@php
    $statusClass = match ($loan->status) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'paid' => 'badge-purple', 'rejected' => 'badge-danger', default => 'badge-neutral' };
    $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
    $successfulPayments = $loan->payments->whereIn('status', ['paid', 'approved']);
@endphp
@include('partials.admin-sidebar', ['active' => 'loans'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Loan review</div><h1>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</h1><p class="subtitle">Complete application, repayment, and client information.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.loans') }}">Back to loans</a><span class="badge {{ $statusClass }}">{{ ucfirst($loan->status) }}</span></div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Principal</div><div class="stat-value">₱{{ number_format($loan->amount, 2) }}</div><div class="stat-note">Requested amount</div></article>
        <article class="stat-card"><div class="stat-label">Total payable</div><div class="stat-value">₱{{ number_format($loan->getTotalWithPenalty(), 2) }}</div><div class="stat-note">Including current penalties</div></article>
        <article class="stat-card"><div class="stat-label">Amount paid</div><div class="stat-value">₱{{ number_format($successfulPayments->sum('amount'), 2) }}</div><div class="stat-note">{{ $successfulPayments->count() }} confirmed payments</div></article>
        <article class="stat-card"><div class="stat-label">Remaining</div><div class="stat-value">₱{{ number_format($loan->getRemainingBalance(), 2) }}</div><div class="stat-note">Current outstanding balance</div></article>
    </section>

    <div class="detail-layout">
        <div>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Application details</h2><p class="panel-description">Loan and borrower information.</p></div></div>
                <div class="panel-body detail-grid">
                    <div class="detail-item"><div class="detail-label">Client</div><div class="detail-value">{{ $loan->user?->name ?? 'Unknown client' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value">{{ $loan->user?->email ?? 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Contact number</div><div class="detail-value">{{ $loan->user?->contact_number ?: 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Address</div><div class="detail-value">{{ $loan->user?->address ?: 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Loan product</div><div class="detail-value">{{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Interest rate</div><div class="detail-value">{{ number_format((float) ($loan->loanType?->interest_rate ?? 0), 2) }}%</div></div>
                    <div class="detail-item"><div class="detail-label">Submitted</div><div class="detail-value">{{ $submitted?->format('M d, Y · h:i A') }} PHT</div></div>
                    <div class="detail-item"><div class="detail-label">Approved</div><div class="detail-value">{{ $loan->approved_at ? $loan->approved_at->copy()->timezone('Asia/Manila')->format('M d, Y · h:i A').' PHT' : 'Not approved' }}</div></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Repayment schedule</h2><p class="panel-description">Installments and due dates for this loan.</p></div></div>
                @if($loan->paymentSchedules->isEmpty())<div class="empty-state"><strong>No repayment schedule</strong>A schedule is created when the loan is approved.</div>
                @else<div class="table-wrap"><table><thead><tr><th>Installment</th><th>Due date</th><th>Scheduled</th><th>Paid</th><th>Penalty</th><th>Status</th></tr></thead><tbody>
                    @foreach($loan->paymentSchedules as $schedule)
                        @php $scheduleClass = match ($schedule->status) { 'paid' => 'badge-success', 'overdue' => 'badge-danger', 'partial' => 'badge-warning', default => 'badge-neutral' }; @endphp
                        <tr><td>#{{ $schedule->installment_number }}</td><td>{{ $schedule->due_date?->format('M d, Y') }}</td><td class="amount">₱{{ number_format($schedule->scheduled_amount, 2) }}</td><td>₱{{ number_format($schedule->paid_amount, 2) }}</td><td>₱{{ number_format($schedule->penalty_amount, 2) }}</td><td><span class="badge {{ $scheduleClass }}">{{ ucfirst($schedule->status) }}</span></td></tr>
                    @endforeach
                </tbody></table></div>@endif
            </section>
        </div>

        <aside>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Government ID</h2><p class="panel-description">Identity document submitted by the client.</p></div></div>
                <div class="panel-body">
                    @if($loan->government_id)
                        <div class="detail-value">Document is available for secure review.</div>
                        <a class="button button-primary" style="margin-top:14px" href="{{ asset('storage/'.$loan->government_id) }}" target="_blank" rel="noopener">Open document</a>
                    @else
                        <div class="empty-state" style="padding:22px 0"><strong>No document found</strong>This application has no attached government ID.</div>
                    @endif
                </div>
            </section>

            @if($loan->getOverdueDays() > 0)
                <section class="panel"><div class="panel-body"><div class="notice"><strong>Overdue loan</strong><br>{{ $loan->getOverdueDays() }} days overdue with ₱{{ number_format($loan->penalty_amount, 2) }} in penalties.</div></div></section>
            @endif

            @if($loan->status === 'pending')
                <section class="panel"><div class="panel-header"><div><h2 class="panel-title">Review decision</h2><p class="panel-description">This action updates the client dashboard.</p></div></div><div class="panel-body action-group">
                    <form method="POST" action="{{ route('admin.loan.approve', $loan->id) }}" data-confirm="Approve this loan request?">@csrf<button class="button button-success" type="submit">Approve loan</button></form>
                    <form method="POST" action="{{ route('admin.loan.reject', $loan->id) }}" data-confirm="Reject this loan request?">@csrf<button class="button button-danger" type="submit">Reject loan</button></form>
                </div></section>
            @endif
        </aside>
    </div>
</div></main>
@include('partials.admin-confirmation')
</body></html>
