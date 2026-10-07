<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Loan Agreement - {{ $loan->loan_code ?: $loan->id }}</title>
<style>
@page { margin:34px 44px 42px; }
* { box-sizing:border-box; }
body { margin:0; color:#172033; font-family:DejaVu Sans,sans-serif; font-size:9.4px; line-height:1.48; }
.header { width:100%; padding-bottom:13px; border-bottom:3px solid #d71920; }
.header td { padding:0; vertical-align:middle; }
.logo { width:62px; height:62px; }
.brand { padding-left:13px!important; }
.brand strong { display:block; color:#b51218; font-size:18px; }
.brand span,.meta { color:#667085; font-size:8px; letter-spacing:.05em; }
.meta { text-align:right; }
h1 { margin:19px 0 3px; font-size:19px; text-align:center; }
.subtitle { margin:0 0 15px; color:#667085; text-align:center; }
.reference { margin-bottom:14px; padding:8px 11px; color:#7f1d1d; background:#fff7f7; border:1px solid #f3c7c9; font-weight:bold; text-align:center; }
.section { margin-top:14px; page-break-inside:avoid; }
.section-title { margin:0 0 6px; padding-bottom:4px; color:#991b1b; border-bottom:1px solid #f0b7ba; font-size:11px; letter-spacing:.03em; text-transform:uppercase; }
.details,.financials,.signature-grid { width:100%; border-collapse:collapse; }
.details td { padding:6px 8px; border-bottom:1px solid #e4e7ec; vertical-align:top; }
.details td:nth-child(odd) { width:19%; color:#667085; }
.details td:nth-child(even) { width:31%; font-weight:bold; }
.financials th { padding:6px 8px; color:#667085; background:#f8fafc; border:1px solid #e4e7ec; font-size:8px; text-align:left; text-transform:uppercase; }
.financials td { padding:7px 8px; border:1px solid #e4e7ec; }
.financials td:last-child { font-weight:bold; text-align:right; }
.financials .total td { color:#7f1d1d; background:#fff7f7; font-size:10px; font-weight:bold; }
.terms { margin:0; padding-left:18px; }
.terms li { margin:0 0 5px; padding-left:3px; }
.notice { margin-top:12px; padding:9px 11px; color:#475467; background:#f8fafc; border-left:4px solid #d71920; }
.signature-section { margin-top:22px; page-break-inside:avoid; }
.signature-grid td { vertical-align:bottom; }
.signature-cell { width:42%; height:106px; padding:0 8px 6px; border-bottom:1px solid #344054; text-align:center; }
.signature-image { display:block; width:160px; height:52px; margin:0 auto 2px; }
.signed { color:#067647; font-size:8px; font-weight:bold; text-transform:uppercase; }
.signature-name { margin-top:4px; font-weight:bold; }
.signature-role,.signature-time { color:#667085; font-size:8px; }
.footer { position:fixed; right:0; bottom:-24px; left:0; color:#98a2b3; font-size:7.5px; text-align:center; }
</style>
</head>
<body>
<table class="header"><tr>
<td style="width:62px">@if(! empty($logoDataUri))<img class="logo" src="{{ $logoDataUri }}" alt="Inkcredible Lending logo">@endif</td>
<td class="brand"><strong>Inkcredible Lending</strong><span>LENDING MANAGEMENT SYSTEM</span></td>
<td class="meta">LOAN AGREEMENT<br>Issued {{ $loan->contract_sent_at?->timezone('Asia/Manila')->format('F j, Y') }}</td>
</tr></table>
<h1>Loan Agreement</h1>
<p class="subtitle">Please review every provision and retain a downloaded copy for your records.</p>
<div class="reference">Contract reference: {{ $loan->loan_code ?: 'LOAN-'.$loan->id }}</div>

<div class="section"><h2 class="section-title">Parties and loan account</h2><table class="details">
<tr><td>Borrower</td><td>{{ $loan->user->full_name }}</td><td>Loan product</td><td>{{ $loan->loanType?->display_name ?? 'Loan' }}</td></tr>
<tr><td>Address</td><td>{{ $loan->user->address ?: 'Not provided' }}</td><td>Loan reference</td><td>{{ $loan->loan_code ?: '#'.$loan->id }}</td></tr>
<tr><td>Lender</td><td>Inkcredible Lending</td><td>Purpose</td><td>{{ $loan->purpose ?: 'Not specified' }}</td></tr>
</table></div>

<div class="section"><h2 class="section-title">Financial disclosure</h2><table class="financials">
<tr><th>Description</th><th>Amount / Terms</th></tr>
<tr><td>Principal amount</td><td>PHP {{ number_format((float) $loan->amount, 2) }}</td></tr>
<tr><td>Contract interest rate</td><td>{{ number_format((float) ($loan->loanType?->interest_rate ?? $loan->interest_rate ?? 0), 2) }}%</td></tr>
<tr><td>Finance fee</td><td>PHP {{ number_format((float) $loan->finance_fee, 2) }}</td></tr>
<tr><td>Processing fee</td><td>PHP {{ number_format((float) $loan->processing_fee, 2) }}</td></tr>
<tr class="total"><td>Total amount payable</td><td>PHP {{ number_format((float) $loan->total_payable, 2) }}</td></tr>
<tr><td>Repayment schedule</td><td>{{ $loan->installment_count }} installment(s) over {{ $loan->repayment_period_days }} days</td></tr>
</table></div>

<div class="section"><h2 class="section-title">Borrower acknowledgements</h2><ol class="terms">
<li>I confirm that the personal, employment, income, and identity information supplied with my application is complete and accurate.</li>
<li>I agree to repay the total amount payable according to the repayment schedule issued following final approval.</li>
<li>I understand that overdue unpaid installments may incur the penalty disclosed in the loan terms accepted with my application.</li>
<li>I authorize Inkcredible Lending to retain this agreement, the related application, identity records, signatures, and payment records for lawful servicing and audit purposes.</li>
<li>I understand that my signature confirms acceptance of this agreement but does not itself disburse funds. Final approval remains subject to the lender's review.</li>
<li>I confirm that I received the opportunity to review the principal, charges, repayment period, total payable amount, and all terms before signing.</li>
</ol></div>

<div class="notice"><strong>Important:</strong> The approved repayment schedule, due dates, and recorded payments form part of this loan account. The borrower should promptly report any discrepancy to Inkcredible Lending.</div>

<div class="signature-section"><h2 class="section-title">Electronic signatures</h2><table class="signature-grid"><tr>
<td style="width:4%"></td><td class="signature-cell">
@if(! empty($clientSignature))<img class="signature-image" src="{{ $clientSignature }}" alt="Borrower digital signature"><div class="signed">Digitally signed</div>@else<div style="height:60px"></div>@endif
<div class="signature-name">{{ $loan->user->full_name }}</div><div class="signature-role">Borrower / Client</div>
@if(! empty($signedAt))<div class="signature-time">{{ $signedAt->timezone('Asia/Manila')->format('F j, Y · h:i A') }} PHT</div>@endif
</td><td style="width:8%"></td><td class="signature-cell">
@if(! empty($adminSignature))<img class="signature-image" src="{{ $adminSignature }}" alt="Administrator digital signature"><div class="signed">Digitally signed</div>@else<div style="height:60px"></div>@endif
<div class="signature-name">{{ $adminSignatureName ?? 'Inkcredible Authorized Representative' }}</div><div class="signature-role">Lender / Final approval</div>
@if(! empty($adminSignedAt))<div class="signature-time">{{ $adminSignedAt->timezone('Asia/Manila')->format('F j, Y · h:i A') }} PHT</div>@endif
</td><td style="width:4%"></td>
</tr></table></div>

<div class="footer">Inkcredible Lending · CM Recto St., Davao City · Contract {{ $loan->loan_code ?: '#'.$loan->id }} · Generated {{ now('Asia/Manila')->format('F j, Y g:i A') }} PHT</div>
</body>
</html>
