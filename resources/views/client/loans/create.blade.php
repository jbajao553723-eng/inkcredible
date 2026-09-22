<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request a Loan | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.client-portal-styles')

.application-grid { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(300px, .65fr); gap: 22px; align-items: start; }
.form-section + .form-section { margin-top: 28px; padding-top: 26px; border-top: 1px solid var(--border); }
.section-kicker { margin-bottom: 5px; color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; }
.section-title { margin: 0; font-size: 16px; }
.section-note { margin: 6px 0 18px; color: var(--muted); font-size: 12px; }
.loan-type-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
.loan-option { position: relative; }
.loan-option input { position: absolute; opacity: 0; }
.loan-type-card { display: block; min-height: 172px; padding: 18px; border: 1px solid #d0d5dd; border-radius: 13px; cursor: pointer; transition: .16s ease; }
.loan-type-card:hover { border-color: #a5b4fc; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 24, 40, .06); }
.loan-option input:focus + .loan-type-card { box-shadow: 0 0 0 4px rgba(99, 102, 241, .12); }
.loan-option input:checked + .loan-type-card { border-color: var(--primary); background: #f5f3ff; box-shadow: 0 0 0 1px var(--primary); }
.type-icon { display: grid; place-items: center; width: 38px; height: 38px; margin-bottom: 16px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.type-icon svg { width: 19px; height: 19px; }
.type-title { font-size: 14px; font-weight: 700; }
.type-description { min-height: 34px; margin-top: 6px; color: var(--muted); font-size: 11px; line-height: 1.45; }
.type-rate { margin-top: 12px; color: #344054; font-size: 11px; font-weight: 600; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 18px; }
.form-label { display: block; margin-bottom: 7px; color: #344054; font-size: 13px; font-weight: 600; }
.required { color: #d92d20; }
.form-control { width: 100%; min-height: 46px; padding: 10px 12px; color: var(--navy); background: #fff; border: 1px solid #d0d5dd; border-radius: 10px; outline: none; transition: border-color .15s, box-shadow .15s; }
.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
textarea.form-control { min-height: 105px; resize: vertical; }
.input-prefix { position: relative; }
.input-prefix span { position: absolute; top: 50%; left: 13px; color: #475467; transform: translateY(-50%); }
.input-prefix input { padding-left: 34px; }
.form-help { margin: 7px 0 0; color: var(--muted); font-size: 12px; line-height: 1.45; }
.field-error { color: #b42318; font-size: 11px; }
.file-input { padding: 11px; background: #fcfcfd; }
.submit-button { display: flex; align-items: center; justify-content: center; gap: 9px; width: 100%; min-height: 48px; color: #fff; background: var(--primary); border: 0; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 20px rgba(79, 70, 229, .18); }
.submit-button:hover { background: var(--primary-dark); }
.submit-button:disabled { opacity: .6; cursor: not-allowed; }
.submit-button svg { width: 18px; height: 18px; }
.estimate-card { padding: 24px; }
.estimate-label { color: var(--muted); font-size: 12px; }
.estimate-value { margin-top: 6px; font-size: 28px; font-weight: 700; letter-spacing: -.035em; }
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
.consent-box { display: flex; gap: 10px; margin: 26px 0 18px; padding: 14px; color: #475467; background: #f9fafb; border: 1px solid #eaecf0; border-radius: 10px; font-size: 11px; line-height: 1.55; }
.consent-box input { width: 16px; height: 16px; margin-top: 1px; flex: 0 0 16px; accent-color: var(--primary); }
.consent-box a { color: var(--primary); font-weight: 600; }

@media (max-width: 1050px) { .application-grid { grid-template-columns: 1fr; } }
@media (max-width: 800px) { .loan-type-grid { grid-template-columns: 1fr; } .loan-type-card { min-height: auto; } .type-description { min-height: auto; } }
@media (max-width: 560px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
</style>
</head>

<body>
@include('partials.client-sidebar', ['active' => 'loans'])

<main class="main">
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
            <section class="panel">
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
                                <div class="section-kicker">Step 1</div>
                                <h3 class="section-title">Select a loan type</h3>
                                <p class="section-note">Limits and rates shown below come directly from the active loan products.</p>

                                <div class="loan-type-grid">
                                    @foreach($loanTypes as $loanType)
                                        <label class="loan-option">
                                            <input type="radio" name="loan_type" value="{{ $loanType->name }}"
                                                   data-name="{{ $loanType->display_name }}"
                                                   data-min="{{ $loanType->min_amount }}"
                                                   data-max="{{ $loanType->max_amount }}"
                                                   data-rate="{{ $loanType->interest_rate }}"
                                                   data-days="{{ $loanType->due_days }}"
                                                   @checked(old('loan_type') === $loanType->name) required>
                                            <span class="loan-type-card">
                                                <span class="type-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16v11H4zM4 10h16M8 15h3"/></svg></span>
                                                <span class="type-title">{{ $loanType->display_name }}</span>
                                                <span class="type-description">{{ $loanType->description }}</span>
                                                <span class="type-rate">{{ number_format((float) $loanType->interest_rate, 0) }}% interest · {{ $loanType->due_days }} {{ Str::plural('day', $loanType->due_days) }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-kicker">Step 2</div>
                                <h3 class="section-title">Loan information</h3>
                                <p class="section-note">Enter your requested amount and tell us how you plan to use it.</p>

                                <div class="form-group">
                                    <label class="form-label" for="amount">Requested amount <span class="required">*</span></label>
                                    <div class="input-prefix"><span>₱</span><input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" min="1" step="0.01" placeholder="0.00" required></div>
                                    <p class="form-help" id="amount-hint">Select a loan type to see its allowed amount range.</p>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="purpose">Loan purpose <span class="cell-secondary">Optional</span></label>
                                    <textarea name="purpose" id="purpose" class="form-control" maxlength="255" placeholder="Briefly describe what the loan will be used for">{{ old('purpose') }}</textarea>
                                    <p class="form-help">Maximum of 255 characters.</p>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-kicker">Step 3</div>
                                <h3 class="section-title">Identity document</h3>
                                <p class="section-note">Upload a clear copy of a valid government-issued ID.</p>

                                <div class="form-group">
                                    <label class="form-label" for="government-id">Government-issued ID <span class="required">*</span></label>
                                    <input type="file" name="government_id" id="government-id" class="form-control file-input" accept="image/jpeg,image/png,application/pdf" required>
                                    <p class="form-help">Accepted: National ID, driver's license, or passport in JPG, PNG, or PDF format up to 2 MB.</p>
                                </div>
                            </div>

                            <label class="consent-box">
                                <input type="checkbox" name="loan_terms_accepted" value="1" @checked(old('loan_terms_accepted')) required>
                                <span>I have reviewed and agree to the <a href="{{ route('loan.terms') }}" target="_blank" rel="noopener">Loan Terms and Borrower Disclosure</a>, including interest, repayment obligations, possible lawful penalties and charges, collection measures, and reporting of suspected fraud or unlawful conduct.</span>
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

            <aside>
                <section class="panel estimate-card">
                    <div class="estimate-label">Estimated total repayment</div>
                    <div class="estimate-value estimate-placeholder" id="estimated-total">Select a loan</div>
                    <div class="estimate-lines">
                        <div class="estimate-line"><span>Principal</span><strong id="estimated-principal">—</strong></div>
                        <div class="estimate-line"><span>Interest</span><strong id="estimated-interest">—</strong></div>
                        <div class="estimate-line"><span>Term</span><strong id="estimated-term">—</strong></div>
                    </div>
                    <p class="form-help">This is an estimate based on the selected product. Final approval is subject to review.</p>
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

function peso(value) {
    return `₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function selectedLoanType() {
    return document.querySelector('input[name="loan_type"]:checked');
}

function updateEstimate() {
    const selected = selectedLoanType();

    if (!selected) return;

    const minimum = Number(selected.dataset.min);
    const maximum = Number(selected.dataset.max);
    const rate = Number(selected.dataset.rate);
    const days = Number(selected.dataset.days);
    const amount = Number(amountInput.value || 0);

    amountInput.min = minimum.toFixed(2);
    amountInput.max = maximum.toFixed(2);
    amountHint.textContent = `${selected.dataset.name}: ${peso(minimum)} to ${peso(maximum)} at ${rate}% interest.`;

    if (amount > 0) {
        const interest = amount * (rate / 100);
        totalOutput.textContent = peso(amount + interest);
        totalOutput.classList.remove('estimate-placeholder');
        principalOutput.textContent = peso(amount);
        interestOutput.textContent = peso(interest);
    } else {
        totalOutput.textContent = 'Enter an amount';
        totalOutput.classList.add('estimate-placeholder');
        principalOutput.textContent = '—';
        interestOutput.textContent = `${rate}%`;
    }

    termOutput.textContent = `${days} ${days === 1 ? 'day' : 'days'}`;
}

loanOptions.forEach((option) => option.addEventListener('change', updateEstimate));
amountInput.addEventListener('input', updateEstimate);
loanForm.addEventListener('submit', function () {
    const button = document.getElementById('submit-loan');
    button.disabled = true;
    button.querySelector('span').textContent = 'Submitting application…';
});

updateEstimate();
</script>
@endif
</body>
</html>
