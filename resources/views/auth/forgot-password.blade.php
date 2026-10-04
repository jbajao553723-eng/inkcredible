<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
@include('partials.auth-styles')
@include('partials.motion-styles')
.recovery-icon { display:grid; place-items:center; width:48px; height:48px; margin-bottom:20px; color:#4f46e5; background:#eef2ff; border:1px solid #d9d6fe; border-radius:14px; box-shadow:0 8px 18px rgba(79,70,229,.08); }
.recovery-icon svg { width:23px; height:23px; }
.recovery-note { display:flex; gap:10px; margin:20px 0 0; padding:13px 14px; color:#475467; background:#f8fafc; border:1px solid #eaecf0; border-radius:10px; font-size:11px; line-height:1.55; }
.recovery-note svg { width:17px; height:17px; flex:0 0 17px; color:#667085; }
.back-link { display:inline-flex; align-items:center; justify-content:center; gap:7px; margin-top:22px; color:#475467; font-size:12px; font-weight:600; text-decoration:none; }
.back-link:hover { color:#4338ca; }
.back-link svg { width:15px; height:15px; }
</style>
@vite('resources/js/app.js')
</head>
<body>
<main class="auth-page">
    <section class="brand-panel" aria-label="About account recovery">
        <a class="brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
        <div class="brand-content">
            <div class="brand-eyebrow">Secure account recovery</div>
            <h2 class="brand-title">Regain access without compromising your account.</h2>
            <p class="brand-description">We use a time-limited email code so only the owner of the registered email address can create a new password.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Private, email-based verification</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Time-limited six-digit code</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Strong password requirements</li>
            </ul>
        </div>
        <div class="brand-footer">Inkcredible Lending Management System</div>
    </section>

    <section class="form-panel">
        <div class="form-shell">
            <a class="brand mobile-brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
            <div class="recovery-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"/></svg></div>
            <div class="form-eyebrow">Password recovery</div>
            <h1>Forgot your password?</h1>
            <p class="form-description">Enter the email connected to your account. If it matches our records, we will send a six-digit verification code.</p>

            @if(session('status'))
                <div class="alert alert-success" role="status" aria-live="polite">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1" data-error-summary>Please check the email address and try again.</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email address <span class="required-mark" aria-hidden="true">*</span></label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror autofocus required>
                    @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                </div>

                <button class="submit-button" type="submit" id="send-reset-link"><span>Send verification code</span></button>
            </form>

            <div class="recovery-note"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 10v6M12 7h.01"/></svg><span>For your security, this page always shows the same confirmation whether or not the email is registered.</span></div>
            <a class="back-link" href="{{ route('login') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15 18-6-6 6-6"/></svg>Back to sign in</a>
        </div>
    </section>
</main>

<script>
document.querySelector('[data-error-summary]')?.focus({ preventScroll: true });
document.getElementById('forgot-password-form').addEventListener('submit', function () {
    const button = document.getElementById('send-reset-link');
    button.disabled = true;
    button.querySelector('span').textContent = 'Sending code...';
});
</script>
</body>
</html>
