<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify email | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.auth-styles')
@include('partials.motion-styles')
.otp-icon { display:grid; place-items:center; width:52px; height:52px; margin-bottom:20px; color:#4f46e5; background:#eef2ff; border:1px solid #d9d6fe; border-radius:15px; box-shadow:0 8px 18px rgba(79,70,229,.09); }
.otp-icon svg { width:25px; height:25px; }
.otp-input { height:58px; padding:10px 18px; font-size:24px; font-weight:700; letter-spacing:.42em; text-align:center; font-variant-numeric:tabular-nums; }
.otp-input::placeholder { color:#d0d5dd; letter-spacing:.35em; }
.otp-meta { display:flex; align-items:center; justify-content:space-between; gap:14px; margin:8px 0 22px; color:#667085; font-size:10px; }
.otp-meta span { display:inline-flex; align-items:center; gap:6px; }
.otp-meta svg { width:14px; height:14px; }
.resend-row { display:flex; align-items:center; justify-content:center; gap:5px; margin-top:20px; color:#667085; font-size:11px; }
.inline-form { display:inline; margin:0; }
.text-button { padding:0; color:#4f46e5; background:transparent; border:0; font:inherit; font-weight:700; cursor:pointer; }
.text-button:hover { color:#4338ca; text-decoration:underline; }
.session-row { display:flex; align-items:center; justify-content:center; gap:8px; margin-top:20px; color:#98a2b3; font-size:11px; }
.session-row .text-button { color:#475467; }
@media(max-width:420px){.otp-input{font-size:21px;letter-spacing:.32em}.otp-meta{align-items:flex-start;flex-direction:column;gap:7px}}
</style>
@vite('resources/js/app.js')
</head>
<body>
<main class="auth-page">
    <section class="brand-panel" aria-label="About email verification">
        <a class="brand" href="{{ route('home') }}"><x-brand-mark /><span>Inkcredible</span></a>
        <div class="brand-content">
            <div class="brand-eyebrow">Verified account identity</div>
            <h2 class="brand-title">Confirm that this email belongs to you.</h2>
            <p class="brand-description">A verified address protects your account and ensures important loan and payment updates reach the right person.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Six-digit single-use code</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Expires automatically after ten minutes</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Required after changing your email</li>
            </ul>
        </div>
        <div class="brand-footer">Inkcredible</div>
    </section>

    <section class="form-panel">
        <div class="form-shell">
            <a class="brand mobile-brand" href="{{ route('home') }}"><x-brand-mark /><span>Inkcredible</span></a>
            <div class="otp-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3.5" y="5.5" width="17" height="13" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 8 7 5 7-5"/></svg></div>
            <div class="form-eyebrow">Email verification</div>
            <h1>Enter your verification code</h1>
            <p class="form-description">We sent a code to <strong>{{ $maskedEmail }}</strong>. Enter the newest code below to continue.</p>

            @if(session('status') === 'verification-code-sent')
                <div class="alert alert-success" role="status" aria-live="polite">A new verification code was sent to your email address.</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1" data-error-summary>{{ $errors->first('code') ?: 'The code could not be verified.' }}</div>
            @endif

            <form method="POST" action="{{ route('verification.otp.verify') }}" id="otp-form">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="code">Six-digit verification code <span class="required-mark" aria-hidden="true">*</span></label>
                    <input class="form-control otp-input @error('code') is-invalid @enderror" id="code" type="text" name="code" value="{{ old('code') }}" placeholder="000000" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" aria-describedby="otp-help" autofocus required>
                    <p class="field-help" id="otp-help">Requesting a new code makes the previous code invalid.</p>
                </div>

                <div class="otp-meta">
                    <span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/></svg>Expires in 10 minutes</span>
                    <span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6z"/></svg>Five attempts allowed</span>
                </div>

                <button class="submit-button" type="submit" id="verify-code"><span>Verify email</span></button>
            </form>

            <div class="resend-row">Did not receive the email?<form class="inline-form" method="POST" action="{{ route('verification.send') }}">@csrf<button class="text-button" type="submit">Send a new code</button></form></div>
            <div class="session-row">Wrong account?<form class="inline-form" method="POST" action="{{ route('logout') }}">@csrf<button class="text-button" type="submit">Log out</button></form></div>
        </div>
    </section>
</main>

<script>
const codeInput = document.getElementById('code');
codeInput.addEventListener('input', function () {
    this.value = this.value.replace(/\D/g, '').slice(0, 6);
});
document.querySelector('[data-error-summary]')?.focus({ preventScroll: true });
document.getElementById('otp-form').addEventListener('submit', function () {
    const button = document.getElementById('verify-code');
    button.disabled = true;
    button.querySelector('span').textContent = 'Verifying email...';
});
</script>
</body>
</html>
