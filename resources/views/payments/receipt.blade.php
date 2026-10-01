<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    @php
        $loan = $payment->loan;
        $client = $loan?->user ?? $payment->user;
        $transactionDate = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
        $submittedDate = $payment->created_at?->copy()->timezone('Asia/Manila');
        $reference = $payment->provider_reference ?: $payment->reference ?: 'Payment #'.$payment->id;
        $loanName = $loan?->loanType?->display_name ?? $loan?->loanType?->name ?? 'Loan';
        $loanCode = $loan?->loan_code ?: 'Loan #'.$payment->loan_id;
        $statusColor = match ($payment->status) {
            \App\Models\Payment::STATUS_APPROVED => '#067647',
            \App\Models\Payment::STATUS_REJECTED => '#b42318',
            default => '#b54708',
        };
        $statusBackground = match ($payment->status) {
            \App\Models\Payment::STATUS_APPROVED => '#ecfdf3',
            \App\Models\Payment::STATUS_REJECTED => '#fef3f2',
            default => '#fffaeb',
        };
    @endphp
    <title>Payment Receipt {{ $reference }}</title>
    <style>
        @page { margin: 34px 38px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #101828; font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.5; }
        .top-rule { height: 7px; margin-bottom: 28px; background: #4f46e5; border-radius: 4px; }
        .header { width: 100%; margin-bottom: 28px; }
        .brand { color: #3730a3; font-size: 21px; font-weight: bold; }
        .brand-note { margin-top: 4px; color: #667085; font-size: 9px; }
        .document-title { color: #101828; font-size: 25px; font-weight: bold; text-align: right; }
        .document-number { margin-top: 5px; color: #667085; font-size: 9px; text-align: right; }
        .summary { margin-bottom: 24px; padding: 22px; background: #f8f9ff; border: 1px solid #e0e7ff; border-radius: 12px; }
        .amount-label { color: #667085; font-size: 9px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .amount { margin-top: 5px; color: #1d2939; font-size: 30px; font-weight: bold; }
        .currency { margin-top: 3px; color: #98a2b3; font-size: 9px; }
        .status { display: inline-block; padding: 7px 11px; color: {{ $statusColor }}; background: {{ $statusBackground }}; border-radius: 12px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-date { margin-top: 8px; color: #667085; font-size: 9px; }
        .section-title { margin: 0 0 9px; color: #344054; font-size: 10px; font-weight: bold; letter-spacing: .6px; text-transform: uppercase; }
        .details { width: 100%; margin-bottom: 24px; border-collapse: collapse; border: 1px solid #e4e7ec; }
        .details td { width: 50%; padding: 13px 15px; border-bottom: 1px solid #eaecf0; vertical-align: top; }
        .details tr:last-child td { border-bottom: 0; }
        .details td + td { border-left: 1px solid #eaecf0; }
        .label { display: block; margin-bottom: 4px; color: #667085; font-size: 8px; font-weight: bold; letter-spacing: .4px; text-transform: uppercase; }
        .value { color: #344054; font-size: 10px; font-weight: bold; word-break: break-word; }
        .notice { padding: 13px 15px; color: #475467; background: #fcfcfd; border-left: 4px solid #c7d2fe; font-size: 9px; line-height: 1.55; }
        .footer { position: fixed; right: 0; bottom: -10px; left: 0; color: #98a2b3; font-size: 8px; text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="top-rule"></div>
    <table class="header"><tr>
        <td><div class="brand">Inkcredible</div><div class="brand-note">Official payment transaction record</div></td>
        <td><div class="document-title">Payment Receipt</div><div class="document-number">Receipt #{{ str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT) }}</div></td>
    </tr></table>

    <table class="summary"><tr>
        <td><div class="amount-label">Payment amount</div><div class="amount">&#8369;{{ number_format((float) $payment->amount, 2) }}</div><div class="currency">{{ strtoupper($payment->currency ?: 'PHP') }}</div></td>
        <td class="text-right"><span class="status">{{ $payment->status }}</span><div class="status-date">{{ $transactionDate?->format('F j, Y - g:i A') ?? 'Date unavailable' }} PHT</div></td>
    </tr></table>

    <div class="section-title">Transaction details</div>
    <table class="details">
        <tr><td><span class="label">Reference</span><span class="value">{{ $reference }}</span></td><td><span class="label">Transaction ID</span><span class="value">#{{ $payment->id }}</span></td></tr>
        <tr><td><span class="label">Payment method</span><span class="value">{{ $payment->method_label }}</span></td><td><span class="label">Status</span><span class="value">{{ ucfirst($payment->status) }}</span></td></tr>
        <tr><td><span class="label">Submitted</span><span class="value">{{ $submittedDate?->format('F j, Y - g:i A') ?? 'Not available' }} PHT</span></td><td><span class="label">Confirmed</span><span class="value">{{ $payment->paid_at ? $transactionDate?->format('F j, Y - g:i A').' PHT' : 'Not yet confirmed' }}</span></td></tr>
    </table>

    <div class="section-title">Client and loan</div>
    <table class="details">
        <tr><td><span class="label">Client</span><span class="value">{{ $client?->name ?? 'Unknown client' }}</span></td><td><span class="label">Email</span><span class="value">{{ $client?->email ?? 'Not available' }}</span></td></tr>
        <tr><td><span class="label">Loan account</span><span class="value">{{ $loanCode }}</span></td><td><span class="label">Loan product</span><span class="value">{{ $loanName }}</span></td></tr>
    </table>

    <div class="notice">
        @if($payment->status === \App\Models\Payment::STATUS_APPROVED)
            This receipt confirms that the payment above was recorded and applied to the associated loan account.
        @elseif($payment->status === \App\Models\Payment::STATUS_PENDING)
            This receipt records a submitted payment that is still awaiting confirmation. It is not proof of final settlement.
        @else
            This receipt records an unsuccessful or rejected payment attempt. No payment was applied to the loan account.
        @endif
    </div>

    <div class="footer">Generated securely by Inkcredible on {{ now()->timezone('Asia/Manila')->format('F j, Y - g:i A') }} PHT</div>
</body>
</html>
