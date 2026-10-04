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
.loan-decision { overflow:hidden; border-color:#d9d6fe; box-shadow:0 12px 30px rgba(79,70,229,.08); }
.loan-decision .panel-header { background:linear-gradient(135deg,#fafaff 0%,#f5f3ff 100%); border-bottom-color:#e0e7ff; }
.contract-stage-badge { padding:6px 9px; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:999px; font-size:9px; font-weight:700; letter-spacing:.04em; white-space:nowrap; text-transform:uppercase; }
.loan-decision-body { display:grid; gap:18px; }
.loan-rejection-form { display:grid; gap:7px; }
.loan-decision-label { display:flex; align-items:center; justify-content:space-between; gap:10px; color:#344054; font-size:11px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
.loan-required { color:#b42318; font-size:10px; font-weight:600; letter-spacing:0; text-transform:none; }
.loan-decision-input { width:100%; min-height:42px; padding:9px 12px; color:#344054; background:#fff; border:1px solid #cfd4dc; border-radius:9px; outline:none; font-size:12px; transition:.15s ease; }
.loan-decision-input::placeholder { color:#98a2b3; }
.loan-decision-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.loan-decision-hint { margin:0; color:#667085; font-size:11px; line-height:1.45; }
.loan-decision-actions { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; padding-top:2px; }
.loan-decision-actions.single-action { grid-template-columns:1fr; }
.loan-decision-actions .button { width:100%; min-height:42px; }
.loan-action-reject { color:#b42318; background:#fff5f4; border-color:#fda29b; }
.loan-action-reject:hover { color:#912018; background:#fee4e2; border-color:#f97066; }
.loan-action-approve { color:#fff; background:#067647; border-color:#067647; box-shadow:0 4px 10px rgba(6,118,71,.16); }
.loan-action-approve:hover { color:#fff; background:#05603a; border-color:#05603a; }
.risk-score { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px; padding:13px; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; }
.risk-level { padding:5px 8px; border-radius:999px; font-size:10px; font-weight:700; }
.risk-level.success { color:#05603a; background:#dcfae6; }.risk-level.warning { color:#b54708; background:#fef0c7; }.risk-level.danger { color:#b42318; background:#fee4e2; }.risk-level.neutral { color:#475467; background:#f2f4f7; }
.risk-detail { display:grid; gap:8px; color:#475467; font-size:11px; line-height:1.5; }
.contract-progress { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:6px; }
.contract-progress-step { position:relative; padding-top:13px; color:#98a2b3; font-size:9px; font-weight:700; line-height:1.35; text-align:center; text-transform:uppercase; }
.contract-progress-step::before { position:absolute; top:0; left:0; width:100%; height:4px; background:#eaecf0; border-radius:999px; content:''; }
.contract-progress-step.is-complete { color:#067647; }
.contract-progress-step.is-complete::before { background:#12b76a; }
.contract-progress-step.is-current { color:#4338ca; }
.contract-progress-step.is-current::before { background:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.contract-state { display:flex; align-items:flex-start; gap:12px; padding:15px; background:#f8fafc; border:1px solid #eaecf0; border-radius:12px; }
.contract-state-icon { display:grid; place-items:center; width:38px; height:38px; flex:0 0 38px; color:#4f46e5; background:#eef2ff; border-radius:10px; }
.contract-state-icon svg { width:18px; height:18px; }
.contract-state.ready { background:#f6fef9; border-color:#abefc6; }
.contract-state.ready .contract-state-icon { color:#067647; background:#dcfae6; }
.contract-state-copy { min-width:0; color:#667085; font-size:11px; line-height:1.55; }
.contract-state-copy strong { display:block; margin-bottom:3px; color:#344054; font-size:12px; }
.contract-state-meta { display:block; margin-top:5px; color:#475467; font-size:10px; font-weight:600; overflow-wrap:anywhere; }
.contract-actions { display:grid; gap:9px; }
.contract-actions.two { grid-template-columns:repeat(2,minmax(0,1fr)); }
.contract-actions .button { width:100%; }
.decision-divider { height:1px; background:#eaecf0; }
.decision-heading { margin:0; color:#344054; font-size:12px; font-weight:700; }
.decision-copy { margin:4px 0 0; color:#667085; font-size:10px; line-height:1.5; }
.admin-signature-form { display:grid; gap:12px; }
.admin-signature-pad { padding:14px; background:#fafaff; border:1px solid #d9d6fe; border-radius:12px; }
.admin-signature-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:11px; }
.admin-signature-head strong { display:block; color:#344054; font-size:11px; }
.admin-signature-head span { display:block; margin-top:3px; color:#667085; font-size:9px; line-height:1.4; }
.admin-signature-pad canvas { display:block; width:100%; height:130px; background:#fff; border:1px solid #a5b4fc; border-radius:9px; cursor:crosshair; touch-action:none; }
.admin-signature-controls { display:flex; justify-content:space-between; gap:10px; margin-top:9px; color:#667085; font-size:9px; line-height:1.4; }
.admin-signature-controls button { padding:4px 8px; color:#4338ca; background:#eef2ff; border:0; border-radius:6px; font-size:9px; font-weight:700; cursor:pointer; }
.admin-signature-error { margin:8px 0 0; color:#b42318; font-size:9px; font-weight:600; }
.admin-approval-consent { display:flex; align-items:flex-start; gap:9px; padding:11px; color:#475467; background:#f8fafc; border-radius:9px; font-size:10px; line-height:1.45; }
.admin-approval-consent input { width:15px; height:15px; flex:0 0 15px; margin-top:1px; accent-color:#4f46e5; }
.final-contract-panel { border-color:#abefc6; }
.final-contract-panel .panel-header { background:#f6fef9; }
.payment-summary-panel { border-color:#dfe3f0; box-shadow:0 8px 24px rgba(16,24,40,.05); }
.payment-summary-panel .panel-header { background:linear-gradient(135deg,#fff 0%,#f8fafc 100%); }
.payment-summary-table th,.payment-summary-table td { padding:13px 0; background:transparent; border-bottom:1px solid #f2f4f7; font-size:12px; letter-spacing:0; text-transform:none; }
.payment-summary-table th { color:#667085; font-weight:500; white-space:normal; }
.payment-summary-table td { color:#344054; font-weight:700; text-align:right; white-space:nowrap; }
.payment-summary-table tr:last-child th,.payment-summary-table tr:last-child td { border-bottom:0; }
.payment-summary-table tr:hover { background:transparent; }
.payment-summary-table .summary-balance th,.payment-summary-table .summary-balance td { padding-top:16px; color:#101828; font-size:13px; }
.payment-summary-progress { height:7px; margin-top:16px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.payment-summary-progress span { display:block; height:100%; background:linear-gradient(90deg,#12b76a,#079455); border-radius:inherit; }
.payment-summary-caption { margin-top:8px; color:#667085; font-size:10px; text-align:right; }
.document-modal .modal-dialog { width:min(980px,calc(100% - 30px)); max-width:980px; }
.document-modal .modal-content { overflow:hidden; border:0; border-radius:16px; box-shadow:0 24px 70px rgba(16,24,40,.24); }
.document-modal-head { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; padding:18px 20px; color:#fff; background:linear-gradient(135deg,#312e81,#4f46e5); }
.document-modal-head h2 { margin:2px 0 0; font-size:18px; }
.document-modal-head p { margin:5px 0 0; color:#e0e7ff; font-size:10px; }
.document-modal-head .btn-close { margin-top:2px; filter:invert(1) grayscale(1) brightness(2); }
.document-modal-body { height:min(72vh,760px); min-height:420px; padding:0; background:#f2f4f7; }
.document-modal-frame { display:block; width:100%; height:100%; border:0; background:#fff; }
@media(max-width:470px){.contract-actions.two,.loan-decision-actions{grid-template-columns:1fr}.contract-progress-step{font-size:8px}}
@media(max-width:600px){.document-modal-body{height:70vh;min-height:360px}.document-modal-head{padding:15px 16px}}
</style>
</head>
<body class="admin-loans-page admin-loan-detail-page">
@php
    $submitted = $loan->created_at?->copy()->timezone('Asia/Manila');
    $successfulPayments = $loan->payments->where('status', 'approved');
    $paidInstallments = $loan->paymentSchedules->where('status', 'paid')->count();
    $totalInstallments = $loan->paymentSchedules->count();
    $confirmedPaidAmount = (float) $successfulPayments->sum('amount');
    $remainingBalance = (float) $loan->getRemainingBalance();
    $paymentProgress = $loan->getTotalWithPenalty() > 0
        ? min(100, max(0, ($confirmedPaidAmount / $loan->getTotalWithPenalty()) * 100))
        : 0;
    $contractStage = ! $loan->contract_sent_at ? 1 : (! $loan->signed_contract_path ? 2 : (! $loan->admin_signed_at ? 3 : 4));
@endphp
@include('partials.admin-sidebar', ['active' => 'loans'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Loan review</div><h1>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</h1><p class="subtitle">Complete application, repayment, and client information.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.loans') }}">Back to loans</a><x-status-badge :status="$loan->status" /></div>
    </header>
    <x-flash-messages />
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
                <div class="panel-header"><div><h2 class="panel-title">Affordability risk</h2><p class="panel-description">Income-based decision support; administrators retain final judgment.</p></div></div>
                <div class="panel-body">
                    <div class="risk-score"><div><div class="detail-label">Behavior-adjusted readiness</div><div class="detail-value">{{ $riskAssessment['readinessScore'] }} / 100</div></div><span class="risk-level {{ $riskAssessment['tone'] }}">{{ $riskAssessment['level'] }} risk</span></div>
                    <div class="risk-detail"><div><strong>Commitment-to-income:</strong> {{ $riskAssessment['ratio'] === null ? 'Unavailable' : number_format($riskAssessment['ratio'], 1).'%' }}</div><div><strong>Verified payslip:</strong> {{ $riskAssessment['verifiedPayslip'] ? 'Yes' : 'No' }}</div><div><strong>Repayment history:</strong> {{ $riskAssessment['earlyPayments'] }} early, {{ $riskAssessment['onTimePayments'] }} on time, {{ $riskAssessment['latePayments'] }} late/overdue</div><div><strong>Completed loans:</strong> {{ $riskAssessment['completedLoans'] }}</div><div><strong>Assessment:</strong> {{ $riskAssessment['suggestion'] }}</div></div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Government ID</h2><p class="panel-description">Identity document submitted by the client.</p></div></div>
                <div class="panel-body">
                    @if($loan->government_id)
                        <div class="detail-value">Document is available for secure review.</div>
                        <button class="button button-primary" style="margin-top:14px" type="button" data-government-id-open data-document-url="{{ route('admin.loan.government-id', $loan) }}">Open document</button>
                    @else
                        <div class="empty-state" style="padding:22px 0"><strong>No document found</strong>This application has no attached government ID.</div>
                    @endif
                </div>
            </section>

            <section class="panel payment-summary-panel">
                <div class="panel-header"><div><h2 class="panel-title">Payment summary</h2><p class="panel-description">Current repayment totals for this loan.</p></div></div>
                <div class="panel-body">
                    <table class="payment-summary-table" aria-label="Payment summary">
                        <tbody>
                            <tr><th scope="row">Paid installments</th><td>{{ $paidInstallments }} of {{ $totalInstallments }}</td></tr>
                            <tr><th scope="row">Confirmed payments</th><td>{{ $successfulPayments->count() }}</td></tr>
                            <tr><th scope="row">Total paid</th><td>&#8369;{{ number_format($confirmedPaidAmount, 2) }}</td></tr>
                            <tr class="summary-balance"><th scope="row">Remaining balance</th><td>&#8369;{{ number_format($remainingBalance, 2) }}</td></tr>
                        </tbody>
                    </table>
                    <div class="payment-summary-progress" aria-label="{{ number_format($paymentProgress, 1) }} percent paid"><span style="width:{{ number_format($paymentProgress, 2, '.', '') }}%"></span></div>
                    <div class="payment-summary-caption">{{ number_format($paymentProgress, 1) }}% of the current payable balance received</div>
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
                    <div class="panel-header"><div><h2 class="panel-title">Contract and decision</h2><p class="panel-description">Both parties must digitally sign before final approval.</p></div><span class="contract-stage-badge">Step {{ $contractStage }} of 4</span></div>
                    <div class="panel-body loan-decision-body">
                        <div class="contract-progress" aria-label="Contract progress, step {{ $contractStage }} of 4">
                            <div class="contract-progress-step {{ $contractStage === 1 ? 'is-current' : 'is-complete' }}">Issue</div>
                            <div class="contract-progress-step {{ $contractStage === 2 ? 'is-current' : ($contractStage > 2 ? 'is-complete' : '') }}">Client signs</div>
                            <div class="contract-progress-step {{ $contractStage === 3 ? 'is-current' : ($contractStage > 3 ? 'is-complete' : '') }}">Admin signs</div>
                            <div class="contract-progress-step {{ $contractStage === 4 ? 'is-current' : '' }}">Approved</div>
                        </div>
                        @if(! $loan->contract_sent_at)
                            <div class="contract-state"><span class="contract-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM8 2v4m8-4v4M8 11h8m-8 4h5"/></svg></span><div class="contract-state-copy"><strong>Agreement is ready to issue</strong>Notify the client that the agreement is ready to review and digitally sign online.</div></div>
                            <div class="contract-actions"><form method="POST" action="{{ route('admin.loan.contract.send', $loan) }}" data-confirm="Generate and send this contract to the client?">@csrf<button class="button button-primary" type="submit">Send contract to client</button></form></div>
                        @elseif(! $loan->signed_contract_path)
                            <div class="contract-state"><span class="contract-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></span><div class="contract-state-copy"><strong>Waiting for the client</strong>The client can apply the verified digital signature and submit the agreement in one step.<span class="contract-state-meta">Sent {{ $loan->contract_sent_at->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</span></div></div>
                            <div class="contract-actions two"><a class="button button-secondary" href="{{ route('loan.contract.download', $loan) }}" download="{{ $loan->loan_code ?: 'loan-'.$loan->id }}-contract.pdf" data-no-transition>Download issued PDF</a><form method="POST" action="{{ route('admin.loan.contract.send', $loan) }}" data-confirm="Resend the contract? Any prior contract submission will be cleared.">@csrf<button class="button button-secondary" type="submit">Resend notification</button></form></div>
                        @else
                            <div class="contract-state ready"><span class="contract-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg></span><div class="contract-state-copy"><strong>Signed PDF ready for review</strong>Verify the borrower signature and agreement details before final approval.<span class="contract-state-meta">{{ $loan->contract_signature_name }} &middot; {{ $loan->contract_signed_at->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</span></div></div>
                            <div class="contract-actions"><a class="button button-secondary" href="{{ route('loan.contract.signed.download', $loan) }}" download="{{ $loan->loan_code ?: 'loan-'.$loan->id }}-signed-contract.pdf" data-no-transition>Download client-signed PDF</a></div>
                            <form id="approve-loan" class="admin-signature-form" method="POST" action="{{ route('admin.loan.approve', $loan->id) }}" data-confirm="Apply your digital signature and final-approve this loan contract?">@csrf
                                <div class="admin-signature-pad" data-signature-pad data-signature-required="true">
                                    <div class="admin-signature-head"><div><strong>Administrator digital signature</strong><span>Draw your signature to countersign this agreement.</span></div><span>Required for approval</span></div>
                                    <canvas width="720" height="220" data-signature-canvas aria-label="Draw administrator digital signature"></canvas>
                                    <input type="hidden" name="admin_signature" data-signature-input>
                                    <div class="admin-signature-controls"><span>This signature and your administrator identity will be recorded on the final PDF.</span><button type="button" data-signature-clear>Clear</button></div>
                                    <p class="admin-signature-error" data-signature-error @if(! $errors->has('admin_signature')) hidden @endif>{{ $errors->first('admin_signature') ?: 'Draw your signature before final approval.' }}</p>
                                </div>
                                <label class="admin-approval-consent"><input type="checkbox" name="approval_accepted" value="1" required><span>I reviewed the client-signed agreement and authorize my digital signature to finalize this contract.</span></label>
                            </form>
                        @endif
                        <div class="decision-divider"></div>
                        <div><h3 class="decision-heading">Decision note</h3><p class="decision-copy">Required only when requesting changes or rejecting this application.</p></div>
                        <form id="reject-loan" class="loan-rejection-form" method="POST" action="{{ route('admin.loan.reject', $loan->id) }}" data-confirm="Reject this loan request and send the reason to the client?">
                            @csrf
                            <label class="loan-decision-label" for="rejection_reason"><span>Message to client</span><span class="loan-required">Required to send</span></label>
                            <input class="loan-decision-input" id="rejection_reason" type="text" name="rejection_reason" value="{{ old('rejection_reason') }}" maxlength="500" placeholder="Explain why this request cannot be approved" required>
                            <p class="loan-decision-hint">This message will appear on the client dashboard.</p>
                        </form>
                        <div class="loan-decision-actions {{ $loan->signed_contract_path ? '' : 'single-action' }}">
                            <button class="button loan-action-reject" type="submit" form="reject-loan">Request changes</button>
                            @if($loan->signed_contract_path)<button class="button loan-action-approve" type="submit" form="approve-loan">Final approve loan</button>@endif
                        </div>
                    </div>
                </section>
            @endif

            @if(in_array($loan->status, ['approved', 'paid'], true) && $loan->signed_contract_path)
                <section class="panel final-contract-panel">
                    <div class="panel-header"><div><h2 class="panel-title">Final signed contract</h2><p class="panel-description">This contract remains available after final approval.</p></div><span class="badge badge-success">Completed</span></div>
                    <div class="panel-body">
                        <div class="contract-state ready"><span class="contract-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg></span><div class="contract-state-copy"><strong>Signed by client and administrator</strong>The finalized PDF contains both digital signatures and their signing timestamps.<span class="contract-state-meta">Administrator: {{ $loan->admin_signature_name ?: 'Recorded administrator' }} &middot; {{ $loan->admin_signed_at?->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</span></div></div>
                        <a class="button button-primary" style="width:100%;margin-top:12px" href="{{ route('loan.contract.signed.download', $loan) }}" download="{{ $loan->loan_code ?: 'loan-'.$loan->id }}-final-signed-contract.pdf" data-no-transition>Download final signed contract</a>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div></main>
@if($loan->government_id)
<div class="modal fade document-modal" id="government-id-modal" tabindex="-1" aria-labelledby="government-id-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content">
        <div class="document-modal-head"><div><div class="eyebrow" style="color:#c7d2fe">Secure document review</div><h2 id="government-id-modal-title">Government ID</h2><p>The document remains inside this loan review window.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close document"></button></div>
        <div class="document-modal-body"><iframe class="document-modal-frame" data-government-id-frame title="Submitted government ID"></iframe></div>
    </div></div>
</div>
@endif
@include('partials.admin-confirmation')
</body></html>
