<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client account report - {{ $client->name }}</title>
    <style>
        @page { margin: 34px 34px 54px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1d2939; font-family: DejaVu Sans, sans-serif; font-size: 8.5px; line-height: 1.42; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .masthead { margin-bottom: 16px; background: #101828; border-radius: 8px; }
        .masthead td { padding: 16px 18px; border: 0; }
        .brand { color: #a5b4fc; font-size: 8px; font-weight: bold; letter-spacing: 1.3px; text-transform: uppercase; }
        h1 { margin: 5px 0 3px; color: #ffffff; font-size: 21px; line-height: 1.15; }
        .header-subtitle { color: #d0d5dd; font-size: 8.5px; }
        .report-meta { width: 34%; color: #d0d5dd; line-height: 1.65; text-align: right; }
        .confidential { display: inline-block; margin-bottom: 5px; padding: 3px 7px; color: #ffffff; background: #4f46e5; border-radius: 9px; font-size: 7px; font-weight: bold; letter-spacing: .5px; }
        .section { margin-top: 16px; }
        .section-heading { margin: 0 0 7px; padding-bottom: 5px; color: #344054; border-bottom: 1px solid #d0d5dd; font-size: 11px; }
        .section-note { margin: -3px 0 8px; color: #667085; font-size: 7.5px; }
        .info-grid { table-layout: fixed; background: #f8fafc; border: 1px solid #e4e7ec; }
        .info-grid td { width: 33.33%; padding: 8px 9px; border-right: 1px solid #e4e7ec; border-bottom: 1px solid #e4e7ec; }
        .info-grid tr:last-child td { border-bottom: 0; }
        .info-grid td:last-child { border-right: 0; }
        .label { color: #667085; font-size: 6.8px; font-weight: bold; letter-spacing: .45px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #1d2939; font-size: 8.5px; overflow-wrap: break-word; }
        .metrics { table-layout: fixed; border-spacing: 6px 5px; border-collapse: separate; margin: -5px -6px 0; width: calc(100% + 12px); }
        .metrics td { width: 33.33%; padding: 9px; background: #f8fafc; border: 1px solid #e4e7ec; border-radius: 5px; }
        .metric-value { margin-top: 3px; color: #312e81; font-size: 12px; font-weight: bold; }
        .status-line { margin-top: 4px; padding: 7px 9px; color: #475467; background: #f5f3ff; border-left: 3px solid #6366f1; }
        .status-pill { display: inline-block; padding: 2px 6px; border-radius: 8px; font-size: 7px; font-weight: bold; text-transform: capitalize; }
        .status-approved, .status-paid { color: #05603a; background: #dcfae6; }
        .status-pending, .status-partial { color: #93370d; background: #fef0c7; }
        .status-rejected, .status-overdue, .status-failed, .status-missed { color: #912018; background: #fee4e2; }
        .status-neutral { color: #344054; background: #eaecf0; }
        .loan { margin-top: 12px; }
        .loan-history { margin-top: 0; page-break-before: always; }
        .loan-title { background: #344054; color: #ffffff; }
        .loan-title td { padding: 7px 9px; border: 0; }
        .loan-title .loan-status { width: 22%; color: #e4e7ec; text-align: right; text-transform: capitalize; }
        .loan-summary { table-layout: fixed; border: 1px solid #e4e7ec; page-break-inside: avoid; }
        .loan-summary td { width: 25%; padding: 7px 8px; border-right: 1px solid #e4e7ec; border-bottom: 1px solid #e4e7ec; }
        .loan-summary tr:last-child td { border-bottom: 0; }
        .loan-summary td:last-child { border-right: 0; }
        .callout { margin-top: 6px; padding: 7px 9px; color: #912018; background: #fff6f5; border: 1px solid #fecdca; }
        .subheading { margin: 8px 0 4px; color: #475467; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .data { table-layout: fixed; }
        .data thead { display: table-header-group; }
        .data th { padding: 5px 6px; color: #475467; background: #f2f4f7; border: 1px solid #e4e7ec; font-size: 6.5px; letter-spacing: .25px; text-align: left; text-transform: uppercase; }
        .data td { padding: 5px 6px; border: 1px solid #eaecf0; overflow-wrap: break-word; }
        .data .amount { text-align: right; white-space: nowrap; }
        .empty { padding: 11px; color: #667085; background: #f8fafc; border: 1px solid #e4e7ec; text-align: center; }
        .privacy { margin-top: 16px; padding: 8px 10px; color: #475467; background: #fffaeb; border: 1px solid #fedf89; font-size: 7.5px; }
        .page-footer { position: fixed; right: 0; bottom: -34px; left: 0; padding-top: 7px; color: #667085; border-top: 1px solid #d0d5dd; font-size: 7px; }
        .page-footer td { width: 33.33%; }
        .page-footer .center { text-align: center; }
        .page-footer .right { text-align: right; }
        .page-number:before { content: counter(page); }
    </style>
</head>
<body>
<table class="masthead">
    <tr>
        <td>
            <div class="brand">Inkcredible Lending</div>
            <h1>Client Account Report</h1>
            <div class="header-subtitle">A plain-language summary of the client's account and lending activity</div>
        </td>
        <td class="report-meta">
            <span class="confidential">CONFIDENTIAL</span><br>
            Report ID: ICR-{{ str_pad((string) $client->id, 6, '0', STR_PAD_LEFT) }}<br>
            Prepared: {{ $preparedAt->format('M d, Y - h:i A') }} PHT<br>
            Currency: Philippine Peso (PHP)
        </td>
    </tr>
</table>

<div class="section">
    <h2 class="section-heading">1. Client information</h2>
    <table class="info-grid">
        <tr>
            <td><div class="label">Client ID</div><div class="value">CL-{{ str_pad((string) $client->id, 6, '0', STR_PAD_LEFT) }}</div></td>
            <td><div class="label">Full name</div><div class="value">{{ $client->full_name }}</div></td>
            <td><div class="label">Email address</div><div class="value">{{ $client->email }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Contact number</div><div class="value">{{ $client->contact_number ?: 'Not provided' }}</div></td>
            <td><div class="label">Age</div><div class="value">{{ $client->age ? $client->age.' years old' : 'Not provided' }}</div></td>
            <td><div class="label">Email verification</div><div class="value">{{ $client->email_verified_at ? 'Verified on '.$client->email_verified_at->copy()->timezone('Asia/Manila')->format('M d, Y') : 'Not verified' }}</div></td>
        </tr>
        <tr>
            <td colspan="2"><div class="label">Home address</div><div class="value">{{ $client->address ?: 'Not provided' }}</div></td>
            <td><div class="label">Registered</div><div class="value">{{ $client->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y - h:i A') ?? 'Not available' }} PHT</div></td>
        </tr>
    </table>
</div>

@php $verification = $client->clientVerification; @endphp
<div class="section">
    <h2 class="section-heading">2. Verification and income information</h2>
    @if($verification)
        <table class="info-grid">
            <tr>
                <td><div class="label">Verification status</div><div class="value"><span class="status-pill status-{{ $verification->status }}">{{ $verification->status }}</span></div></td>
                <td><div class="label">Employment status</div><div class="value">{{ str($verification->employment_status)->replace('_', ' ')->title() }}</div></td>
                <td><div class="label">Monthly income</div><div class="value">PHP {{ number_format((float) $verification->monthly_income, 2) }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Company or business</div><div class="value">{{ $verification->company_name ?: 'Not applicable' }}</div></td>
                <td><div class="label">Position or occupation</div><div class="value">{{ $verification->job_title ?: 'Not applicable' }}</div></td>
                <td><div class="label">Income source</div><div class="value">{{ $verification->source_of_income }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Employment length</div><div class="value">{{ $verification->employment_length_months }} months</div></td>
                <td><div class="label">Valid ID type</div><div class="value">{{ str($verification->valid_id_type)->replace('_', ' ')->title() }}</div></td>
                <td><div class="label">Valid ID number</div><div class="value">{{ $verification->valid_id_number }}</div></td>
            </tr>
            <tr>
                <td><div class="label">Submitted</div><div class="value">{{ $verification->submitted_at?->copy()->timezone('Asia/Manila')->format('M d, Y - h:i A') ?? 'Not available' }} PHT</div></td>
                <td><div class="label">Reviewed</div><div class="value">{{ $verification->reviewed_at?->copy()->timezone('Asia/Manila')->format('M d, Y - h:i A') ?? 'Awaiting review' }}</div></td>
                <td><div class="label">Reviewed by</div><div class="value">{{ $verification->reviewer?->full_name ?? 'Not reviewed' }}</div></td>
            </tr>
            @if($verification->additional_information)
                <tr><td colspan="3"><div class="label">Additional information</div><div class="value">{{ $verification->additional_information }}</div></td></tr>
            @endif
            @if($verification->rejection_reason)
                <tr><td colspan="3"><div class="label">Verification rejection reason</div><div class="value">{{ $verification->rejection_reason }}</div></td></tr>
            @endif
        </table>
    @else
        <div class="empty">The client has not submitted account verification information.</div>
    @endif
</div>

<div class="section">
    <h2 class="section-heading">3. Financial summary</h2>
    <table class="metrics">
        <tr>
            <td><div class="label">Loan applications</div><div class="metric-value">{{ $summary['applications'] }}</div></td>
            <td><div class="label">Approved principal</div><div class="metric-value">PHP {{ number_format($summary['approved_principal'], 2) }}</div></td>
            <td><div class="label">Total payable</div><div class="metric-value">PHP {{ number_format($summary['scheduled_payable'], 2) }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Amount paid</div><div class="metric-value">PHP {{ number_format($summary['paid'], 2) }}</div></td>
            <td><div class="label">Penalties</div><div class="metric-value">PHP {{ number_format($summary['penalties'], 2) }}</div></td>
            <td><div class="label">Outstanding balance</div><div class="metric-value">PHP {{ number_format($summary['outstanding'], 2) }}</div></td>
        </tr>
    </table>
    <div class="status-line"><strong>Application status:</strong> {{ $summary['pending'] }} pending | {{ $summary['approved'] }} approved | {{ $summary['paid_loans'] }} paid | {{ $summary['rejected'] }} rejected &nbsp;&nbsp; <strong>Payment records:</strong> {{ $summary['payments'] }}</div>
    <div class="section-note" style="margin-top:5px">Financial totals include approved and paid loans. Pending and rejected applications do not add to the outstanding balance.</div>
</div>

<div class="privacy"><strong>Privacy notice:</strong> This report contains confidential personal and financial information. It is intended only for authorized account review, servicing, and recordkeeping. Store and share it securely.</div>

<div class="section loan-history">
    <h2 class="section-heading">4. Loan and repayment history</h2>
    @forelse($loans as $loan)
        @php
            $isFinancial = in_array($loan->status, ['approved', 'paid'], true);
            $loanPenalty = $loan->paymentSchedules->sum(fn ($schedule) => (float) $schedule->penalty_amount);
            $loanBalance = max(0, (float) $loan->total_payable + $loanPenalty - (float) $loan->paid_amount);
            $submittedAt = $loan->created_at?->copy()->timezone('Asia/Manila');
        @endphp
        <div class="loan">
            <table class="loan-title"><tr>
                <td><strong>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</strong> | {{ $loan->loanType?->display_name ?? $loan->loanType?->name ?? 'Loan product' }}</td>
                <td class="loan-status">{{ $loan->status }}</td>
            </tr></table>
            <table class="loan-summary">
                <tr>
                    <td><div class="label">Purpose</div><div class="value">{{ $loan->purpose ?: 'Not provided' }}</div></td>
                    <td><div class="label">Principal</div><div class="value">PHP {{ number_format((float) $loan->amount, 2) }}</div></td>
                    <td><div class="label">Interest rate</div><div class="value">{{ number_format((float) ($loan->loanType?->interest_rate ?? 0), 2) }}%</div></td>
                    <td><div class="label">Total payable</div><div class="value">PHP {{ number_format((float) $loan->total_payable, 2) }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Amount paid</div><div class="value">PHP {{ number_format((float) $loan->paid_amount, 2) }}</div></td>
                    <td><div class="label">Penalties</div><div class="value">PHP {{ number_format($loanPenalty, 2) }}</div></td>
                    <td><div class="label">Current balance</div><div class="value">{{ $isFinancial ? 'PHP '.number_format($loanBalance, 2) : 'Not applicable' }}</div></td>
                    <td><div class="label">Payment records</div><div class="value">{{ $loan->payments->count() }}</div></td>
                </tr>
                <tr>
                    <td><div class="label">Submitted</div><div class="value">{{ $submittedAt?->format('M d, Y - h:i A') ?? 'Not available' }} PHT</div></td>
                    <td><div class="label">Approved</div><div class="value">{{ $loan->approved_at?->copy()->timezone('Asia/Manila')->format('M d, Y - h:i A') ?? 'Not approved' }}</div></td>
                    <td><div class="label">Disbursed</div><div class="value">{{ $loan->disbursed_at?->copy()->timezone('Asia/Manila')->format('M d, Y - h:i A') ?? 'Not disbursed' }}</div></td>
                    <td><div class="label">Terms accepted</div><div class="value">{{ $loan->terms_accepted_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'Not recorded' }}{{ $loan->terms_version ? ' (v'.$loan->terms_version.')' : '' }}</div></td>
                </tr>
            </table>

            @if($loan->rejection_reason)
                <div class="callout"><strong>Rejection reason:</strong> {{ $loan->rejection_reason }}</div>
            @endif

            <div class="subheading">Repayment schedule</div>
            @if($loan->paymentSchedules->isNotEmpty())
                <table class="data">
                    <thead><tr><th>Installment</th><th>Due date</th><th class="amount">Scheduled</th><th class="amount">Paid</th><th class="amount">Penalty</th><th class="amount">Remaining</th><th>Status</th></tr></thead>
                    <tbody>@foreach($loan->paymentSchedules as $schedule)
                        @php $remaining = max(0, (float) $schedule->scheduled_amount + (float) $schedule->penalty_amount - (float) $schedule->paid_amount); @endphp
                        <tr>
                            <td>#{{ $schedule->installment_number }}</td>
                            <td>{{ $schedule->due_date?->format('M d, Y') ?? 'Not set' }}</td>
                            <td class="amount">PHP {{ number_format((float) $schedule->scheduled_amount, 2) }}</td>
                            <td class="amount">PHP {{ number_format((float) $schedule->paid_amount, 2) }}</td>
                            <td class="amount">PHP {{ number_format((float) $schedule->penalty_amount, 2) }}</td>
                            <td class="amount">PHP {{ number_format($remaining, 2) }}</td>
                            <td><span class="status-pill status-{{ $schedule->status }}">{{ $schedule->status }}</span></td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @else
                <div class="empty">No repayment schedule has been created for this loan.</div>
            @endif

            <div class="subheading">Payment history</div>
            @if($loan->payments->isNotEmpty())
                <table class="data">
                    <thead><tr><th>Date and time</th><th>Reference</th><th>Method</th><th>Provider</th><th class="amount">Amount</th><th>Status</th></tr></thead>
                    <tbody>@foreach($loan->payments as $payment)
                        @php $paymentTime = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila'); @endphp
                        <tr>
                            <td>{{ $paymentTime?->format('M d, Y - h:i A') ?? 'Not available' }} PHT</td>
                            <td>{{ $payment->reference ?: ($payment->provider_reference ?: 'Not recorded') }}</td>
                            <td>{{ $payment->method_label }}</td>
                            <td>{{ str($payment->provider ?: 'manual')->replace('_', ' ')->title() }}</td>
                            <td class="amount">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                            <td><span class="status-pill status-{{ $payment->status }}">{{ $payment->status }}</span></td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @else
                <div class="empty">No payment transactions have been recorded for this loan.</div>
            @endif

            <div class="subheading">Submitted documents</div>
            @if($loan->loanDocuments->isNotEmpty())
                <table class="data">
                    <thead><tr><th>Document type</th><th>Original filename</th><th>File type</th><th>Uploaded</th><th>Verification</th></tr></thead>
                    <tbody>@foreach($loan->loanDocuments as $document)
                        <tr>
                            <td>{{ str($document->document_type)->replace('_', ' ')->title() }}</td>
                            <td>{{ $document->original_filename ?: 'Not recorded' }}</td>
                            <td>{{ $document->mime_type ?: 'Not recorded' }}</td>
                            <td>{{ $document->created_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'Not available' }}</td>
                            <td>{{ $document->is_verified ? 'Verified' : 'Not verified' }}</td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @else
                <div class="empty">No loan documents are attached to this record.</div>
            @endif
        </div>
    @empty
        <div class="empty">No loan applications have been recorded for this client.</div>
    @endforelse
</div>

<table class="page-footer"><tr><td>Inkcredible Lending | Client report ICR-{{ str_pad((string) $client->id, 6, '0', STR_PAD_LEFT) }}</td><td class="center">Page <span class="page-number"></span></td><td class="right">Generated {{ $preparedAt->format('Y-m-d') }}</td></tr></table>
</body>
</html>
