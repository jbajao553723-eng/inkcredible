<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.auth-styles')
@include('partials.motion-styles')
.recovery-icon { display:grid; place-items:center; width:48px; height:48px; margin-bottom:20px; color:#4f46e5; background:#eef2ff; border:1px solid #d9d6fe; border-radius:14px; box-shadow:0 8px 18px rgba(79,70,229,.08); }
.recovery-icon svg { width:23px; height:23px; }
.password-guidance { margin:4px 0 18px; padding:12px 14px; color:#475467; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; font-size:10px; line-height:1.6; }
.password-guidance strong { display:block; margin-bottom:3px; color:#344054; font-size:11px; }
.back-link { display:inline-flex; align-items:center; justify-content:center; gap:7px; margin-top:22px; color:#475467; font-size:12px; font-weight:600; text-decoration:none; }
.back-link:hover { color:#4338ca; }
.back-link svg { width:15px; height:15px; }
</style>
@vite('resources/js/app.js')
</head>
<body>
<main class="auth-page">
    <section class="brand-panel" aria-label="About password security">
        <a class="brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
        <div class="brand-content">
            <div class="brand-eyebrow">Protected account access</div>
            <h2 class="brand-title">Create a password that keeps your account secure.</h2>
            <p class="brand-description">Your new password immediately replaces the previous one and restores access to your Inkcredible account.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>At least eight characters</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Includes a number or symbol</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Confirmed before saving</li>
            </ul>
        </div>
        <div class="brand-footer">Inkcredible Lending Management System</div>
    </section>

    <section class="form-panel">
        <div class="form-shell">
            <a class="brand mobile-brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
            <div class="recovery-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 12l2 2 4-4"/></svg></div>
            <div class="form-eyebrow">Account recovery</div>
            <h1>Choose a new password</h1>
            <p class="form-description">Confirm your account email and enter a strong new password below.</p>

            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1" data-error-summary>We could not reset your password. Please review the highlighted fields.</div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" id="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label class="form-label" for="email">Email address <span class="required-mark" aria-hidden="true">*</span></label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" autocomplete="username" inputmode="email" autocapitalize="none" spellcheck="false" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror autofocus required>
                    @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New password <span class="required-mark" aria-hidden="true">*</span></label>
                    <div class="input-wrap">
                        <input class="form-control password-input @error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="Enter a new password" autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="password-error password-feedback" @enderror required>
                        <button class="password-toggle" type="button" data-toggle-password="password" aria-controls="password" aria-pressed="false" aria-label="Show new password"><svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg class="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.7 6.1A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8M6.6 6.6C4 8.3 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button>
                    </div>
                    <div class="password-meter" data-password-meter aria-hidden="true"><span></span><span></span><span></span></div>
                    <p class="field-feedback" id="password-feedback" aria-live="polite">Use at least 8 characters with a number or symbol.</p>
                    @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm new password <span class="required-mark" aria-hidden="true">*</span></label>
                    <div class="input-wrap">
                        <input class="form-control password-input" id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat your new password" autocomplete="new-password" required>
                        <button class="password-toggle" type="button" data-toggle-password="password_confirmation" aria-controls="password_confirmation" aria-pressed="false" aria-label="Show password confirmation"><svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg class="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.7 6.1A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8M6.6 6.6C4 8.3 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button>
                    </div>
                    <p class="field-feedback" id="confirmation-feedback" aria-live="polite"></p>
                </div>

                <div class="password-guidance"><strong>Password security</strong>Avoid using your name, email address, or a password you use on another website.</div>
                <button class="submit-button" type="submit" id="reset-password"><span>Reset password</span></button>
            </form>

            <a class="back-link" href="{{ route('login') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15 18-6-6 6-6"/></svg>Back to sign in</a>
        </div>
    </section>
</main>

<script>
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.togglePassword);
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        this.classList.toggle('is-showing', !showing);
        this.setAttribute('aria-pressed', String(!showing));
        this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});

const passwordInput = document.getElementById('password');
const confirmationInput = document.getElementById('password_confirmation');
const passwordFeedback = document.getElementById('password-feedback');
const confirmationFeedback = document.getElementById('confirmation-feedback');
const passwordMeter = document.querySelector('[data-password-meter]');

function updatePasswordFeedback() {
    const value = passwordInput.value;
    const checks = [value.length >= 8, /[A-Za-z]/.test(value) && /[0-9\W_]/.test(value), value.length >= 12];
    const strength = checks.filter(Boolean).length;
    passwordMeter.dataset.strength = strength;
    passwordFeedback.textContent = value ? (strength === 3 ? 'Strong password.' : strength === 2 ? 'Good password. Add more characters for extra strength.' : 'Use at least 8 characters with a number or symbol.') : 'Use at least 8 characters with a number or symbol.';
    passwordFeedback.className = 'field-feedback ' + (strength === 3 ? 'is-valid' : '');
    updateConfirmationFeedback();
}

function updateConfirmationFeedback() {
    if (!confirmationInput.value) {
        confirmationFeedback.textContent = '';
        confirmationFeedback.className = 'field-feedback';
        return;
    }
    const matches = passwordInput.value === confirmationInput.value;
    confirmationFeedback.textContent = matches ? 'Passwords match.' : 'Passwords do not match yet.';
    confirmationFeedback.className = 'field-feedback ' + (matches ? 'is-valid' : 'is-invalid');
}

passwordInput.addEventListener('input', updatePasswordFeedback);
confirmationInput.addEventListener('input', updateConfirmationFeedback);
document.querySelector('[data-error-summary]')?.focus({ preventScroll: true });
document.getElementById('reset-password-form').addEventListener('submit', function () {
    const button = document.getElementById('reset-password');
    button.disabled = true;
    button.querySelector('span').textContent = 'Resetting password...';
});
</script>
</body>
</html>
