<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.auth-styles') @include('partials.motion-styles')</style>
@vite('resources/js/app.js')
</head>

<body>
<main class="auth-page">
    <section class="brand-panel" aria-label="About Inkcredible">
        <a class="brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>

        <div class="brand-content">
            <div class="brand-eyebrow">Lending made clearer</div>
            <h2 class="brand-title">Your finances, organized in one place.</h2>
            <p class="brand-description">Review your loans, monitor balances, and make secure payments from one straightforward account.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Clear balances and repayment progress</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Organized payment history</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Protected and verified payments</li>
            </ul>
        </div>

        <div class="brand-footer">Inkcredible Lending Management System</div>
    </section>

    <section class="form-panel">
        <div class="form-shell">
            <a class="brand mobile-brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
            <div class="form-eyebrow">Account access</div>
            <h1>Welcome back</h1>
            <p class="form-description">Enter your account details to continue to your dashboard.</p>

            @if(session('status'))
                <div class="alert alert-success" role="status" aria-live="polite">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" role="alert" tabindex="-1" data-error-summary>
                    We could not sign you in. Please check your email and password.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email address <span class="required-mark" aria-hidden="true">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="you@example.com" autocomplete="username" inputmode="email"
                           autocapitalize="none" spellcheck="false" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror autofocus required>
                    @error('email')<p class="field-error" id="email-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required-mark" aria-hidden="true">*</span></label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror"
                               placeholder="Enter your password" autocomplete="current-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror required>
                        <button class="password-toggle" type="button" data-toggle-password="password" aria-controls="password" aria-pressed="false" aria-label="Show password"><svg class="eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg><svg class="eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.7 6.1A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8M6.6 6.6C4 8.3 2.5 12 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg></button>
                    </div>
                    @error('password')<p class="field-error" id="password-error">{{ $message }}</p>@enderror
                    <p class="field-feedback" id="caps-lock-message" aria-live="polite"></p>
                </div>

                <div class="form-options">
                    <label class="checkbox-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Keep me signed in</label>
                    @if(Route::has('password.request'))
                        <a class="text-link" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button class="submit-button" type="submit" id="submit-login"><span>Sign in</span></button>
            </form>

            <div class="divider">New to Inkcredible?</div>
            <p class="auth-switch">Create an account to submit and manage your loans. <a class="text-link" href="{{ route('register') }}">Create account</a></p>
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
const capsLockMessage = document.getElementById('caps-lock-message');
passwordInput.addEventListener('keyup', (event) => {
    capsLockMessage.textContent = event.getModifierState('CapsLock') ? 'Caps Lock is on.' : '';
});
passwordInput.addEventListener('blur', () => capsLockMessage.textContent = '');

document.querySelector('[data-error-summary]')?.focus({ preventScroll: true });

document.getElementById('login-form').addEventListener('submit', function () {
    const button = document.getElementById('submit-login');
    button.disabled = true;
    button.querySelector('span').textContent = 'Signing in…';
});
</script>
</body>
</html>
