<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Contract | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.client-portal-styles')
.contract-layout { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:20px; }
.contract-summary { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
.contract-item { padding:14px; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; }
.contract-label { margin-bottom:5px; color:#667085; font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
.contract-value { font-size:13px; font-weight:600; }
.terms-list { margin:0; padding-left:20px; color:#475467; font-size:12px; line-height:1.7; }
.sign-box { display:grid; gap:14px; }
.accept-row { display:flex; align-items:flex-start; gap:9px; color:#475467; font-size:11px; line-height:1.5; }
.signed-note { padding:14px; color:#05603a; background:#ecfdf3; border:1px solid #abefc6; border-radius:10px; font-size:12px; line-height:1.6; }
.workflow-steps { display:grid; gap:10px; margin-bottom:16px; }
.workflow-step { display:flex; align-items:flex-start; gap:10px; color:#475467; font-size:11px; line-height:1.5; }
.step-number { display:grid; place-items:center; width:24px; height:24px; flex:0 0 24px; color:#4338ca; background:#eef2ff; border-radius:50%; font-size:10px; font-weight:700; }
.file-upload { display:block; padding:18px; color:#475467; background:#f8fafc; border:1px dashed #98a2b3; border-radius:10px; text-align:center; cursor:pointer; }
.file-upload:hover { background:#f5f3ff; border-color:#6366f1; }
.file-upload strong { display:block; margin-bottom:4px; color:#344054; font-size:12px; }
.file-upload input { width:100%; margin-top:12px; font-size:11px; }
.file-selection { display:none; align-items:center; gap:7px; padding:10px 12px; color:#067647; background:#ecfdf3; border:1px solid #abefc6; border-radius:9px; font-size:11px; font-weight:600; overflow-wrap:anywhere; }
.file-selection.visible { display:flex; }
.file-selection-mark { display:grid; place-items:center; width:20px; height:20px; flex:0 0 20px; color:#fff; background:#12b76a; border-radius:50%; }
@media(max-width:900px){.contract-layout{grid-template-columns:1fr}.contract-summary{grid-template-columns:1fr}}
</style>
@vite('resources/js/app.js')
</head>
<body>
@include('partials.client-sidebar', ['active' => 'dashboard'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
<header class="topbar"><div><div class="eyebrow">Client contract signing</div><h1>Loan contract</h1><p class="subtitle">Download the PDF, sign it, then return the signed PDF for administrator review.</p></div><div class="top-actions"><a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a><a class="button button-primary" href="{{ route('loan.contract.download', $loan) }}">Download unsigned PDF</a></div></header>
<x-flash-messages />
@if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

<div class="contract-layout">
<div>
<section class="panel"><div class="panel-header"><div><h2 class="panel-title">Contract summary</h2><p class="panel-description">Contract {{ $loan->loan_code ?: '#'.$loan->id }}</p></div></div><div class="panel-body">
<div class="contract-summary">
<div class="contract-item"><div class="contract-label">Borrower</div><div class="contract-value">{{ $loan->user->full_name }}</div></div>
<div class="contract-item"><div class="contract-label">Loan product</div><div class="contract-value">{{ $loan->loanType?->display_name }}</div></div>
<div class="contract-item"><div class="contract-label">Principal</div><div class="contract-value">PHP {{ number_format((float)$loan->amount, 2) }}</div></div>
<div class="contract-item"><div class="contract-label">Total payable</div><div class="contract-value">PHP {{ number_format((float)$loan->total_payable, 2) }}</div></div>
<div class="contract-item"><div class="contract-label">Repayment</div><div class="contract-value">{{ $loan->installment_count }} installment(s) / {{ $loan->repayment_period_days }} days</div></div>
<div class="contract-item"><div class="contract-label">Status</div><div class="contract-value">{{ $loan->signed_contract_path ? 'Signed PDF returned - awaiting administrator review' : 'Awaiting your signed PDF' }}</div></div>
</div></div></section>
<section class="panel"><div class="panel-header"><div><h2 class="panel-title">Important terms</h2><p class="panel-description">The downloadable PDF contains the complete agreement and borrower signature line.</p></div></div><div class="panel-body"><ol class="terms-list"><li>Your submitted identity and income information must remain accurate.</li><li>The repayment schedule starts only after final administrator approval.</li><li>Overdue unpaid installments may receive the penalty described in the accepted loan terms.</li><li>Sign the PDF using a valid digital signature, or print, sign, and scan it back to PDF.</li><li>Keep a copy of the signed contract for your records.</li></ol></div></section>
</div>
<aside><section class="panel"><div class="panel-header"><div><h2 class="panel-title">Return signed contract</h2><p class="panel-description">Only the client can submit the signed PDF.</p></div></div><div class="panel-body">
@if($loan->signed_contract_path)
<div class="signed-note"><strong>Signed PDF sent to the administrator</strong><br>{{ $loan->signed_contract_original_name }}<br>{{ $loan->contract_signed_at->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</div>
<a class="button button-secondary" style="width:100%;margin-top:12px" href="{{ route('loan.contract.signed.download', $loan) }}">Download submitted signed PDF</a>
@else
<div class="workflow-steps"><div class="workflow-step"><span class="step-number">1</span><span>Download the unsigned contract using the button above.</span></div><div class="workflow-step"><span class="step-number">2</span><span>Add your signature on the borrower signature line and save the signed document as a PDF.</span></div><div class="workflow-step"><span class="step-number">3</span><span>Upload the signed PDF below to return it to the administrator.</span></div></div>
<form class="sign-box" method="POST" action="{{ route('loan.contract.sign', $loan) }}" enctype="multipart/form-data" data-confirm="Send this signed PDF to the administrator for final review?">@csrf
<label class="file-upload" for="signed_contract"><strong>Select your signed contract</strong><span>PDF only, maximum 10 MB</span><input id="signed_contract" type="file" name="signed_contract" accept="application/pdf,.pdf" data-signed-contract-input required></label>
<div class="file-selection" data-signed-contract-selection><span class="file-selection-mark">&#10003;</span><span data-signed-contract-name></span></div>
<label class="accept-row"><input type="checkbox" name="contract_accepted" value="1" required><span>I confirm that I signed this contract and that the uploaded PDF is the document I want to return for final review.</span></label>
<button class="button button-primary" type="submit">Send signed PDF to administrator</button>
</form>
@endif
</div></section></aside>
</div></div></main>
<script>
const signedContractInput = document.querySelector('[data-signed-contract-input]');
signedContractInput?.addEventListener('change', () => {
    const selection = document.querySelector('[data-signed-contract-selection]');
    const name = document.querySelector('[data-signed-contract-name]');
    const file = signedContractInput.files?.[0];
    if (name) name.textContent = file?.name ?? '';
    selection?.classList.toggle('visible', Boolean(file));
});
</script>
</body></html>
