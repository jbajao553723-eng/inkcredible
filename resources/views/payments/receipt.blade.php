<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Receipt #{{ $payment->id }}</title>
<style>
@page { margin:36px 44px; }
* { box-sizing:border-box; }
body { margin:0; color:#172033; font-family:DejaVu Sans,sans-serif; font-size:10px; line-height:1.5; }
.header { width:100%; padding-bottom:15px; border-bottom:3px solid #d71920; }
.header td { padding:0; vertical-align:middle; }
.logo { width:62px; height:62px; }
.brand { padding-left:13px!important; }
.brand strong { display:block; color:#b51218; font-size:18px; }
.brand span,.meta { color:#667085; font-size:8.5px; letter-spacing:.05em; }
.meta { text-align:right; }
.title { margin:24px 0 3px; font-size:22px; text-align:center; }
.subtitle { margin:0 0 20px; color:#667085; text-align:center; }
.amount { margin-bottom:20px; padding:20px; background:#fff7f7; border:1px solid #f3c7c9; text-align:center; }
.amount-label { color:#667085; font-size:9px; font-weight:bold; letter-spacing:.08em; text-transform:uppercase; }
.amount-value { margin:5px 0; color:#991b1b; font-size:30px; font-weight:bold; }
.status { display:inline-block; padding:4px 10px; color:#067647; background:#ecfdf3; border-radius:12px; font-size:8px; font-weight:bold; text-transform:uppercase; }
.status.pending { color:#b54708; background:#fffaeb; }
.status.rejected { color:#b42318; background:#fef3f2; }
.section { margin-top:18px; page-break-inside:avoid; }
.section-title { margin:0 0 8px; color:#991b1b; font-size:11px; letter-spacing:.04em; text-transform:uppercase; }
.details { width:100%; border-collapse:collapse; }
.details td { padding:9px 10px; border-bottom:1px solid #e4e7ec; }
.details td:first-child { width:36%; color:#667085; }
.details td:last-child { font-weight:bold; text-align:right; }
.notice { margin-top:20px; padding:12px 14px; color:#475467; background:#f8fafc; border-left:4px solid #d71920; }
.footer { position:fixed; right:0; bottom:-18px; left:0; color:#98a2b3; font-size:8px; text-align:center; }
</style>
</head>
<body>
@php
    $loan = $payment->loan;
    $client = $loan?->user ?? $payment->user;
    $paidAt = ($payment->paid_at ?? $payment->created_at)?->copy()->timezone('Asia/Manila');
    $reference = $payment->provider_reference ?: $payment->reference ?: 'Payment #'.$payment->id;
    $statusClass = in_array($payment->status, ['pending', 'rejected'], true) ? $payment->status : '';
@endphp
<table class="header"><tr>
<td style="width:62px">@if($logoDataUri)<img class="logo" src="{{ $logoDataUri }}" alt="Inkcredible Lending logo">@endif</td>
<td class="brand"><strong>Inkcredible Lending</strong><span>LENDING MANAGEMENT SYSTEM</span></td>
<td class="meta">RECEIPT #{{ str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT) }}<br>{{ $paidAt?->format('F j, Y · g:i A') }} PHT</td>
</tr></table>
<h1 class="title">Official Payment Receipt</h1>
<p class="subtitle">Payment record for {{ $loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</p>
<div class="amount"><div class="amount-label">Amount</div><div class="amount-value">PHP {{ number_format((float) $payment->amount, 2) }}</div><span class="status {{ $statusClass }}">{{ strtoupper($payment->status) }}</span></div>
<div class="section"><h2 class="section-title">Transaction details</h2><table class="details">
<tr><td>Reference number</td><td>{{ $reference }}</td></tr>
<tr><td>Transaction ID</td><td>#{{ $payment->id }}</td></tr>
<tr><td>Payment date</td><td>{{ $paidAt?->format('F j, Y · g:i A') ?? 'Date unavailable' }} PHT</td></tr>
<tr><td>Payment method</td><td>{{ $payment->method_label }}</td></tr>
<tr><td>Currency</td><td>{{ strtoupper($payment->currency ?: 'PHP') }}</td></tr>
</table></div>
<div class="section"><h2 class="section-title">Account details</h2><table class="details">
<tr><td>Client</td><td>{{ $client?->full_name ?? $client?->name ?? 'Unknown client' }}</td></tr>
<tr><td>Loan account</td><td>{{ $loan?->loan_code ?: 'Loan #'.$payment->loan_id }}</td></tr>
<tr><td>Loan product</td><td>{{ $loan?->loanType?->display_name ?? $loan?->loanType?->name ?? 'Loan' }}</td></tr>
</table></div>
<div class="notice">@if($payment->status === 'approved')This payment was confirmed and applied to the associated loan account. Keep this receipt for your records.@elseif($payment->status === 'pending')This payment is awaiting confirmation and is not proof of final settlement.@else This payment was rejected, and no amount was applied to the associated loan account.@endif</div>
<div class="footer">Inkcredible Lending · CM Recto St., Davao City · Generated {{ now('Asia/Manila')->format('F j, Y g:i A') }} PHT</div>
</body>
</html>
