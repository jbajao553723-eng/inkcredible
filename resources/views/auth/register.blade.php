<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | Inkcredible</title>
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
            <div class="brand-eyebrow">Get started</div>
            <h2 class="brand-title">A simpler way to manage your loan journey.</h2>
            <p class="brand-description">Create your account to request a loan, follow its approval status, and stay on top of every payment.</p>
            <ul class="feature-list">
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Quick online loan requests</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Real-time balance overview</li>
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Clear transaction records</li>
            </ul>
        </div>

        <div class="brand-footer">Inkcredible Lending Management System</div>
    </section>

    <section class="form-panel">
        <div class="form-shell register">
            <a class="brand mobile-brand" href="{{ route('login') }}"><span class="brand-mark">I</span><span>Inkcredible</span></a>
            <div class="form-eyebrow">Client registration</div>
            <h1>Create your account</h1>
            <p class="form-description">Set up your profile to start submitting and managing loan requests.</p>

            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="first-name">First name</label>
                        <input type="text" name="first_name" id="first-name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name') }}" placeholder="Juan" autocomplete="given-name" maxlength="100" autofocus required>
                        @error('first_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last-name">Last name</label>
                        <input type="text" name="last_name" id="last-name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" placeholder="Dela Cruz" autocomplete="family-name" maxlength="100" required>
                        @error('last_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact-number">Contact number</label>
                        <input type="tel" name="contact_number" id="contact-number" class="form-control @error('contact_number') is-invalid @enderror"
                               value="{{ old('contact_number') }}" placeholder="09XX XXX XXXX" autocomplete="tel" maxlength="30" required>
                        @error('contact_number')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="age">Age</label>
                        <input type="number" name="age" id="age" class="form-control @error('age') is-invalid @enderror"
                               value="{{ old('age') }}" min="18" max="120" placeholder="Your age" inputmode="numeric" required>
                        @error('age')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Complete address</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                              placeholder="Street, barangay, city, and province" autocomplete="street-address" maxlength="500" required>{{ old('address') }}</textarea>
                    @error('address')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror"
                                   placeholder="Create a password" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show password">Show</button>
                        </div>
                        <p class="field-help">Use at least 8 characters.</p>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password-confirmation">Confirm password</label>
                        <div class="input-wrap">
                            <input type="password" name="password_confirmation" id="password-confirmation" class="form-control password-input"
                                   placeholder="Repeat your password" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-toggle-password="password-confirmation" aria-label="Show password confirmation">Show</button>
                        </div>
                    </div>
                </div>

                <label class="consent-box">
                    <input type="checkbox" name="terms_accepted" value="1" @checked(old('terms_accepted')) required>
                    <span>I confirm that my information is accurate and I have read and agree to the <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms and Conditions</a>, including the privacy, acceptable-use, and account provisions.</span>
                </label>
                @error('terms_accepted')<p class="field-error" style="margin-top:-12px; margin-bottom:16px">You must accept the Terms and Conditions to create an account.</p>@enderror

                <button class="submit-button" type="submit" id="submit-register"><span>Create account</span></button>
                <p class="terms-note">Terms version {{ config('legal.account_terms_version') }} · Your acceptance date is recorded.</p>
            </form>

            <div class="divider">Already registered?</div>
            <p class="auth-switch">Return to your account securely. <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
</main>

<script>
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.togglePassword);
        const showing = input.type === 'text';
        input.type = showing ? 'password' : 'text';
        this.textContent = showing ? 'Show' : 'Hide';
        this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});

document.getElementById('register-form').addEventListener('submit', function () {
    const button = document.getElementById('submit-register');
    button.disabled = true;
    button.querySelector('span').textContent = 'Creating account…';
});
</script>
</body>
</html>
