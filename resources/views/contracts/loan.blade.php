<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Loan Agreement - {{ $loan->loan_code }}</title>
<style>
@page { margin: 42px 48px; }
body { color:#172033; font-family: DejaVu Sans, sans-serif; font-size:10.5px; line-height:1.55; }
.header { padding-bottom:16px; border-bottom:2px solid #4f46e5; }
.brand { color:#312e81; font-size:22px; font-weight:bold; }
.meta { float:right; color:#667085; text-align:right; }
h1 { margin:24px 0 5px; font-size:20px; text-align:center; }
.subtitle { margin:0 0 22px; color:#667085; text-align:center; }
.box { margin:15px 0; padding:14px; background:#f8f9fc; border:1px solid #dfe3f0; border-radius:6px; }
table { width:100%; border-collapse:collapse; }
td { padding:6px 8px; vertical-align:top; }
td:first-child { width:35%; color:#667085; }
h2 { margin:20px 0 8px; color:#312e81; font-size:13px; }
p { margin:7px 0; }
.signature-grid { width:100%; margin-top:45px; }
.signature-cell { width:46%; padding-top:35px; border-top:1px solid #344054; text-align:center; }
.signature-name { margin-top:5px; font-weight:bold; }
.signed { color:#067647; font-size:13px; font-style:italic; }
.footer { position:fixed; right:0; bottom:-20px; left:0; color:#98a2b3; font-size:8px; text-align:center; }
</style>
</head>
<body>
<div class="header"><span class="brand">Inkcredible</span><span class="meta">Contract {{ $loan->loan_code ?: '#'.$loan->id }}<br>Issued {{ $loan->contract_sent_at?->timezone('Asia/Manila')->format('F j, Y') }}</span></div>
<h1>Loan Agreement</h1>
<p class="subtitle">Please retain a downloaded copy of this agreement for your records.</p>

<div class="box"><table>
<tr><td>Borrower</td><td><strong>{{ $loan->user->full_name }}</strong></td></tr>
<tr><td>Address</td><td>{{ $loan->user->address }}</td></tr>
<tr><td>Loan product</td><td>{{ $loan->loanType?->display_name ?? 'Loan' }}</td></tr>
<tr><td>Principal amount</td><td>PHP {{ number_format((float) $loan->amount, 2) }}</td></tr>
<tr><td>Interest rate</td><td>{{ number_format((float) ($loan->loanType?->interest_rate ?? 0), 2) }}%</td></tr>
<tr><td>Total payable</td><td><strong>PHP {{ number_format((float) $loan->total_payable, 2) }}</strong></td></tr>
<tr><td>Repayment term</td><td>{{ $loan->installment_count }} installment(s) over {{ $loan->repayment_period_days }} days</td></tr>
<tr><td>Purpose</td><td>{{ $loan->purpose ?: 'Not specified' }}</td></tr>
</table></div>

<h2>Borrower acknowledgements</h2>
<p>1. I confirm that the personal, employment, income, and identity information supplied with my application is accurate.</p>
<p>2. I agree to repay the total payable amount according to the schedule issued after final approval.</p>
<p>3. I understand that overdue unpaid installments may receive the penalty stated in the loan terms accepted with my application.</p>
<p>4. I authorize Inkcredible to maintain this agreement, the related application, and payment records for servicing and audit purposes.</p>
<p>5. I understand that signing this agreement does not itself disburse funds. Final approval remains subject to administrator review.</p>

<table class="signature-grid"><tr>
<td style="width:8%"></td>
<td class="signature-cell">
<div>&nbsp;</div>
<div class="signature-name">{{ $loan->user->full_name }}</div>
<div>Borrower / Client</div>
</td>
<td style="width:8%"></td>
<td class="signature-cell"><div>&nbsp;</div><div class="signature-name">Inkcredible Authorized Representative</div><div>Final approval</div></td>
<td style="width:8%"></td>
</tr></table>

<div class="footer">Inkcredible Lending Management System - generated {{ now('Asia/Manila')->format('F j, Y h:i A') }} PHT</div>
</body>
</html>
