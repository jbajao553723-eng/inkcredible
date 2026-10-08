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
@include('partials.pdf-styles')
.amount { padding:22px; background:#172033; border:0; text-align:left; }
.amount-label { color:#d0d5dd; }
.amount-value { color:#fff; font-size:34px; }
.amount .status { margin-top:5px; }
.details { table-layout:fixed; }
.details td { padding:12px 10px; word-wrap:break-word; }
.details td:first-child { width:33%; }
.details td:last-child { text-align:left; }
.section-title { color:#172033; }
.receipt-reference { margin-top:16px; padding:12px; border:1px solid #d7dce4; }
.receipt-reference strong { display:block; margin-top:3px; word-wrap:break-word; }
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
@include('partials.pdf-header', [
    'documentReference' => 'RCPT-'.str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT),
    'documentDate' => $paidAt?->format('M d, Y - h:i A') ?? 'Date unavailable',
    'documentCategory' => 'Payment records',
    'documentTitle' => $payment->status === 'approved' ? 'Payment receipt' : 'Payment status record',
    'documentSubtitle' => 'Account: '.($loan?->loan_code ?: 'Loan #'.$payment->loan_id).' | Keep a copy for your records.',
])
<div class="amount"><div class="amount-label">Amount</div><div class="amount-value">PHP {{ number_format((float) $payment->amount, 2) }}</div><span class="status {{ $statusClass }}">{{ strtoupper($payment->status) }}</span></div>
<div class="section"><h2 class="section-title">Transaction details</h2><table class="details">
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
<div class="receipt-reference"><span class="amount-label" style="color:#667085">Transaction reference</span><strong>{{ $reference }}</strong></div>
<div class="notice">@if($payment->status === 'approved')This payment was confirmed and applied to the associated loan account. Keep this receipt for your records.@elseif($payment->status === 'pending')This payment is awaiting confirmation and is not proof of final settlement.@else This payment was rejected, and no amount was applied to the associated loan account.@endif</div>
@include('partials.pdf-footer', ['documentFooter' => 'Payment #'.$payment->id.' | Generated '.now('Asia/Manila')->format('Y-m-d h:i A').' PHT'])
</body>
</html>
