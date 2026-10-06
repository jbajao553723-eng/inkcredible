<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loan Contract | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.client-portal-styles')
.contract-hero { position:relative; display:flex; align-items:center; justify-content:space-between; gap:24px; margin-bottom:20px; padding:24px 26px; overflow:hidden; color:#fff; background:linear-gradient(135deg,#312e81 0%,#4f46e5 58%,#7c3aed 100%); border-radius:18px; box-shadow:0 18px 38px rgba(79,70,229,.18); }
.contract-hero::after { position:absolute; right:-45px; bottom:-90px; width:230px; height:230px; background:radial-gradient(circle,rgba(255,255,255,.2),transparent 68%); border-radius:50%; content:''; }
.contract-hero-copy { position:relative; z-index:1; }
.contract-hero-label { margin-bottom:7px; color:#c7d2fe; font-size:10px; font-weight:700; letter-spacing:.11em; text-transform:uppercase; }
.contract-hero h2 { margin:0; font-size:22px; letter-spacing:-.025em; }
.contract-hero p { max-width:620px; margin:8px 0 0; color:#e0e7ff; font-size:12px; line-height:1.6; }
.contract-hero-actions { position:relative; z-index:1; display:grid; justify-items:end; gap:10px; min-width:190px; }
.contract-status { position:relative; z-index:1; display:flex; align-items:center; gap:9px; min-width:max-content; padding:9px 12px; color:#fff; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.24); border-radius:999px; font-size:11px; font-weight:700; backdrop-filter:blur(8px); }
.contract-status-dot { width:8px; height:8px; background:#fbbf24; border:2px solid rgba(255,255,255,.4); border-radius:50%; box-sizing:content-box; }
.contract-status.complete .contract-status-dot { background:#6ee7b7; }
.contract-download { width:100%; color:#3730a3; background:#fff; border-color:#fff; box-shadow:0 10px 24px rgba(30,27,75,.22); }
.contract-download:hover { color:#312e81; background:#f8fafc; transform:translateY(-1px); }
.contract-download svg { width:17px; height:17px; }
.contract-layout { display:grid; grid-template-columns:minmax(0,1fr) 380px; gap:20px; align-items:start; }
.contract-stack { display:grid; gap:20px; }
.contract-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
.contract-item { min-width:0; padding:16px; background:#f8fafc; border:1px solid #eaecf0; border-radius:12px; }
.contract-item.emphasis { background:linear-gradient(145deg,#eef2ff,#f5f3ff); border-color:#c7d2fe; }
.contract-label { margin-bottom:5px; color:#667085; font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
.contract-value { color:#344054; font-size:13px; font-weight:700; line-height:1.4; overflow-wrap:anywhere; }
.contract-item.emphasis .contract-value { color:#3730a3; font-size:15px; }
.client-assessment { display:grid; grid-template-columns:180px minmax(0,1fr); gap:20px; align-items:center; }
.assessment-score { padding:18px; text-align:center; background:#f8fafc; border:1px solid #eaecf0; border-radius:13px; }
.assessment-score-value { color:#344054; font-size:28px; font-weight:700; letter-spacing:-.035em; }
.assessment-score-label { margin-top:4px; color:#667085; font-size:10px; }
.assessment-level { display:inline-flex; margin-top:10px; padding:5px 8px; border-radius:999px; font-size:9px; font-weight:700; }
.assessment-level.success { color:#067647; background:#ecfdf3; }.assessment-level.warning { color:#b54708; background:#fffaeb; }.assessment-level.danger { color:#b42318; background:#fef3f2; }.assessment-level.neutral { color:#475467; background:#f2f4f7; }
.assessment-details { min-width:0; }
.assessment-track { height:7px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.assessment-track span { display:block; height:100%; background:#6366f1; border-radius:inherit; }
.assessment-formula { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; margin-top:14px; }
.assessment-formula-item { min-width:0; padding:10px; background:#f8fafc; border-radius:9px; }
.assessment-formula-item span { display:block; color:#667085; font-size:9px; line-height:1.35; }
.assessment-formula-item strong { display:block; margin-top:4px; color:#344054; font-size:11px; overflow-wrap:anywhere; }
.assessment-guidance { margin:13px 0 0; color:#475467; font-size:11px; line-height:1.55; }
.assessment-note { margin:8px 0 0; color:#98a2b3; font-size:9px; line-height:1.45; }
.terms-list { display:grid; gap:11px; margin:0; padding:0; color:#475467; font-size:12px; line-height:1.6; list-style:none; counter-reset:terms; }
.terms-list li { position:relative; padding-left:34px; counter-increment:terms; }
.terms-list li::before { position:absolute; top:0; left:0; display:grid; place-items:center; width:22px; height:22px; color:#4f46e5; background:#eef2ff; border-radius:7px; font-size:10px; font-weight:700; content:counter(terms); }
.contract-sidebar { position:sticky; top:24px; }
.upload-panel { border-color:#d9d6fe; box-shadow:0 12px 30px rgba(79,70,229,.08); }
.upload-panel .panel-header { background:linear-gradient(145deg,#fafaff,#f5f3ff); }
.download-block { margin-bottom:18px; padding:14px; background:#eef2ff; border:1px solid #c7d2fe; border-radius:12px; }
.download-block-label { margin-bottom:8px; color:#4338ca; font-size:10px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; }
.download-block .button { width:100%; }
.download-block-note { margin:8px 0 0; color:#667085; font-size:10px; line-height:1.45; text-align:center; }
.sign-box { display:grid; gap:14px; }
.accept-row { display:flex; align-items:flex-start; gap:10px; padding:12px; color:#475467; background:#f8fafc; border-radius:10px; font-size:11px; line-height:1.5; }
.accept-row input { width:16px; height:16px; flex:0 0 16px; margin-top:1px; accent-color:#4f46e5; }
.signed-note { padding:18px; color:#05603a; background:linear-gradient(145deg,#ecfdf3,#f6fef9); border:1px solid #abefc6; border-radius:12px; font-size:12px; line-height:1.6; }
.signed-note strong { display:block; margin-bottom:5px; font-size:13px; }
.stored-signature { padding:15px; background:#fff; border:1px solid #d9d6fe; border-radius:12px; text-align:center; }
.stored-signature img { display:block; width:100%; max-width:250px; height:78px; margin:0 auto 7px; object-fit:contain; }
.stored-signature span { color:#667085; font-size:9px; }
.signature-missing { padding:16px; color:#7a2e0e; background:#fffaeb; border:1px solid #fedf89; border-radius:12px; font-size:11px; line-height:1.55; }
.signature-missing strong { display:block; margin-bottom:4px; color:#93370d; font-size:12px; }
.workflow-steps { display:grid; margin-bottom:18px; }
.workflow-step { position:relative; display:flex; align-items:flex-start; gap:11px; padding-bottom:16px; color:#475467; font-size:11px; line-height:1.5; }
.workflow-step:not(:last-child)::after { position:absolute; top:26px; bottom:4px; left:12px; width:1px; background:#d9d6fe; content:''; }
.workflow-step:last-child { padding-bottom:0; }
.step-number { position:relative; z-index:1; display:grid; place-items:center; width:26px; height:26px; flex:0 0 26px; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:50%; font-size:10px; font-weight:700; }
.workflow-step strong { display:block; margin-bottom:2px; color:#344054; font-size:11px; }
.file-upload { display:block; padding:22px 18px; color:#667085; background:#fafaff; border:1.5px dashed #a5b4fc; border-radius:12px; text-align:center; cursor:pointer; transition:.18s ease; }
.file-upload:hover { background:#f5f3ff; border-color:#6366f1; transform:translateY(-1px); }
.upload-icon { display:grid; place-items:center; width:38px; height:38px; margin:0 auto 10px; color:#4f46e5; background:#eef2ff; border-radius:10px; }
.upload-icon svg { width:19px; height:19px; }
.file-upload strong { display:block; margin-bottom:4px; color:#344054; font-size:12px; }
.file-upload span { font-size:10px; }
.file-upload input { width:100%; margin-top:14px; padding:7px; color:#475467; background:#fff; border:1px solid #e4e7ec; border-radius:8px; font-size:10px; }
.file-selection { display:none; align-items:center; gap:7px; padding:10px 12px; color:#067647; background:#ecfdf3; border:1px solid #abefc6; border-radius:9px; font-size:11px; font-weight:600; overflow-wrap:anywhere; }
.file-selection.visible { display:flex; }
.file-selection-mark { display:grid; place-items:center; width:20px; height:20px; flex:0 0 20px; color:#fff; background:#12b76a; border-radius:50%; }
.sign-box > .button { width:100%; }
@media(max-width:1000px){.contract-layout{grid-template-columns:1fr}.contract-sidebar{position:static}.contract-summary{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.contract-hero{align-items:flex-start;flex-direction:column;padding:21px}.contract-hero-actions{width:100%;justify-items:stretch}.contract-summary{grid-template-columns:1fr}.contract-status{min-width:0;justify-content:center}.contract-hero h2{font-size:19px}.client-assessment{grid-template-columns:1fr}.assessment-formula{grid-template-columns:1fr}}
</style>
@vite('resources/js/app.js')
</head>
<body>
@php
    $contractReturned = filled($loan->signed_contract_path);
    $verification = $loan->user->clientVerification;
    $hasDigitalSignature = filled($verification?->digital_signature);
@endphp
@include('partials.client-sidebar', ['active' => 'dashboard'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
<header class="topbar"><div><div class="eyebrow">Final application step</div><h1>Loan contract</h1><p class="subtitle">Review the agreement, apply your verified signature, and submit it for final approval.</p></div><div class="top-actions"><a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a></div></header>
<x-flash-messages />
@if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

<section class="contract-hero">
<div class="contract-hero-copy"><div class="contract-hero-label">{{ $loan->loan_code ?: 'Loan #'.$loan->id }}</div><h2>{{ $contractReturned ? 'Your signed contract has been submitted' : 'Your agreement is ready to sign' }}</h2><p>{{ $contractReturned ? 'The administrator will verify the digitally signed PDF before making the final loan decision.' : 'Your verified signature can be applied automatically—no download, manual signing, or upload is required.' }}</p></div>
<div class="contract-hero-actions"><div class="contract-status {{ $contractReturned ? 'complete' : '' }}"><span class="contract-status-dot"></span>{{ $contractReturned ? 'Submitted for review' : 'Action required' }}</div><a class="button contract-download" href="{{ route('loan.contract.download', $loan) }}" download="{{ $loan->loan_code ?: 'loan-'.$loan->id }}-contract.pdf" data-no-transition><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12m0 0 5-5m-5 5-5-5M5 20h14"/></svg>Download PDF</a></div>
</section>

<div class="contract-layout">
<div class="contract-stack">
<section class="panel"><div class="panel-header"><div><h2 class="panel-title">Contract summary</h2><p class="panel-description">Contract {{ $loan->loan_code ?: '#'.$loan->id }}</p></div></div><div class="panel-body">
<div class="contract-summary">
<div class="contract-item"><div class="contract-label">Borrower</div><div class="contract-value">{{ $loan->user->full_name }}</div></div>
<div class="contract-item"><div class="contract-label">Loan product</div><div class="contract-value">{{ $loan->loanType?->display_name }}</div></div>
<div class="contract-item emphasis"><div class="contract-label">Principal</div><div class="contract-value">&#8369;{{ number_format((float)$loan->amount, 2) }}</div></div>
<div class="contract-item"><div class="contract-label">Finance fee (5%)</div><div class="contract-value">&#8369;{{ number_format((float)$loan->finance_fee, 2) }}</div></div>
<div class="contract-item"><div class="contract-label">Processing fee</div><div class="contract-value">&#8369;{{ number_format((float)$loan->processing_fee, 2) }}</div></div>
<div class="contract-item emphasis"><div class="contract-label">Total payable</div><div class="contract-value">&#8369;{{ number_format((float)$loan->total_payable, 2) }}</div></div>
<div class="contract-item"><div class="contract-label">Repayment</div><div class="contract-value">{{ $loan->installment_count }} installment(s) / {{ $loan->repayment_period_days }} days</div></div>
<div class="contract-item"><div class="contract-label">Current stage</div><div class="contract-value">{{ $contractReturned ? 'Administrator review' : 'Borrower signature' }}</div></div>
</div></div></section>
<section class="panel"><div class="panel-header"><div><h2 class="panel-title">Your affordability assessment</h2><p class="panel-description">The same calculation visible during administrator review.</p></div></div><div class="panel-body client-assessment">
<div class="assessment-score"><div class="assessment-score-value">{{ $riskAssessment['isAssessed'] ? $riskAssessment['readinessScore'] : '—' }}</div><div class="assessment-score-label">{{ $riskAssessment['isAssessed'] ? 'Readiness score / 100' : 'Verification required' }}</div><span class="assessment-level {{ $riskAssessment['tone'] }}">{{ $riskAssessment['isAssessed'] ? $riskAssessment['level'].' risk' : $riskAssessment['level'] }}</span></div>
<div class="assessment-details"><div class="assessment-track" aria-label="{{ $riskAssessment['isAssessed'] ? $riskAssessment['readinessScore'].' out of 100 readiness score' : 'Readiness is not assessed' }}"><span style="width:{{ $riskAssessment['readinessScore'] ?? 0 }}%"></span></div><div class="assessment-formula"><div class="assessment-formula-item"><span>Affordability ratio</span><strong>{{ $riskAssessment['ratio'] === null ? 'Unavailable' : number_format($riskAssessment['ratio'], 1).'%' }}</strong></div><div class="assessment-formula-item"><span>Verified payslip</span><strong>{{ $riskAssessment['verifiedPayslip'] ? 'Yes · score bonus' : 'Not yet' }}</strong></div><div class="assessment-formula-item"><span>Payment behavior</span><strong>{{ $riskAssessment['earlyPayments'] }} early · {{ $riskAssessment['latePayments'] }} late</strong></div></div><p class="assessment-guidance">{{ $riskAssessment['suggestion'] }}</p><p class="assessment-note">Early and on-time repayment can improve future assessments; late payments reduce the score. This guide does not guarantee approval.</p></div>
</div></section>
<section class="panel"><div class="panel-header"><div><h2 class="panel-title">Before you sign</h2><p class="panel-description">Confirm the agreement details before applying your stored signature.</p></div></div><div class="panel-body"><ol class="terms-list"><li>Your submitted identity and income information must remain accurate.</li><li>The repayment schedule starts only after final administrator approval.</li><li>Overdue unpaid installments may receive the penalty described in the accepted loan terms.</li><li>Pressing the signing button applies the digital signature captured during verification to this agreement.</li><li>A signed PDF is generated automatically and retained for you and the administrator.</li></ol></div></section>
</div>
<aside class="contract-sidebar"><section class="panel upload-panel"><div class="panel-header"><div><h2 class="panel-title">{{ $contractReturned ? 'Submission received' : 'Digital contract signing' }}</h2><p class="panel-description">{{ $contractReturned ? 'Your document is securely on file.' : 'Apply your verified signature in one step.' }}</p></div></div><div class="panel-body">
@if($contractReturned)
<div class="signed-note"><strong>Digitally signed PDF sent to the administrator</strong>{{ $loan->signed_contract_original_name }}<br>Submitted {{ $loan->contract_signed_at->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</div>
<a class="button button-secondary" style="width:100%;margin-top:12px" href="{{ route('loan.contract.signed.download', $loan) }}" download="{{ $loan->loan_code ?: 'loan-'.$loan->id }}-signed-contract.pdf" data-no-transition>Download submitted signed PDF</a>
@elseif(! $hasDigitalSignature)
<div class="signature-missing"><strong>Digital signature required</strong>Your verified profile does not yet have a signature. Add it once in verification settings, then return here to sign this contract.</div>
<a class="button button-primary" style="width:100%;margin-top:14px" href="{{ route('profile.verification.edit') }}">Add digital signature</a>
@else
<div class="stored-signature"><img src="{{ $verification->digital_signature }}" alt="Your stored digital signature"><span>Verified signature captured {{ $verification->signature_captured_at?->timezone('Asia/Manila')->format('M d, Y') }}</span></div>
<form class="sign-box" style="margin-top:14px" method="POST" action="{{ route('loan.contract.sign', $loan) }}" data-confirm="Apply your verified digital signature and send this contract to the administrator for final approval?">@csrf
<label class="accept-row"><input type="checkbox" name="contract_accepted" value="1" required><span>I reviewed this agreement and authorize Inkcredible to apply my stored digital signature to this contract.</span></label>
<button class="button button-primary" type="submit">Sign contract and send to admin</button>
</form>
@endif
</div></section></aside>
</div></div></main>
@include('partials.client-confirmation')
</body></html>
