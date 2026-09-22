<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client report - {{ $client->name }}</title>
    <style>
        @page { margin: 42px 38px 48px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.45; }
        .masthead { width: 100%; padding-bottom: 16px; border-bottom: 3px solid #243b64; }
        .masthead td { border: 0; padding: 0; vertical-align: top; }
        .brand { color: #c27a24; font-size: 9px; font-weight: bold; letter-spacing: 1.4px; text-transform: uppercase; }
        h1 { margin: 6px 0 3px; color: #243b64; font-family: DejaVu Serif, serif; font-size: 24px; }
        .subtitle, .muted { color: #667085; }
        .meta { color: #667085; line-height: 1.7; text-align: right; }
        .meta strong { display: block; color: #172033; }
        .section { margin-top: 20px; }
        .section-title { margin: 0 0 8px; padding-bottom: 5px; color: #243b64; border-bottom: 1px solid #dfe3ea; font-family: DejaVu Serif, serif; font-size: 13px; }
        .profile { width: 100%; background: #f5f7fa; border-left: 4px solid #c27a24; }
        .profile td { width: 33.33%; padding: 9px 11px; border: 0; vertical-align: top; }
        .label { color: #667085; font-size: 7px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; }
        .value { margin-top: 2px; font-size: 9px; }
        .metrics { width: 100%; border-collapse: separate; border-spacing: 5px 0; margin-left: -5px; }
        .metrics td { width: 20%; padding: 9px; border: 1px solid #dfe3ea; }
        .metric-value { margin-top: 4px; color: #243b64; font-size: 13px; font-weight: bold; }
        .loan { margin: 0 0 16px; page-break-inside: avoid; }
        .loan-header { width: 100%; color: #fff; background: #243b64; }
        .loan-header td { padding: 7px 9px; border: 0; }
        .loan-header .right { color: #d7e0ef; text-align: right; text-transform: capitalize; }
        table.data { width: 100%; border-collapse: collapse; }
        .data th { padding: 6px; color: #667085; background: #f5f7fa; border-bottom: 1px solid #dfe3ea; font-size: 7px; letter-spacing: .4px; text-align: left; text-transform: uppercase; }
        .data td { padding: 6px; border-bottom: 1px solid #edf0f3; vertical-align: top; }
        .data .amount { text-align: right; white-space: nowrap; }
        .subtable { margin-top: 6px; }
        .status { font-weight: bold; text-transform: capitalize; }
        .status-paid, .status-approved { color: #087443; }
        .status-overdue, .status-rejected, .status-missed { color: #b42318; }
        .status-pending, .status-partial { color: #9a6700; }
        .empty { padding: 14px; color: #667085; background: #f5f7fa; text-align: center; }
        .footer { margin-top: 24px; padding-top: 9px; color: #667085; border-top: 1px solid #dfe3ea; font-size: 7.5px; }
    </style>
</head>
<body>
<table class="masthead">
    <tr>
        <td>
            <div class="brand">Inkcredible Lending</div>
            <h1>Client Account Report</h1>
            <div class="subtitle">Identity, applications, repayment schedules, and payment history</div>
        </td>
        <td class="meta">
            <strong>CONFIDENTIAL</strong>
            Report #ICR-{{ str_pad((string) $client->id, 6, '0', STR_PAD_LEFT) }}<br>
            Prepared {{ $preparedAt->format('F d, Y g:i A') }} PHT<br>
            All amounts in PHP
        </td>
    </tr>
</table>

<div class="section">
    <h2 class="section-title">Client profile</h2>
    <table class="profile">
        <tr>
            <td><div class="label">Full name</div><div class="value">{{ $client->name }}</div></td>
            <td><div class="label">Email address</div><div class="value">{{ $client->email }}</div></td>
            <td><div class="label">Contact number</div><div class="value">{{ $client->contact_number ?: 'Not provided' }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Address</div><div class="value">{{ $client->address ?: 'Not provided' }}</div></td>
            <td><div class="label">Verification status</div><div class="value">{{ ucfirst($client->clientVerification?->status ?? 'Not submitted') }}</div></td>
            <td><div class="label">Registered</div><div class="value">{{ $client->created_at?->copy()->timezone('Asia/Manila')->format('F d, Y') ?? 'Not available' }}</div></td>
        </tr>
    </table>
</div>

<div class="section">
    <h2 class="section-title">Portfolio summary</h2>
    <table class="metrics">
        <tr>
            <td><div class="label">Applications</div><div class="metric-value">{{ $summary['applications'] }}</div></td>
            <td><div class="label">Approved principal</div><div class="metric-value">PHP {{ number_format($summary['approved_principal'], 2) }}</div></td>
            <td><div class="label">Paid</div><div class="metric-value">PHP {{ number_format($summary['paid'], 2) }}</div></td>
            <td><div class="label">Penalties</div><div class="metric-value">PHP {{ number_format($summary['penalties'], 2) }}</div></td>
            <td><div class="label">Outstanding</div><div class="metric-value">PHP {{ number_format($summary['outstanding'], 2) }}</div></td>
        </tr>
    </table>
    <div class="muted" style="margin-top: 5px;">Financial totals include approved and paid loans only; pending or rejected applications do not create an outstanding balance.</div>
</div>

<div class="section">
    <h2 class="section-title">Loan activity ({{ $loans->count() }} {{ \Illuminate\Support\Str::plural('record', $loans->count()) }})</h2>
    @forelse($loans as $loan)
        @php
            $isFinancial = in_array($loan->status, ['approved', 'paid'], true);
            $loanPenalty = $loan->paymentSchedules->sum(fn ($schedule) => (float) $schedule->penalty_amount);
            $loanBalance = max(0, (float) $loan->total_payable + $loanPenalty - (float) $loan->paid_amount);
        @endphp
        <div class="loan">
            <table class="loan-header"><tr>
                <td><strong>{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</strong> &nbsp;|&nbsp; {{ $loan->loanType?->display_name ?? 'Loan' }}</td>
                <td class="right">{{ $loan->status }}</td>
            </tr></table>
            <table class="data">
                <thead><tr><th>Principal</th><th>Total payable</th><th>Paid</th><th>Penalty</th><th class="amount">Balance</th><th>Approved / disbursed</th></tr></thead>
                <tbody><tr>
                    <td>PHP {{ number_format((float) $loan->amount, 2) }}</td>
                    <td>PHP {{ number_format((float) $loan->total_payable, 2) }}</td>
                    <td>PHP {{ number_format((float) $loan->paid_amount, 2) }}</td>
                    <td>PHP {{ number_format($loanPenalty, 2) }}</td>
                    <td class="amount">{{ $isFinancial ? 'PHP '.number_format($loanBalance, 2) : 'N/A' }}</td>
                    <td>{{ $loan->approved_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'Not approved' }}{{ $loan->disbursed_at ? ' / '.$loan->disbursed_at->copy()->timezone('Asia/Manila')->format('M d, Y') : '' }}</td>
                </tr></tbody>
            </table>

            @if($loan->paymentSchedules->isNotEmpty())
                <table class="data subtable">
                    <thead><tr><th>Installment</th><th>Due date</th><th class="amount">Scheduled</th><th class="amount">Paid</th><th class="amount">Penalty</th><th>Status</th></tr></thead>
                    <tbody>@foreach($loan->paymentSchedules as $schedule)
                        <tr>
                            <td>#{{ $schedule->installment_number }}</td><td>{{ $schedule->due_date?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="amount">PHP {{ number_format((float) $schedule->scheduled_amount, 2) }}</td><td class="amount">PHP {{ number_format((float) $schedule->paid_amount, 2) }}</td>
                            <td class="amount">PHP {{ number_format((float) $schedule->penalty_amount, 2) }}</td><td class="status status-{{ $schedule->status }}">{{ $schedule->status }}</td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @endif

            @if($loan->payments->isNotEmpty())
                <table class="data subtable">
                    <thead><tr><th>Payment date</th><th>Reference</th><th>Method</th><th class="amount">Amount</th><th>Status</th></tr></thead>
                    <tbody>@foreach($loan->payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_at?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'Not settled' }}</td><td>{{ $payment->reference ?: ($payment->provider_reference ?: 'N/A') }}</td>
                            <td>{{ ucfirst($payment->method ?: ($payment->provider ?: 'Not recorded')) }}</td><td class="amount">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="status status-{{ $payment->status }}">{{ $payment->status }}</td>
                        </tr>
                    @endforeach</tbody>
                </table>
            @endif
        </div>
    @empty
        <div class="empty">No loan activity has been recorded for this client.</div>
    @endforelse
</div>

<div class="footer">
    Generated from the Inkcredible Lending administration system for authorized internal review. This document contains confidential client information. Scheduled payable for approved and paid loans: PHP {{ number_format($summary['scheduled_payable'], 2) }}.
</div>
</body>
</html>
