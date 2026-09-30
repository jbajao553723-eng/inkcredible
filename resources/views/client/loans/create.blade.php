<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request a Loan | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style>
@include('partials.client-portal-styles')

.main { background: radial-gradient(circle at 85% 8%, rgba(99, 102, 241, .07), transparent 27%), var(--canvas); }
.application-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(310px, .65fr); gap: 24px; align-items: start; }
.application-panel { border-color: #dfe3ea; box-shadow: 0 12px 34px rgba(16, 24, 40, .06); }
.application-panel .panel-body { padding: 20px; background: #fbfcfe; }
.form-section { padding: 22px; background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 1px 2px rgba(16, 24, 40, .03); }
.form-section + .form-section { margin-top: 16px; }
.section-heading { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 18px; }
.section-number { display: grid; place-items: center; width: 34px; height: 34px; flex: 0 0 34px; color: #fff; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 10px; font-size: 12px; font-weight: 700; box-shadow: 0 7px 15px rgba(79, 70, 229, .18); }
.section-kicker { margin-bottom: 3px; color: var(--primary); font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
.section-title { margin: 0; font-size: 16px; }
.section-note { margin: 5px 0 0; color: var(--muted); font-size: 12px; line-height: 1.5; }
.loan-type-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; align-items: stretch; }
.loan-option { position: relative; display: block; height: 100%; }
.loan-option input { position: absolute; opacity: 0; }
.loan-type-card { position: relative; display: flex; flex-direction: column; height: 100%; min-height: 216px; padding: 18px; overflow: hidden; border: 1px solid #d7dce3; border-radius: 14px; cursor: pointer; transition: .18s ease; }
.loan-type-card::after { content: '✓'; position: absolute; top: 14px; right: 14px; display: grid; place-items: center; width: 22px; height: 22px; color: transparent; background: #f2f4f7; border-radius: 50%; font-size: 12px; font-weight: 800; transition: .18s ease; }
.loan-type-card:hover { border-color: #a5b4fc; transform: translateY(-2px); box-shadow: 0 10px 22px rgba(16, 24, 40, .07); }
.loan-option input:focus + .loan-type-card { box-shadow: 0 0 0 4px rgba(99, 102, 241, .12); }
.loan-option input:checked + .loan-type-card { border-color: var(--primary); background: linear-gradient(145deg, #fafaff, #f1efff); box-shadow: 0 0 0 1px var(--primary), 0 10px 25px rgba(79, 70, 229, .1); }
.loan-option input:checked + .loan-type-card::after { color: #fff; background: var(--primary); }
.type-icon { display: grid; place-items: center; width: 38px; height: 38px; margin-bottom: 16px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.type-icon svg { width: 19px; height: 19px; }
.type-title { display: block; padding-right: 24px; font-size: 14px; font-weight: 700; line-height: 1.35; }
.type-description { display: block; margin-top: 6px; color: var(--muted); font-size: 11px; line-height: 1.55; }
.type-rate { display: block; margin-top: auto; padding-top: 12px; color: #344054; font-size: 11px; font-weight: 600; line-height: 1.5; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 18px; }
.form-label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 600; }
.required { color: #d92d20; }
.form-control { width: 100%; min-height: 48px; padding: 11px 13px; color: var(--navy); background: #fff; border: 1px solid #cfd5de; border-radius: 11px; outline: none; transition: border-color .15s, box-shadow .15s; }
.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
textarea.form-control { min-height: 105px; resize: vertical; }
.purpose-options { display: flex; flex-wrap: wrap; gap: 9px; }
.purpose-option { position: relative; }
.purpose-option input { position: absolute; opacity: 0; pointer-events: none; }
.purpose-chip { display: inline-flex; align-items: center; min-height: 40px; padding: 9px 14px; color: #344054; background: #fff; border: 1px solid #d0d5dd; border-radius: 999px; cursor: pointer; font-size: 12px; font-weight: 600; transition: .15s ease; }
.purpose-chip:hover { border-color: #a5b4fc; background: #f9fafb; }
.purpose-option input:focus + .purpose-chip { box-shadow: 0 0 0 4px rgba(99, 102, 241, .12); }
.purpose-option input:checked + .purpose-chip { color: #4338ca; background: #eef2ff; border-color: var(--primary); box-shadow: 0 0 0 1px var(--primary); }
.purpose-group[hidden], .purpose-other[hidden] { display: none; }
.purpose-other { max-width: 520px; margin-top: 12px; }
.amount-entry { padding: 16px; background: #f8f9fc; border: 1px solid #eaecf0; border-radius: 13px; }
.input-prefix { position: relative; }
.input-prefix span { position: absolute; top: 50%; left: 14px; color: #344054; transform: translateY(-50%); font-size: 17px; font-weight: 700; }
.input-prefix input { padding-left: 38px; font-size: 16px; font-weight: 600; }
.quick-amounts { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 10px; }
.quick-amount { padding: 7px 10px; color: #475467; background: #fff; border: 1px solid #d7dce3; border-radius: 8px; font-size: 11px; font-weight: 600; cursor: pointer; transition: .15s ease; }
.quick-amount:hover { color: var(--primary); border-color: #a5b4fc; background: #f5f3ff; }
.form-help { margin: 7px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }
.field-error { color: #b42318; font-size: 11px; }
.file-input { position: absolute; width: 1px; height: 1px; overflow: hidden; opacity: 0; }
.upload-zone { display: flex; align-items: center; gap: 14px; min-height: 94px; padding: 17px; background: #fafbff; border: 1px dashed #aeb7c5; border-radius: 13px; cursor: pointer; transition: .16s ease; }
.upload-zone:hover, .upload-zone:focus-within { background: #f5f3ff; border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .08); }
.upload-zone.is-selected { background:#ecfdf3; border-color:#12b76a; }
.upload-zone.is-selected .upload-icon { color:#067647; background:#d1fadf; }
.upload-icon { display: grid; place-items: center; width: 42px; height: 42px; flex: 0 0 42px; color: var(--primary); background: #eef2ff; border-radius: 11px; }
.upload-icon svg { width: 21px; height: 21px; }
.upload-check { display:none; margin-left:auto; color:#067647; }
.upload-check svg { width:24px; height:24px; }
.upload-zone.is-selected .upload-check { display:block; }
.upload-title { display: block; color: #344054; font-size: 13px; font-weight: 700; }
.upload-file-name { display: block; margin-top: 4px; color: var(--muted); font-size: 11px; }
.submit-button { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 52px; color: #fff; background: linear-gradient(135deg, #4f46e5, #6338c5); border: 0; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 10px 24px rgba(79, 70, 229, .22); transition: .16s ease; }
.submit-button:hover { background: var(--primary-dark); }
.submit-button:disabled { opacity: .6; cursor: not-allowed; }
.submit-button svg { width: 18px; height: 18px; }
.application-aside { position: sticky; top: 24px; }
.estimate-card { position: relative; padding: 25px; overflow: hidden; border-color: #d9dcf5; box-shadow: 0 10px 28px rgba(16, 24, 40, .06); }
.estimate-card::before { content: ''; position: absolute; inset: 0 0 auto; height: 4px; background: linear-gradient(90deg, #4f46e5, #8b5cf6); }
.estimate-label { color: var(--muted); font-size: 12px; }
.estimate-value { margin-top: 7px; font-size: 29px; font-weight: 700; letter-spacing: -.035em; }
.estimate-placeholder { color: #98a2b3; }
.estimate-lines { margin-top: 22px; padding-top: 18px; border-top: 1px solid var(--border); }
.estimate-line { display: flex; justify-content: space-between; gap: 15px; padding: 7px 0; color: var(--muted); font-size: 12px; }
.estimate-line strong { color: #344054; font-weight: 600; }
.info-card { margin-top: 16px; padding: 22px; }
.info-title { margin: 0 0 16px; font-size: 14px; }
.step { display: flex; gap: 12px; }
.step + .step { margin-top: 16px; }
.step-number { display: grid; place-items: center; width: 26px; height: 26px; flex: 0 0 26px; color: var(--primary); background: #eef2ff; border-radius: 50%; font-size: 11px; font-weight: 700; }
.step-title { font-size: 12px; font-weight: 600; }
.step-note { margin-top: 3px; color: var(--muted); font-size: 11px; line-height: 1.45; }
.consent-box { display: flex; gap: 12px; margin: 18px 0; padding: 16px; color: #475467; background: #f8f9fc; border: 1px solid #dfe3ea; border-radius: 13px; font-size: 11px; line-height: 1.6; }
.consent-box input { width: 18px; height: 18px; margin-top: 1px; flex: 0 0 18px; accent-color: var(--primary); }
.terms-trigger { padding: 0; color: var(--primary); background: none; border: 0; font: inherit; font-weight: 700; text-decoration: underline; text-underline-offset: 2px; cursor: pointer; }
.terms-dialog { width: min(880px, calc(100% - 32px)); height: min(760px, calc(100vh - 40px)); padding: 0; overflow: hidden; background: #fff; border: 0; border-radius: 18px; box-shadow: 0 30px 80px rgba(15, 23, 42, .3); }
.terms-dialog::backdrop { background: rgba(15, 23, 42, .62); backdrop-filter: blur(4px); }
.terms-dialog-shell { display: grid; grid-template-rows: auto 1fr auto; height: 100%; }
.terms-dialog-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 20px; border-bottom: 1px solid var(--border); }
.terms-dialog-header h2 { margin: 0; font-size: 17px; }
.terms-dialog-header p { margin: 4px 0 0; color: var(--muted); font-size: 11px; }
.terms-close { display: grid; place-items: center; width: 36px; height: 36px; flex: 0 0 36px; color: #475467; background: #f2f4f7; border: 0; border-radius: 9px; cursor: pointer; font-size: 20px; }
.terms-frame { width: 100%; height: 100%; border: 0; background: #f8fafc; }
.terms-dialog-actions { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 20px; border-top: 1px solid var(--border); }
.terms-dialog-note { color: var(--muted); font-size: 11px; }
.terms-dialog-actions .button { min-height: 38px; }

@media (max-width: 1050px) { .application-grid { grid-template-columns: 1fr; } }
@media (max-width: 1050px) { .application-aside { position: static; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; } .info-card { margin-top: 0; } }
@media (max-width: 800px) { .loan-type-grid { grid-template-columns: 1fr; } .loan-type-card { min-height: auto; } .type-description { min-height: auto; } .application-aside { grid-template-columns: 1fr; } .info-card { margin-top: 0; } }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; gap: 0; } .application-panel .panel-body { padding: 12px; } .form-section { padding: 17px; } .terms-dialog-actions { align-items: stretch; flex-direction: column; } .terms-dialog-actions .button { width: 100%; } }
</style>
</head>

<body>
@include('partials.client-sidebar', ['active' => 'loans'])

<main class="main" id="main-content" tabindex="-1">
    <div class="page-shell">
        <header class="topbar">
            <div>
                <div class="eyebrow">Loan application</div>
                <h1>Request a loan</h1>
                <p class="subtitle">Choose the loan that fits your needs and review the estimate before submitting.</p>
            </div>
            <div class="top-actions">
                <a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>

        @if($errors->any() || session('error'))
            <div class="alert alert-error" role="alert">
                @if(session('error'))<div>{{ session('error') }}</div>@endif
                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="application-grid">
            <section class="panel application-panel">
                <div class="panel-header">
                    <div><h2 class="panel-title">Application details</h2><p class="panel-description">All fields marked with an asterisk are required.</p></div>
                    <span class="badge badge-neutral">Secure form</span>
                </div>

                <div class="panel-body">
                    @if($loanTypes->isEmpty())
                        <div class="empty-state"><strong>No loan products available</strong>Please check again later or contact the administrator.</div>
                    @else
                        <form method="POST" action="{{ route('loan.store') }}" enctype="multipart/form-data" id="loan-form">
                            @csrf

                            <div class="form-section">
                                <div class="section-heading">
                                    <span class="section-number">01</span>
                                    <div><div class="section-kicker">Choose a product</div><h3 class="section-title">Select a loan type</h3><p class="section-note">Compare the available limits, interest, and repayment period.</p></div>
                                </div>

                                <div class="loan-type-grid">
                                    @foreach($loanTypes as $loanType)
                                        <label class="loan-option">
                                            <input type="radio" name="loan_type" value="{{ $loanType->name }}"
                                                   data-name="{{ $loanType->display_name }}"
                                                   data-min="{{ $loanType->min_amount }}"
                                                   data-max="{{ $loanType->max_amount }}"
                                                   data-rate="{{ $loanType->interest_rate }}"
                                                   data-days="{{ $loanType->due_days }}"
                                                   data-type="{{ $loanType->name }}"
                                                   @checked(old('loan_type') === $loanType->name) required>
                                            <span class="loan-type-card">
                                                <span class="type-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v11H4zM4 10h16M8 15h3"/></svg></span>
                                                <span class="type-title">{{ $loanType->display_name }}</span>
                                                <span class="type-description">{{ $loanType->description }}</span>
                                                <span class="type-rate">{{ number_format((float) $loanType->interest_rate, 0) }}% interest · {{ match($loanType->name) { 'arawan' => '30 daily payments', 'weekly', 'emergency' => '4 weekly payments', default => $loanType->due_days.' days' } }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                            </div>

                            <div class="form-section">
                                <div class="section-heading">
                                    <span class="section-number">02</span>
                                    <div><div class="section-kicker">Request details</div><h3 class="section-title">Loan information</h3><p class="section-note">Enter your requested amount and choose how you plan to use it.</p></div>
                                </div>

                                <div class="form-group amount-entry">
                                    <label class="form-label" for="amount">Requested amount <span class="required">*</span></label>
                                    <div class="input-prefix"><span>₱</span><input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" min="1" step="0.01" placeholder="0.00" required></div>
                                    <div class="quick-amounts" id="quick-amounts" hidden>
                                        <button class="quick-amount" type="button" data-amount-choice="minimum">Minimum</button>
                                        <button class="quick-amount" type="button" data-amount-choice="middle">Mid-range</button>
                                        <button class="quick-amount" type="button" data-amount-choice="maximum">Maximum</button>
                                    </div>
                                    <p class="form-help" id="amount-hint">Select a loan type to see its allowed amount range.</p>
                                </div>

                                <div class="form-group">
                                    <div class="form-label">Loan purpose <span class="required">*</span></div>
                                    @foreach($purposeOptions as $loanTypeName => $options)
                                        <div class="purpose-group" data-purpose-group="{{ $loanTypeName }}" @if(old('loan_type') !== $loanTypeName) hidden @endif>
                                            <div class="purpose-options">
                                                @foreach($options as $value => $label)
                                                    <label class="purpose-option">
                                                        <input type="radio" name="purpose_choice" value="{{ $value }}" @checked(old('loan_type') === $loanTypeName && old('purpose_choice') === $value)>
                                                        <span class="purpose-chip">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="purpose-other" id="purpose-other-wrap" @if(old('purpose_choice') !== 'other') hidden @endif>
                                        <label class="form-label" for="purpose-other">Your reason</label>
                                        <input type="text" name="purpose_other" id="purpose-other" class="form-control" value="{{ old('purpose_other') }}" maxlength="255" placeholder="Enter your loan purpose" @if(old('purpose_choice') !== 'other') disabled @else required @endif>
                                    </div>
                                    <p class="form-help">Choose one option. Select Other only when your reason is not listed.</p>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-heading">
                                    <span class="section-number">03</span>
                                    <div><div class="section-kicker">Supporting document</div><h3 class="section-title">Identity document</h3><p class="section-note">Upload a clear copy of a valid government-issued ID.</p></div>
                                </div>

                                <div class="form-group">
                                    <label class="upload-zone" for="government-id" id="government-id-zone">
                                        <span class="upload-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg></span>
                                        <span><span class="upload-title">Choose your government-issued ID <span class="required">*</span></span><span class="upload-file-name" id="government-id-name">Click to select a JPG, PNG, or PDF file</span></span>
                                        <span class="upload-check" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 12 2.6 2.6L16.5 9"/></svg></span>
                                    </label>
                                    <input type="file" name="government_id" id="government-id" class="file-input" accept="image/jpeg,image/png,application/pdf" required>
                                    <p class="form-help">Accepted: National ID, driver's license, or passport in JPG, PNG, or PDF format up to 2 MB.</p>
                                </div>
                            </div>

                            <label class="consent-box">
                                <input type="checkbox" name="loan_terms_accepted" value="1" @checked(old('loan_terms_accepted')) required>
                                <span>I have reviewed and agree to the <button class="terms-trigger" type="button" id="open-loan-terms">Loan Terms and Borrower Disclosure</button>, including interest, repayment obligations, possible lawful penalties and charges, collection measures, and reporting of suspected fraud or unlawful conduct.</span>
                            </label>
                            @error('loan_terms_accepted')<p class="field-error" style="color:#b42318; margin-top:-12px; margin-bottom:16px">You must accept the loan terms before submitting an application.</p>@enderror

                            <button class="submit-button" type="submit" id="submit-loan">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-5-5 5 5-5 5"/></svg>
                                <span>Submit loan request</span>
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <aside class="application-aside">
                <section class="panel estimate-card">
                    <div class="estimate-label">Estimated total repayment</div>
                    <div class="estimate-value estimate-placeholder" id="estimated-total">Select a loan</div>
                    <div class="estimate-lines">
                        <div class="estimate-line"><span>Principal</span><strong id="estimated-principal">—</strong></div>
                        <div class="estimate-line"><span>Interest</span><strong id="estimated-interest">—</strong></div>
                        <div class="estimate-line"><span>Term</span><strong id="estimated-term">—</strong></div>
                        <div class="estimate-line"><span>Approx. payment amount</span><strong id="estimated-installment">—</strong></div>
                    </div>
                    <p class="form-help">This estimate excludes late charges. An unpaid installment incurs a simple penalty equal to 5% of its unpaid amount for each day overdue.</p>
                </section>

                <section class="panel info-card">
                    <h2 class="info-title">What happens next?</h2>
                    <div class="step"><span class="step-number">1</span><div><div class="step-title">Application review</div><div class="step-note">An administrator checks your request and submitted ID.</div></div></div>
                    <div class="step"><span class="step-number">2</span><div><div class="step-title">Approval decision</div><div class="step-note">Your dashboard will show whether the loan was approved or rejected.</div></div></div>
                    <div class="step"><span class="step-number">3</span><div><div class="step-title">Repayment</div><div class="step-note">Approved balances can be paid securely from the Payments page.</div></div></div>
                </section>
            </aside>
        </div>
    </div>
</main>

<dialog class="terms-dialog" id="loan-terms-dialog" aria-labelledby="loan-terms-title">
    <div class="terms-dialog-shell">
        <header class="terms-dialog-header">
            <div><h2 id="loan-terms-title">Loan Terms and Borrower Disclosure</h2><p>Your application remains filled in while you review this document.</p></div>
            <button class="terms-close" type="button" id="close-loan-terms" aria-label="Close loan terms">×</button>
        </header>
        <iframe class="terms-frame" src="{{ route('loan.terms') }}" title="Loan Terms and Borrower Disclosure"></iframe>
        <footer class="terms-dialog-actions">
            <span class="terms-dialog-note">Closing this window will return you to the application without clearing your entries.</span>
            <button class="button button-primary" type="button" id="accept-loan-terms">I have read the terms</button>
        </footer>
    </div>
</dialog>

@if($loanTypes->isNotEmpty())
<script>
const loanForm = document.getElementById('loan-form');
const loanOptions = document.querySelectorAll('input[name="loan_type"]');
const amountInput = document.getElementById('amount');
const amountHint = document.getElementById('amount-hint');
const totalOutput = document.getElementById('estimated-total');
const principalOutput = document.getElementById('estimated-principal');
const interestOutput = document.getElementById('estimated-interest');
const termOutput = document.getElementById('estimated-term');
const installmentOutput = document.getElementById('estimated-installment');
const purposeGroups = document.querySelectorAll('[data-purpose-group]');
const purposeChoices = document.querySelectorAll('input[name="purpose_choice"]');
const purposeOtherWrap = document.getElementById('purpose-other-wrap');
const purposeOtherInput = document.getElementById('purpose-other');
const quickAmounts = document.getElementById('quick-amounts');
const quickAmountButtons = document.querySelectorAll('[data-amount-choice]');
const governmentIdInput = document.getElementById('government-id');
const governmentIdName = document.getElementById('government-id-name');
const governmentIdZone = document.getElementById('government-id-zone');
const termsCheckbox = document.querySelector('input[name="loan_terms_accepted"]');
const termsDialog = document.getElementById('loan-terms-dialog');
const openTermsButton = document.getElementById('open-loan-terms');
const closeTermsButton = document.getElementById('close-loan-terms');
const acceptTermsButton = document.getElementById('accept-loan-terms');

function peso(value) {
    return `₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function selectedLoanType() {
    return document.querySelector('input[name="loan_type"]:checked');
}

function syncOtherPurpose() {
    const selectedPurpose = document.querySelector('input[name="purpose_choice"]:checked');
    const showOther = selectedPurpose?.value === 'other';

    purposeOtherWrap.hidden = !showOther;
    purposeOtherInput.disabled = !showOther;
    purposeOtherInput.required = showOther;

    if (!showOther) purposeOtherInput.value = '';
}

function updatePurposeOptions(clearSelection = false) {
    const loanType = selectedLoanType()?.value;

    if (clearSelection) {
        purposeChoices.forEach((choice) => { choice.checked = false; });
    }

    purposeGroups.forEach((group) => {
        const active = group.dataset.purposeGroup === loanType;
        group.hidden = !active;
        group.querySelectorAll('input[name="purpose_choice"]').forEach((choice) => {
            choice.disabled = !active;
            choice.required = active;
        });
    });

    syncOtherPurpose();
}

function updateEstimate() {
    const selected = selectedLoanType();

    if (!selected) {
        quickAmounts.hidden = true;
        return;
    }

    const minimum = Number(selected.dataset.min);
    const maximum = Number(selected.dataset.max);
    const rate = Number(selected.dataset.rate);
    const type = selected.dataset.type;
    const installments = type === 'arawan' ? 30 : ['weekly', 'emergency'].includes(type) ? 4 : 1;
    const amount = Number(amountInput.value || 0);

    amountInput.min = minimum.toFixed(2);
    amountInput.max = maximum.toFixed(2);
    amountHint.textContent = `${selected.dataset.name}: ${peso(minimum)} to ${peso(maximum)} at ${rate}% interest.`;
    quickAmounts.hidden = false;
    quickAmountButtons[0].textContent = `Minimum ${peso(minimum)}`;
    quickAmountButtons[1].textContent = `Mid-range ${peso((minimum + maximum) / 2)}`;
    quickAmountButtons[2].textContent = `Maximum ${peso(maximum)}`;

    if (amount > 0) {
        const interest = amount * (rate / 100);
        totalOutput.textContent = peso(amount + interest);
        totalOutput.classList.remove('estimate-placeholder');
        principalOutput.textContent = peso(amount);
        interestOutput.textContent = peso(interest);
        installmentOutput.textContent = `${peso((amount + interest) / installments)} x ${installments}`;
    } else {
        totalOutput.textContent = 'Enter an amount';
        totalOutput.classList.add('estimate-placeholder');
        principalOutput.textContent = '—';
        interestOutput.textContent = `${rate}%`;
        installmentOutput.textContent = '—';
    }

    if (type === 'arawan') {
        termOutput.textContent = '1 month - 30 daily payments';
    } else if (['weekly', 'emergency'].includes(type)) {
        termOutput.textContent = '4 weeks - 4 weekly payments';
    } else {
        termOutput.textContent = `${selected.dataset.days} days`;
    }
}

quickAmountButtons.forEach((button) => button.addEventListener('click', () => {
    const selected = selectedLoanType();
    if (!selected) return;

    const minimum = Number(selected.dataset.min);
    const maximum = Number(selected.dataset.max);
    const amount = button.dataset.amountChoice === 'minimum'
        ? minimum
        : button.dataset.amountChoice === 'maximum'
            ? maximum
            : (minimum + maximum) / 2;

    amountInput.value = amount.toFixed(2);
    updateEstimate();
    amountInput.focus();
}));

governmentIdInput.addEventListener('change', () => {
    const file = governmentIdInput.files[0];
    governmentIdName.textContent = file ? `Selected: ${file.name}` : 'Click to select a JPG, PNG, or PDF file';
    governmentIdZone.classList.toggle('is-selected', Boolean(file));
});

function closeTermsDialog() {
    termsDialog.close();
    document.body.style.overflow = '';
}

openTermsButton.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (typeof termsDialog.showModal === 'function') {
        termsDialog.showModal();
        document.body.style.overflow = 'hidden';
    } else {
        window.open(@json(route('loan.terms')), '_blank', 'noopener');
    }
});

closeTermsButton.addEventListener('click', closeTermsDialog);
acceptTermsButton.addEventListener('click', () => {
    termsCheckbox.checked = true;
    closeTermsDialog();
});
termsDialog.addEventListener('close', () => { document.body.style.overflow = ''; });
termsDialog.addEventListener('click', (event) => {
    if (event.target === termsDialog) closeTermsDialog();
});

loanOptions.forEach((option) => option.addEventListener('change', () => {
    updatePurposeOptions(true);
    updateEstimate();
}));
purposeChoices.forEach((choice) => choice.addEventListener('change', syncOtherPurpose));
amountInput.addEventListener('input', updateEstimate);
loanForm.addEventListener('submit', function () {
    const button = document.getElementById('submit-loan');
    button.disabled = true;
    button.querySelector('span').textContent = 'Submitting application…';
});

updatePurposeOptions();
updateEstimate();
</script>
@endif
</body>
</html>
