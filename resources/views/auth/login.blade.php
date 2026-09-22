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
                <li><span class="feature-check"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 12 4 4 8-9"/></svg></span>Secure PayMongo checkout</li>
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
                <div class="alert alert-success" role="status">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    We could not sign you in. Please check your email and password.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="you@example.com" autocomplete="username" autofocus required>
                    @error('email')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="password" class="form-control password-input @error('password') is-invalid @enderror"
                               placeholder="Enter your password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show password">Show</button>
                    </div>
                    @error('password')<p class="field-error">{{ $message }}</p>@enderror
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
        this.textContent = showing ? 'Show' : 'Hide';
        this.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
});

document.getElementById('login-form').addEventListener('submit', function () {
    const button = document.getElementById('submit-login');
    button.disabled = true;
    button.querySelector('span').textContent = 'Signing in…';
});
</script>
</body>
</html>
