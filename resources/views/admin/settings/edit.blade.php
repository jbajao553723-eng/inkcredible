<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings | Inkcredible {{ $user->isSuperAdmin() ? 'Security' : 'Admin' }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.settings-shell { max-width:1120px; }
.settings-nav { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin-bottom:22px; }
.settings-nav-link { display:flex; min-width:0; align-items:center; gap:11px; padding:14px 15px; color:#475467; background:#fff; border:1px solid var(--border); border-radius:13px; text-decoration:none; transition:.16s ease; }
.settings-nav-link:hover,.settings-nav-link.active { color:var(--primary); background:var(--primary-soft); border-color:rgba(var(--primary-rgb),.32); transform:translateY(-1px); }
.settings-nav-icon { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; color:var(--primary); background:var(--primary-soft); border-radius:9px; }
.settings-nav-icon svg { width:17px; height:17px; }
.settings-nav-copy { min-width:0; }.settings-nav-copy strong,.settings-nav-copy small { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }.settings-nav-copy strong { color:#344054; font-size:11px; }.settings-nav-copy small { margin-top:3px; color:#98a2b3; font-size:9px; }
.settings-layout { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:20px; align-items:start; }
.settings-stack { display:grid; gap:20px; min-width:0; }
.settings-aside { position:sticky; top:24px; display:grid; gap:14px; }
.account-summary { position:relative; padding:22px; overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:0 8px 24px rgba(16,24,40,.055); }
.account-summary::before { position:absolute; inset:0 0 auto; height:4px; background:linear-gradient(90deg,#4f46e5,#8b5cf6); content:''; }
.account-summary-head { display:flex; align-items:center; gap:13px; }
.account-avatar-large { display:grid; place-items:center; width:50px; height:50px; flex:0 0 50px; color:#4338ca; background:linear-gradient(145deg,#eef2ff,#e0e7ff); border:1px solid #c7d2fe; border-radius:14px; font-size:15px; font-weight:700; letter-spacing:.02em; }
.account-summary-copy { min-width:0; }
.account-summary h2 { margin:0; color:#101828; font-size:15px; font-weight:700; letter-spacing:-.02em; line-height:1.3; }.account-summary p { margin:4px 0 0; color:#667085; font-size:10px; line-height:1.45; overflow-wrap:anywhere; }.account-role { display:inline-flex; align-items:center; gap:6px; margin-top:16px; padding:6px 9px; color:#4338ca; background:#f4f3ff; border:1px solid #d9d6fe; border-radius:999px; font-size:9px; font-weight:700; }.account-role::before { width:6px; height:6px; background:#6366f1; border-radius:50%; content:''; }
.security-summary { padding:17px; background:#fff; border:1px solid var(--border); border-radius:14px; }
.security-summary strong { display:block; color:#344054; font-size:11px; }.security-summary p { margin:6px 0 0; color:#667085; font-size:9px; line-height:1.55; }.security-status { display:flex; align-items:center; gap:7px; margin-top:12px; color:#067647; font-size:9px; font-weight:700; }.security-status::before { width:8px; height:8px; background:#12b76a; border-radius:50%; box-shadow:0 0 0 3px #d1fadf; content:''; }
.settings-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
.settings-form-group { min-width:0; }.settings-form-group.full { grid-column:1/-1; }
.settings-label { display:block; margin-bottom:6px; color:#344054; font-size:10px; font-weight:700; }.settings-label span { color:#98a2b3; font-weight:500; }
.settings-input { width:100%; min-height:42px; padding:9px 11px; color:#101828; background:#fff; border:1px solid #d0d5dd; border-radius:9px; outline:0; font-size:12px; }.settings-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(var(--primary-rgb),.12); }.settings-input.is-invalid { border-color:#f04438; }
.settings-help { margin:6px 0 0; color:#98a2b3; font-size:9px; line-height:1.5; }.settings-error { margin:5px 0 0; color:#b42318; font-size:9px; }
.settings-actions { display:flex; justify-content:flex-end; gap:9px; margin-top:20px; padding-top:17px; border-top:1px solid #eaecf0; }
.security-callout { display:flex; gap:12px; margin-bottom:18px; padding:14px; color:#475467; background:#f8fafc; border:1px solid #eaecf0; border-radius:11px; font-size:10px; line-height:1.55; }.security-callout-icon { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; color:#067647; background:#ecfdf3; border-radius:9px; }.security-callout-icon svg { width:17px; height:17px; }.security-callout strong { display:block; margin-bottom:3px; color:#344054; font-size:11px; }
.motion-option { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:18px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:12px; }.motion-option-copy { display:flex; align-items:flex-start; gap:12px; }.motion-option-icon { display:grid; place-items:center; width:38px; height:38px; flex:0 0 38px; color:#4f46e5; background:#eef2ff; border-radius:10px; }.motion-option-icon svg { width:19px; height:19px; }.motion-option-copy strong,.motion-option-copy small { display:block; }.motion-option-copy strong { color:#344054; font-size:12px; }.motion-option-copy small { max-width:480px; margin-top:4px; color:#667085; font-size:10px; line-height:1.5; }.toggle { position:relative; width:42px; height:24px; flex:0 0 42px; }.toggle input { position:absolute; opacity:0; }.toggle-track { position:absolute; inset:0; background:#d0d5dd; border-radius:999px; cursor:pointer; transition:.15s; }.toggle-track::after { position:absolute; top:3px; left:3px; width:18px; height:18px; background:#fff; border-radius:50%; box-shadow:0 1px 3px rgba(16,24,40,.25); content:''; transition:.15s; }.toggle input:focus-visible + .toggle-track { outline:3px solid rgba(99,102,241,.25); outline-offset:2px; }.toggle input:checked + .toggle-track { background:var(--primary); }.toggle input:checked + .toggle-track::after { transform:translateX(18px); }
.password-wrap { position:relative; }.password-wrap .settings-input { padding-right:44px; }.password-toggle-setting { position:absolute; top:50%; right:7px; display:grid; place-items:center; width:29px; height:29px; padding:0; color:#667085; background:#f2f4f7; border:0; border-radius:7px; transform:translateY(-50%); cursor:pointer; }.password-toggle-setting svg { width:15px; height:15px; }
@media(max-width:900px){.settings-layout{grid-template-columns:1fr}.settings-aside{position:static;grid-template-columns:1fr 1fr}}
@media(max-width:680px){.settings-nav,.settings-form-grid,.settings-aside{grid-template-columns:1fr}.settings-form-group.full{grid-column:auto}.settings-actions{flex-direction:column}.settings-actions .button{width:100%}.motion-option{align-items:flex-start}}
</style>
</head>
<body>
@php
    $section = request('section', $errors->password->any() ? 'security' : ($errors->motion->any() ? 'motion' : 'profile'));
    $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
@endphp
@include('partials.admin-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Administrator account</div><h1>Settings</h1><p class="subtitle">Manage your account details, security, and motion accessibility.</p></div></header>

    @if(session('status') === 'profile-updated')<div class="alert alert-success" role="status"><strong>Profile updated</strong>Your administrator information has been saved.</div>@endif
    @if(session('status') === 'password-updated')<div class="alert alert-success" role="status"><strong>Password updated</strong>Other administrator sessions were revoked for your security.</div>@endif
    @if(session('status') === 'motion-updated')<div class="alert alert-success" role="status"><strong>Motion preference updated</strong>Your setting now applies across the administrator workspace.</div>@endif

    <nav class="settings-nav" aria-label="Settings sections">
        <a class="settings-nav-link {{ $section === 'profile' ? 'active' : '' }}" href="#profile"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg></span><span class="settings-nav-copy"><strong>Profile</strong><small>Name and contact details</small></span></a>
        <a class="settings-nav-link {{ $section === 'security' ? 'active' : '' }}" href="#security"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span><span class="settings-nav-copy"><strong>Security</strong><small>Password and account access</small></span></a>
        <a class="settings-nav-link {{ $section === 'motion' ? 'active' : '' }}" href="#motion"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span class="settings-nav-copy"><strong>Motion</strong><small>Animation accessibility</small></span></a>
    </nav>

    <div class="settings-layout">
        <div class="settings-stack">
            <section class="panel" id="profile"><div class="panel-header"><div><h2 class="panel-title">Profile information</h2><p class="panel-description">These details identify you throughout the administrator workspace and audit history.</p></div><span class="badge badge-neutral">{{ $user->isSuperAdmin() ? 'Superadmin' : 'Admin' }}</span></div><div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.profile.update') }}">@csrf @method('PATCH')
                    <div class="settings-form-grid">
                        <div class="settings-form-group"><label class="settings-label" for="first-name">First name</label><input class="settings-input @error('first_name','profile') is-invalid @enderror" id="first-name" name="first_name" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" required>@error('first_name','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="last-name">Last name</label><input class="settings-input @error('last_name','profile') is-invalid @enderror" id="last-name" name="last_name" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" required>@error('last_name','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="admin-email">Email address</label><input class="settings-input @error('email','profile') is-invalid @enderror" id="admin-email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>@error('email','profile')<p class="settings-error">{{ $message }}</p>@enderror<p class="settings-help">Used for administrator account notices and password recovery.</p></div>
                        <div class="settings-form-group"><label class="settings-label" for="contact-number">Contact number</label><input class="settings-input @error('contact_number','profile') is-invalid @enderror" id="contact-number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" autocomplete="tel" required>@error('contact_number','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group full"><label class="settings-label" for="profile-current-password">Current password <span>Required only when changing email</span></label><div class="password-wrap"><input class="settings-input @error('current_password','profile') is-invalid @enderror" id="profile-current-password" type="password" name="current_password" autocomplete="current-password"><button class="password-toggle-setting" type="button" data-password-toggle="profile-current-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('current_password','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="settings-actions"><button class="button button-primary" type="submit">Save profile</button></div>
                </form>
            </div></section>

            <section class="panel" id="security"><div class="panel-header"><div><h2 class="panel-title">Password and sign-in security</h2><p class="panel-description">Change your password and review the protection applied to administrator access.</p></div><span class="badge badge-success">Protected</span></div><div class="panel-body">
                <div class="security-callout"><span class="security-callout-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 12l2 2 4-4"/></svg></span><span><strong>Administrator sign-in protection</strong>Accounts are protected by password verification, login throttling, active-account checks, and revocable server-side sessions.</span></div>
                <form method="POST" action="{{ route('admin.settings.password.update') }}">@csrf @method('PUT')
                    <div class="settings-form-grid">
                        <div class="settings-form-group full"><label class="settings-label" for="current-password">Current password</label><div class="password-wrap"><input class="settings-input @error('current_password','password') is-invalid @enderror" id="current-password" type="password" name="current_password" autocomplete="current-password" required><button class="password-toggle-setting" type="button" data-password-toggle="current-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('current_password','password')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="new-password">New password</label><div class="password-wrap"><input class="settings-input @error('password','password') is-invalid @enderror" id="new-password" type="password" name="password" autocomplete="new-password" required><button class="password-toggle-setting" type="button" data-password-toggle="new-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('password','password')<p class="settings-error">{{ $message }}</p>@enderror<p class="settings-help">At least 8 characters with a number or symbol.</p></div>
                        <div class="settings-form-group"><label class="settings-label" for="confirm-password">Confirm new password</label><div class="password-wrap"><input class="settings-input" id="confirm-password" type="password" name="password_confirmation" autocomplete="new-password" required><button class="password-toggle-setting" type="button" data-password-toggle="confirm-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div></div>
                    </div>
                    <div class="settings-actions"><button class="button button-primary" type="submit">Update password</button></div>
                </form>
            </div></section>

            <section class="panel" id="motion"><div class="panel-header"><div><h2 class="panel-title">Motion accessibility</h2><p class="panel-description">Control animated transitions throughout your administrator workspace.</p></div></div><div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.motion.update') }}">@csrf @method('PATCH')
                    <label class="motion-option"><span class="motion-option-copy"><span class="motion-option-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span><strong>Reduce motion</strong><small>Minimize page transitions, entrance effects, and animated interface feedback. This can make navigation more comfortable.</small></span></span><span class="toggle"><input type="checkbox" name="reduce_motion" value="1" @checked(old('reduce_motion', $reduceMotion))><i class="toggle-track"></i></span></label>
                    @if($errors->motion->any())<div class="alert alert-error" style="margin-top:16px"><strong>Setting not saved</strong>{{ $errors->motion->first() }}</div>@endif
                    <div class="settings-actions"><button class="button button-primary" type="submit">Save motion setting</button></div>
                </form>
            </div></section>
        </div>

        <aside class="settings-aside">
            <section class="account-summary"><div class="account-summary-head"><span class="account-avatar-large">{{ $initials ?: 'A' }}</span><span class="account-summary-copy"><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></span></div><span class="account-role">{{ $user->isSuperAdmin() ? 'Super Administrator' : 'Administrator' }}</span></section>
            <section class="security-summary"><strong>Account protection</strong><p>Your account uses password verification, login throttling, active-account enforcement, and server-side sessions.</p><span class="security-status">Security controls active</span></section>
        </aside>
    </div>
</div></main>
<script>
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', function () {
        const input = document.getElementById(this.dataset.passwordToggle);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
});
</script>
</body></html>
