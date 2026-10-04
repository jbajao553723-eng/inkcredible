<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Verification | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style>
@include('partials.client-portal-styles')
@include('partials.settings-styles')
.signature-capture { grid-column:1/-1; margin-top:18px; padding:20px; background:linear-gradient(145deg,#fafaff,#f5f3ff); border:1px solid #d9d6fe; border-radius:14px; }
.signature-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:14px; }
.signature-heading h3 { margin:0; color:#344054; font-size:14px; }
.signature-heading p { margin:5px 0 0; color:#667085; font-size:10px; line-height:1.5; }
.signature-security { padding:5px 8px; color:#067647; background:#ecfdf3; border-radius:999px; font-size:9px; font-weight:700; white-space:nowrap; }
.signature-existing { display:flex; align-items:center; gap:14px; margin-bottom:13px; padding:11px; color:#475467; background:#fff; border:1px solid #eaecf0; border-radius:10px; font-size:10px; line-height:1.45; }
.signature-existing img { width:150px; height:48px; flex:0 0 150px; object-fit:contain; }
.signature-canvas-wrap { position:relative; overflow:hidden; background:#fff; border:1px solid #a5b4fc; border-radius:11px; box-shadow:inset 0 1px 2px rgba(16,24,40,.05); }
.signature-canvas-wrap canvas { display:block; width:100%; height:180px; cursor:crosshair; touch-action:none; }
.signature-line { position:absolute; right:32px; bottom:38px; left:32px; height:1px; background:#d0d5dd; pointer-events:none; }
.signature-footer { display:flex; align-items:center; justify-content:space-between; gap:14px; margin-top:14px; }
.signature-footer p { margin:0; color:#667085; font-size:9px; line-height:1.45; }
.signature-footer .button { flex:0 0 auto; }
.signature-error { margin:9px 0 0; color:#b42318; font-size:10px; font-weight:600; }
.legacy-signature-form { margin-top:20px; }
.signature-form-actions { margin-top:12px; }
.income-evidence-card { margin-top:20px; padding:20px; background:#f8fafc; border:1px solid #eaecf0; border-radius:14px; }
.income-evidence-head { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; margin-bottom:14px; }
.income-evidence-head h3 { margin:0; color:#344054; font-size:13px; }
.income-evidence-head p { margin:5px 0 0; color:#667085; font-size:10px; line-height:1.5; }
.evidence-status { padding:5px 8px; border-radius:999px; font-size:9px; font-weight:700; white-space:nowrap; }
.evidence-status.verified { color:#067647; background:#ecfdf3; }.evidence-status.missing { color:#b54708; background:#fffaeb; }
.income-evidence-actions { display:flex; align-items:flex-end; gap:12px; }.income-evidence-actions .form-group { flex:1; }.income-evidence-actions .save-button { flex:0 0 auto; }
@media(max-width:620px){.signature-heading,.signature-footer,.signature-existing{align-items:stretch;flex-direction:column}.signature-existing img{width:100%;flex-basis:58px}.signature-security{align-self:flex-start}.signature-footer .button{width:100%}}
@media(max-width:620px){.income-evidence-head,.income-evidence-actions{align-items:stretch;flex-direction:column}.income-evidence-actions .save-button{width:100%}}
</style>
</head>
<body>
@php
    $verification = $user->clientVerification;
    $verificationStatus = $verification?->status ?? 'not submitted';
    $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
    $canSubmitVerification = ! $verification || $verification->status === 'rejected';
    $canCaptureLegacySignature = $verificationStatus === 'approved' && ! $verification?->digital_signature;
    $canUpdatePayslip = $verificationStatus === 'approved';
@endphp
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Client verification</h1><p class="subtitle">Submit financial and identity information for administrator review.</p></div><span class="badge {{ $verificationClass }}">{{ ucfirst($verificationStatus) }}</span></header>
    @include('partials.settings-tabs', ['activeSettings' => 'verification'])
    <section class="panel">
        <div class="panel-header"><div><h2 class="panel-title">Employment, income, and identity</h2><p class="panel-description">All information must be accurate and the documents must be clear and unedited.</p></div><span class="badge {{ $verificationClass }}">{{ ucfirst($verificationStatus) }}</span></div>
        <div class="panel-body">
            @if($verificationStatus === 'pending')
                <div class="verification-notice notice-pending"><strong>Review in progress.</strong> Your submission is locked while an administrator reviews it. Loan requests remain unavailable until approval.</div>
            @elseif($verificationStatus === 'approved')
                <div class="verification-notice notice-approved"><strong>Account fully verified.</strong> You may now request loans from the client dashboard. {{ $canCaptureLegacySignature ? 'Add your digital signature below to enable automatic contract signing.' : 'Your digital signature is securely on file.' }}</div>
            @elseif($verificationStatus === 'rejected')
                <div class="verification-notice notice-rejected"><strong>Changes required.</strong><br>{{ $verification->rejection_reason }}<br><br>Correct the information or replace the documents, then resubmit.</div>
            @else
                <div class="verification-notice notice-pending"><strong>Verification required.</strong> Complete every required field before requesting a loan.</div>
            @endif
            <x-flash-messages bag="verification" :show-errors="true" />
            @if($canSubmitVerification)
                <form method="POST" action="{{ route('profile.verification.store') }}" enctype="multipart/form-data" id="verification-form">@csrf
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label" for="employment-status">Employment status</label><select class="form-control" name="employment_status" id="employment-status" required><option value="">Select status</option>@foreach(['employed'=>'Employed','self_employed'=>'Self-employed','unemployed'=>'Unemployed','student'=>'Student','retired'=>'Retired','other'=>'Other'] as $value=>$label)<option value="{{ $value }}" @selected(old('employment_status', $verification?->employment_status) === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div class="form-group"><label class="form-label" for="monthly-income">Monthly income</label><input class="form-control" type="number" name="monthly_income" id="monthly-income" min="0" step="0.01" value="{{ old('monthly_income', $verification?->monthly_income) }}" placeholder="0.00" required></div>
                        <div class="form-group"><label class="form-label" for="company-name">Company or business</label><input class="form-control" name="company_name" id="company-name" value="{{ old('company_name', $verification?->company_name) }}" placeholder="Employer or business name"></div>
                        <div class="form-group"><label class="form-label" for="job-title">Job title or occupation</label><input class="form-control" name="job_title" id="job-title" value="{{ old('job_title', $verification?->job_title) }}" placeholder="Your role"></div>
                        <div class="form-group"><label class="form-label" for="employment-length">Employment length</label><input class="form-control" type="number" name="employment_length_months" id="employment-length" min="0" max="1200" value="{{ old('employment_length_months', $verification?->employment_length_months ?? 0) }}" required><p class="form-help">Enter the number of months.</p></div>
                        <div class="form-group"><label class="form-label" for="income-source">Primary source of income</label><input class="form-control" name="source_of_income" id="income-source" value="{{ old('source_of_income', $verification?->source_of_income) }}" required></div>
                        <div class="form-group"><label class="form-label" for="id-type">Valid ID type</label><select class="form-control" name="valid_id_type" id="id-type" required><option value="">Select ID type</option>@foreach(['Philippine National ID','Driver’s License','Passport','UMID','Postal ID','PRC ID','Other Government ID'] as $idType)<option value="{{ $idType }}" @selected(old('valid_id_type', $verification?->valid_id_type) === $idType)>{{ $idType }}</option>@endforeach</select></div>
                        <div class="form-group"><label class="form-label" for="id-number">Valid ID number</label><input class="form-control" name="valid_id_number" id="id-number" value="{{ old('valid_id_number', $verification?->valid_id_number) }}" autocomplete="off" required><p class="form-help">Stored encrypted and visible only to authorized reviewers.</p></div>
                        <div class="form-group full"><label class="form-label" for="additional-information">Additional background <span class="optional">Optional</span></label><textarea class="form-control" name="additional_information" id="additional-information" maxlength="2000">{{ old('additional_information', $verification?->additional_information) }}</textarea></div>
                    </div>
                    <div class="document-grid">
                        <div class="form-group"><label class="form-label" for="valid-id">Valid ID document</label><input class="form-control" type="file" name="valid_id" id="valid-id" accept="image/jpeg,image/png,application/pdf" @required(! $verification?->valid_id_path)><p class="form-help">JPG, PNG, or PDF up to 4 MB. {{ $verification?->valid_id_path ? 'Upload only to replace the file already submitted.' : '' }}</p></div>
                        <div class="form-group"><label class="form-label" for="selfie-with-id">Selfie holding your valid ID</label><input class="form-control" type="file" name="selfie_with_id" id="selfie-with-id" accept="image/jpeg,image/png" @required(! $verification?->selfie_with_id_path)><p class="form-help">Your face and ID must both be visible. JPG or PNG up to 4 MB.</p></div>
                        <div class="form-group"><label class="form-label" for="payslip">Recent payslip <span class="optional">Recommended</span></label><input class="form-control" type="file" name="payslip" id="payslip" accept="image/jpeg,image/png,application/pdf"><p class="form-help">A clear recent payslip can strengthen the assessment after administrator verification. JPG, PNG, or PDF up to 4 MB.</p></div>
                    </div>
                    @include('profile.partials.signature-pad', ['signatureRequired' => ! $verification?->digital_signature, 'existingSignature' => $verification?->digital_signature])
                    <div class="form-actions"><button class="save-button" type="submit" id="submit-verification" data-signature-submit data-busy-label="Submitting verification...">{{ $verification ? 'Resubmit verification' : 'Submit for admin review' }}</button></div>
                </form>
            @endif
            @if($canCaptureLegacySignature)
                <form class="legacy-signature-form" method="POST" action="{{ route('profile.verification.signature.store') }}" data-signature-form>@csrf
                    @include('profile.partials.signature-pad', ['signatureRequired' => true, 'existingSignature' => null])
                    <div class="form-actions signature-form-actions"><button class="save-button" type="submit" data-signature-submit data-busy-label="Saving signature...">Save digital signature</button></div>
                </form>
            @endif
            @if($canUpdatePayslip)
                <form class="income-evidence-card" method="POST" action="{{ route('profile.verification.payslip.store') }}" enctype="multipart/form-data">@csrf
                    <div class="income-evidence-head"><div><h3>Income evidence</h3><p>Add or replace a recent payslip to strengthen your readiness assessment. A new file returns verification to administrator review.</p></div><span class="evidence-status {{ $verification->payslip_verified_at ? 'verified' : 'missing' }}">{{ $verification->payslip_verified_at ? 'Payslip verified' : ($verification->payslip_path ? 'Awaiting review' : 'Not provided') }}</span></div>
                    <div class="income-evidence-actions"><div class="form-group"><label class="form-label" for="approved-payslip">Recent payslip</label><input class="form-control" type="file" name="payslip" id="approved-payslip" accept="image/jpeg,image/png,application/pdf" required>@error('payslip','payslip')<p class="field-error">{{ $message }}</p>@enderror<p class="form-help">JPG, PNG, or PDF up to 4 MB.</p></div><button class="save-button" type="submit">Submit for review</button></div>
                </form>
            @endif
        </div>
    </section>
</div></main>
<script>
const verificationForm=document.getElementById('verification-form');const employmentStatus=document.getElementById('employment-status');
if(verificationForm&&employmentStatus){function syncEmploymentFields(){const required=['employed','self_employed'].includes(employmentStatus.value);document.getElementById('company-name').required=required;document.getElementById('job-title').required=required;}employmentStatus.addEventListener('change',syncEmploymentFields);syncEmploymentFields();}
</script>
</body></html>
