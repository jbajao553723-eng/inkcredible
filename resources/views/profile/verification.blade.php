<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Verification | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.client-portal-styles') @include('partials.settings-styles')</style>
</head>
<body>
@php
    $verification = $user->clientVerification;
    $verificationStatus = $verification?->status ?? 'not submitted';
    $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
    $canSubmitVerification = ! $verification || $verification->status === 'rejected';
@endphp
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Client verification</h1><p class="subtitle">Submit financial and identity information for administrator review.</p></div><span class="badge {{ $verificationClass }}">{{ ucfirst($verificationStatus) }}</span></header>
    @include('partials.settings-tabs', ['activeSettings' => 'verification'])
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
    <section class="panel">
        <div class="panel-header"><div><h2 class="panel-title">Employment, income, and identity</h2><p class="panel-description">All information must be accurate and the documents must be clear and unedited.</p></div><span class="badge {{ $verificationClass }}">{{ ucfirst($verificationStatus) }}</span></div>
        <div class="panel-body">
            @if($verificationStatus === 'pending')
                <div class="verification-notice notice-pending"><strong>Review in progress.</strong> Your submission is locked while an administrator reviews it. Loan requests remain unavailable until approval.</div>
            @elseif($verificationStatus === 'approved')
                <div class="verification-notice notice-approved"><strong>Account fully verified.</strong> You may now request loans from the client dashboard.</div>
            @elseif($verificationStatus === 'rejected')
                <div class="verification-notice notice-rejected"><strong>Changes required.</strong><br>{{ $verification->rejection_reason }}<br><br>Correct the information or replace the documents, then resubmit.</div>
            @else
                <div class="verification-notice notice-pending"><strong>Verification required.</strong> Complete every required field before requesting a loan.</div>
            @endif
            @if($errors->verification->any())<div class="alert alert-error"><ul>@foreach($errors->verification->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
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
                    </div>
                    <button class="save-button" type="submit" id="submit-verification">{{ $verification ? 'Resubmit verification' : 'Submit for admin review' }}</button>
                </form>
            @endif
        </div>
    </section>
</div></main>
@if($canSubmitVerification)<script>
const verificationForm=document.getElementById('verification-form');const employmentStatus=document.getElementById('employment-status');
function syncEmploymentFields(){const required=['employed','self_employed'].includes(employmentStatus.value);document.getElementById('company-name').required=required;document.getElementById('job-title').required=required;}
employmentStatus.addEventListener('change',syncEmploymentFields);verificationForm.addEventListener('submit',function(){const button=document.getElementById('submit-verification');button.disabled=true;button.textContent='Submitting verification...';});syncEmploymentFields();
</script>@endif
</body></html>
