<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Details | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.loan-decision { overflow:hidden; border-color:#dfe3f0; box-shadow:0 8px 24px rgba(16,24,40,.06); }
.loan-decision .panel-header { background:linear-gradient(135deg, #f8fafc 0%, #f5f3ff 100%); border-bottom-color:#e0e7ff; }
.loan-decision-body { display:grid; gap:14px; }
.loan-rejection-form { display:grid; gap:7px; }
.loan-decision-label { display:flex; align-items:center; justify-content:space-between; gap:10px; color:#344054; font-size:11px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
.loan-required { color:#b42318; font-size:10px; font-weight:600; letter-spacing:0; text-transform:none; }
.loan-decision-input { width:100%; min-height:42px; padding:9px 12px; color:#344054; background:#fff; border:1px solid #cfd4dc; border-radius:9px; outline:none; font-size:12px; transition:.15s ease; }
.loan-decision-input::placeholder { color:#98a2b3; }
.loan-decision-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.loan-decision-hint { margin:0; color:#667085; font-size:11px; line-height:1.45; }
.loan-decision-actions { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; padding-top:2px; }
.loan-decision-actions .button { width:100%; min-height:42px; }
.loan-action-reject { color:#b42318; background:#fff5f4; border-color:#fda29b; }
.loan-action-reject:hover { color:#912018; background:#fee4e2; border-color:#f97066; }
.loan-action-approve { color:#fff; background:#067647; border-color:#067647; box-shadow:0 4px 10px rgba(6,118,71,.16); }
.loan-action-approve:hover { color:#fff; background:#05603a; border-color:#05603a; }
</style>
</head>
<body>
@php
    $statusClass = match ($loan->status) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'paid' => 'badge-purple', 'rejected' => 'badge-danger', default => 'badge-neutral' };
    $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
    $successfulPayments = $loan->payments->where('status', 'approved');
@endphp
@include('partials.admin-sidebar', ['active' => 'loans'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Loan review</div><h1>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</h1><p class="subtitle">Complete application, repayment, and client information.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.loans') }}">Back to loans</a><span class="badge {{ $statusClass }}">{{ ucfirst($loan->status) }}</span></div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

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
                    <div class="detail-item"><div class="detail-label">Loan purpose</div><div class="detail-value">{{ $loan->purpose ?: 'Not provided' }}</div></div>
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

            @if($loan->status === 'rejected' && $loan->rejection_reason)
                <section class="panel"><div class="panel-body"><div class="notice"><strong>Rejection reason</strong><br>{{ $loan->rejection_reason }}</div></div></section>
            @endif

            @if($loan->status === 'pending')
                <section class="panel loan-decision">
                    <div class="panel-header"><div><h2 class="panel-title">Review decision</h2><p class="panel-description">Approve the request or explain what the client needs to correct.</p></div></div>
                    <div class="panel-body loan-decision-body">
                        <form id="approve-loan" method="POST" action="{{ route('admin.loan.approve', $loan->id) }}" data-confirm="Approve this loan request?">@csrf</form>
                        <form id="reject-loan" class="loan-rejection-form" method="POST" action="{{ route('admin.loan.reject', $loan->id) }}" data-confirm="Reject this loan request and send the reason to the client?">
                            @csrf
                            <label class="loan-decision-label" for="rejection_reason"><span>Reason for rejection</span><span class="loan-required">Required to reject</span></label>
                            <input class="loan-decision-input" id="rejection_reason" type="text" name="rejection_reason" value="{{ old('rejection_reason') }}" maxlength="500" placeholder="Explain why this request cannot be approved" required>
                            <p class="loan-decision-hint">This message will appear on the client dashboard.</p>
                        </form>
                        <div class="loan-decision-actions">
                            <button class="button loan-action-reject" type="submit" form="reject-loan">Request changes</button>
                            <button class="button loan-action-approve" type="submit" form="approve-loan">Approve loan</button>
                        </div>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div></main>
@include('partials.admin-confirmation')
</body></html>
