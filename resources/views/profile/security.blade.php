<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Security | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style data-partial-page-style>
@include('partials.client-portal-styles')
@include('partials.settings-styles')
.email-verification-card { border-color:#c7d2fe; box-shadow:0 10px 28px rgba(79,70,229,.07); }
.email-verification-body { display:grid; grid-template-columns:48px minmax(0,1fr) auto; gap:15px; align-items:center; }
.email-verification-icon { display:grid; place-items:center; width:48px; height:48px; color:#4f46e5; background:#eef2ff; border:1px solid #d9d6fe; border-radius:13px; }
.email-verification-icon svg { width:22px; height:22px; }
.email-verification-copy strong { display:block; color:#344054; font-size:0.8125rem; }
.email-verification-copy span { display:block; margin-top:5px; color:#667085; font-size:0.625rem; line-height:1.55; overflow-wrap:anywhere; }
.email-verification-meta { padding:9px 11px; color:#067647; background:#ecfdf3; border:1px solid #abefc6; border-radius:10px; font-size:0.625rem; font-weight:700; white-space:nowrap; }
.two-factor-card { border-color:#d9d6fe; }
.two-factor-body { display:grid; grid-template-columns:minmax(0,1fr) minmax(260px,.7fr); gap:22px; align-items:start; }
.two-factor-copy { display:grid; grid-template-columns:48px minmax(0,1fr); gap:15px; align-items:start; }
.two-factor-copy strong { display:block; color:#344054; font-size:0.8125rem; }
.two-factor-copy p { margin:6px 0 0; color:#667085; font-size:0.625rem; line-height:1.6; }
.two-factor-points { display:grid; gap:7px; margin:13px 0 0; padding:0; list-style:none; }
.two-factor-points li { color:#475467; font-size:0.625rem; }
.two-factor-points li::before { margin-right:7px; color:#12b76a; font-weight:800; content:'\2713'; }
.two-factor-form { padding:15px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:11px; }
.two-factor-form .save-button { width:100%; }
.two-factor-danger { color:#b42318; background:#fff; border:1px solid #fda29b; }
.two-factor-danger:hover { background:#fef3f2; }
@media(max-width:600px){.email-verification-body{grid-template-columns:42px minmax(0,1fr)}.email-verification-meta{grid-column:1/-1;text-align:center}}
@media(max-width:760px){.two-factor-body{grid-template-columns:1fr}}
</style>
</head>
<body>
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1" data-partial-page><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Settings</h1><p class="subtitle">Manage your profile, verification, security, and accessibility preferences.</p></div><div class="top-actions"><a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a></div></header>
    @include('partials.settings-tabs', ['activeSettings' => 'security'])
    <div class="settings-tab-content" id="settings-tab-content" role="tabpanel" tabindex="-1" data-partial-content>
    @if(session('status') === 'password-updated')<div class="alert alert-success" role="status">Password updated successfully.</div>@endif
    @if(session('status') === 'two-factor-enabled')<div class="alert alert-success" role="status">Two-factor authentication is now enabled.</div>@endif
    @if(session('status') === 'two-factor-disabled')<div class="alert alert-success" role="status">Two-factor authentication has been disabled.</div>@endif
    @if($errors->has('two_factor'))<div class="alert alert-error" role="alert">{{ $errors->first('two_factor') }}</div>@endif
    <div class="security-grid">
        <section class="panel email-verification-card"><div class="panel-header"><div><h2 class="panel-title">Email verification</h2><p class="panel-description">Your email is confirmed once when your account is created or whenever you change it.</p></div><span class="badge {{ auth()->user()->hasVerifiedEmail() ? 'badge-success' : 'badge-warning' }}">{{ auth()->user()->hasVerifiedEmail() ? 'Verified' : 'Pending' }}</span></div><div class="panel-body email-verification-body">
            <span class="email-verification-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6z"/><path stroke-linecap="round" stroke-width="1.8" d="M9 10h.01M12 10h.01M15 10h.01M9 14h.01M12 14h.01M15 14h.01"/></svg></span>
            <span class="email-verification-copy"><strong>{{ auth()->user()->email }}</strong><span>This verification is not requested again during normal sign-in. A new code is required only if you change your email address.</span></span>
            <span class="email-verification-meta">One-time check</span>
        </div></section>
        <section class="panel two-factor-card"><div class="panel-header"><div><h2 class="panel-title">Two-factor authentication</h2><p class="panel-description">Require a temporary email code after your password whenever you sign in.</p></div><span class="badge {{ auth()->user()->hasTwoFactorAuthenticationEnabled() ? 'badge-success' : 'badge-neutral' }}">{{ auth()->user()->hasTwoFactorAuthenticationEnabled() ? 'Enabled' : 'Optional' }}</span></div><div class="panel-body two-factor-body">
            <div class="two-factor-copy"><span class="email-verification-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 10V8a5 5 0 0 1 10 0v2M5 10h14v10H5zM12 14v2"/></svg></span><span><strong>{{ auth()->user()->hasTwoFactorAuthenticationEnabled() ? 'Extra sign-in protection is active' : 'Protect your account with a second step' }}</strong><p>A six-digit code will be sent to {{ auth()->user()->email }}. Codes expire after ten minutes and can only be used once.</p><ul class="two-factor-points"><li>Password and email code required</li><li>Five code attempts per challenge</li><li>Other sessions revoked when disabled</li></ul></span></div>
            @include('partials.two-factor-toggle', ['twoFactorUser' => auth()->user()])
        </div></section>
        <section class="panel"><div class="panel-header"><div><h2 class="panel-title">Update password</h2><p class="panel-description">Use a strong password that you do not use elsewhere.</p></div><span class="required-note"><span class="required-asterisk">*</span> Required fields</span></div><div class="panel-body">
            <form method="POST" action="{{ route('password.update') }}">@csrf @method('put')
                <div class="form-group"><label class="form-label" for="current-password">Current password <span class="required-asterisk" aria-hidden="true">*</span></label><input class="form-control" type="password" name="current_password" id="current-password" autocomplete="current-password" required>@foreach($errors->updatePassword->get('current_password') as $message)<p class="field-error">{{ $message }}</p>@endforeach</div>
                <div class="form-group"><label class="form-label" for="new-password">New password <span class="required-asterisk" aria-hidden="true">*</span></label><input class="form-control" type="password" name="password" id="new-password" autocomplete="new-password" required>@foreach($errors->updatePassword->get('password') as $message)<p class="field-error">{{ $message }}</p>@endforeach</div>
                <div class="form-group"><label class="form-label" for="confirm-password">Confirm new password <span class="required-asterisk" aria-hidden="true">*</span></label><input class="form-control" type="password" name="password_confirmation" id="confirm-password" autocomplete="new-password" required></div>
                <button class="save-button" type="submit">Update password</button>
            </form>
        </div></section>
    </div>
    </div>
</div></main>
</body></html>
