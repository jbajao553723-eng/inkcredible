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
.settings-nav { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px; margin-bottom:22px; padding:5px; background:#f2f4f7; border:1px solid #e4e7ec; border-radius:12px; }
.settings-nav-link { display:flex; min-width:0; align-items:center; justify-content:center; gap:8px; padding:10px 14px; color:#475467; background:transparent; border:1px solid transparent; border-radius:8px; text-decoration:none; transition:.16s ease; }
.settings-nav-link:hover { color:var(--primary); background:rgba(255,255,255,.7); }.settings-nav-link.active { color:var(--primary); background:#fff; border-color:#e4e7ec; box-shadow:0 2px 6px rgba(16,24,40,.08); }
.settings-nav-icon { display:grid; place-items:center; width:23px; height:23px; flex:0 0 23px; color:var(--primary); }
.settings-nav-icon svg { width:17px; height:17px; }
.settings-nav-copy { min-width:0; }.settings-nav-copy strong { display:block; overflow:hidden; color:#344054; font-size:11px; text-overflow:ellipsis; white-space:nowrap; }.settings-nav-link.active .settings-nav-copy strong { color:var(--primary); }
.settings-layout { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:20px; align-items:start; }
.settings-stack { display:grid; gap:20px; min-width:0; }
.settings-aside { position:sticky; top:24px; display:grid; gap:14px; }
.account-summary { position:relative; padding:22px; overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:0 8px 24px rgba(16,24,40,.055); }
.account-summary::before { position:absolute; inset:0 0 auto; height:4px; background:linear-gradient(90deg,#4f46e5,#8b5cf6); content:''; }
.account-summary-head { display:flex; align-items:center; gap:13px; }
.account-avatar-large { display:grid; place-items:center; width:50px; height:50px; flex:0 0 50px; overflow:hidden; color:#4338ca; background:linear-gradient(145deg,#eef2ff,#e0e7ff); border:1px solid #c7d2fe; border-radius:14px; font-size:15px; font-weight:700; letter-spacing:.02em; }.account-avatar-large img { width:100%; height:100%; object-fit:cover; }
.account-summary-copy { min-width:0; }
.account-summary h2 { margin:0; color:#101828; font-size:15px; font-weight:700; letter-spacing:-.02em; line-height:1.3; }.account-summary p { margin:4px 0 0; color:#667085; font-size:10px; line-height:1.45; overflow-wrap:anywhere; }.account-role { display:inline-flex; align-items:center; gap:6px; margin-top:16px; padding:6px 9px; color:#4338ca; background:#f4f3ff; border:1px solid #d9d6fe; border-radius:999px; font-size:9px; font-weight:700; }.account-role::before { width:6px; height:6px; background:#6366f1; border-radius:50%; content:''; }
.security-summary { padding:17px; background:#fff; border:1px solid var(--border); border-radius:14px; }
.security-summary strong { display:block; color:#344054; font-size:11px; }.security-summary p { margin:6px 0 0; color:#667085; font-size:9px; line-height:1.55; }.security-status { display:flex; align-items:center; gap:7px; margin-top:12px; color:#067647; font-size:9px; font-weight:700; }.security-status::before { width:8px; height:8px; background:#12b76a; border-radius:50%; box-shadow:0 0 0 3px #d1fadf; content:''; }
.settings-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
.settings-form-group { min-width:0; }.settings-form-group.full { grid-column:1/-1; }
.settings-label { display:block; margin-bottom:6px; color:#344054; font-size:10px; font-weight:700; }.settings-label span { color:#98a2b3; font-weight:500; }.settings-label .required-asterisk { margin-left:2px; color:#d92d20; font-size:11px; font-weight:700; }
.settings-input { width:100%; min-height:42px; padding:9px 11px; color:#101828; background:#fff; border:1px solid #d0d5dd; border-radius:9px; outline:0; font-size:12px; }.settings-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(var(--primary-rgb),.12); }.settings-input.is-invalid { border-color:#f04438; }
.settings-help { margin:6px 0 0; color:#98a2b3; font-size:9px; line-height:1.5; }.settings-error { margin:5px 0 0; color:#b42318; font-size:9px; }
.settings-actions { display:flex; justify-content:flex-end; gap:9px; margin-top:20px; padding-top:17px; border-top:1px solid #eaecf0; }
.settings-save-state { display:flex; align-items:center; gap:7px; margin-right:auto; color:#98a2b3; font-size:9px; }.settings-save-state::before { width:7px; height:7px; background:#12b76a; border-radius:50%; content:''; }.settings-save-state.is-dirty { color:#b54708; }.settings-save-state.is-dirty::before { background:#f79009; }
.admin-photo-row { display:flex; align-items:center; gap:16px; margin-bottom:20px; padding:16px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:12px; }.admin-photo-preview { display:grid; place-items:center; width:64px; height:64px; flex:0 0 64px; overflow:hidden; color:#4338ca; background:#eef2ff; border:3px solid #fff; border-radius:17px; box-shadow:0 4px 12px rgba(16,24,40,.1); font-size:17px; font-weight:700; }.admin-photo-preview img { width:100%; height:100%; object-fit:cover; }.admin-photo-preview [hidden] { display:none; }.admin-photo-copy { min-width:0; flex:1; }.admin-photo-copy strong,.admin-photo-copy span { display:block; }.admin-photo-copy strong { color:#344054; font-size:11px; }.admin-photo-copy span { margin-top:4px; color:#667085; font-size:9px; line-height:1.45; }.admin-photo-input { position:absolute; width:1px; height:1px; overflow:hidden; opacity:0; }.photo-select-button { display:inline-flex; margin-top:10px; padding:7px 10px; color:#4338ca; background:#fff; border:1px solid #c7d2fe; border-radius:8px; font-size:9px; font-weight:700; cursor:pointer; }
.password-strength { margin-top:9px; }.strength-track { display:grid; grid-template-columns:repeat(3,1fr); gap:4px; }.strength-track i { height:4px; background:#eaecf0; border-radius:999px; transition:background-color .16s ease; }.password-strength[data-level="1"] i:nth-child(1) { background:#f04438; }.password-strength[data-level="2"] i:nth-child(-n+2) { background:#f79009; }.password-strength[data-level="3"] i { background:#12b76a; }.strength-copy { display:flex; justify-content:space-between; gap:10px; margin-top:6px; color:#98a2b3; font-size:9px; }.password-match { margin-top:6px; color:#667085; font-size:9px; }.password-match.is-valid { color:#067647; }.password-match.is-invalid { color:#b42318; }
.security-callout { display:flex; gap:12px; margin-bottom:18px; padding:14px; color:#475467; background:#f8fafc; border:1px solid #eaecf0; border-radius:11px; font-size:10px; line-height:1.55; }.security-callout-icon { display:grid; place-items:center; width:34px; height:34px; flex:0 0 34px; color:#067647; background:#ecfdf3; border-radius:9px; }.security-callout-icon svg { width:17px; height:17px; }.security-callout strong { display:block; margin-bottom:3px; color:#344054; font-size:11px; }
.motion-option { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:18px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:12px; }.motion-option-copy { display:flex; align-items:flex-start; gap:12px; }.motion-option-icon { display:grid; place-items:center; width:38px; height:38px; flex:0 0 38px; color:#4f46e5; background:#eef2ff; border-radius:10px; }.motion-option-icon svg { width:19px; height:19px; }.motion-option-copy strong,.motion-option-copy small { display:block; }.motion-option-copy strong { color:#344054; font-size:12px; }.motion-option-copy small { max-width:480px; margin-top:4px; color:#667085; font-size:10px; line-height:1.5; }.toggle { position:relative; width:42px; height:24px; flex:0 0 42px; }.toggle input { position:absolute; opacity:0; }.toggle-track { position:absolute; inset:0; background:#d0d5dd; border-radius:999px; cursor:pointer; transition:.15s; }.toggle-track::after { position:absolute; top:3px; left:3px; width:18px; height:18px; background:#fff; border-radius:50%; box-shadow:0 1px 3px rgba(16,24,40,.25); content:''; transition:.15s; }.toggle input:focus-visible + .toggle-track { outline:3px solid rgba(99,102,241,.25); outline-offset:2px; }.toggle input:checked + .toggle-track { background:var(--primary); }.toggle input:checked + .toggle-track::after { transform:translateX(18px); }
.motion-preview { display:flex; align-items:center; justify-content:center; gap:8px; min-height:96px; margin-top:14px; overflow:hidden; background:linear-gradient(135deg,#f8fafc,#eef2ff); border:1px solid #e4e7ec; border-radius:12px; }.motion-preview span { width:12px; height:12px; background:#6366f1; border-radius:50%; animation:settings-preview 1.25s ease-in-out infinite alternate; }.motion-preview span:nth-child(2) { animation-delay:.16s; }.motion-preview span:nth-child(3) { animation-delay:.32s; }.motion-preview.is-reduced span { animation:none; opacity:.62; }.motion-preview-note { margin:8px 0 0; color:#667085; font-size:9px; text-align:center; }@keyframes settings-preview { from { opacity:.35; transform:translateY(7px) scale(.86); } to { opacity:1; transform:translateY(-7px) scale(1); } }
.password-wrap { position:relative; }.password-wrap .settings-input { padding-right:44px; }.password-toggle-setting { position:absolute; top:50%; right:7px; display:grid; place-items:center; width:29px; height:29px; padding:0; color:#667085; background:#f2f4f7; border:0; border-radius:7px; transform:translateY(-50%); cursor:pointer; }.password-toggle-setting svg { width:15px; height:15px; }
@media(max-width:900px){.settings-layout{grid-template-columns:1fr}.settings-aside{position:static;grid-template-columns:1fr 1fr}}
@media(max-width:680px){.settings-nav,.settings-form-grid,.settings-aside{grid-template-columns:1fr}.settings-form-group.full{grid-column:auto}.settings-actions{align-items:stretch;flex-direction:column}.settings-save-state{margin-right:0}.settings-actions .button{width:100%}.motion-option,.admin-photo-row{align-items:flex-start}.admin-photo-row{flex-wrap:wrap}}
.settings-stack>[data-settings-panel][hidden],.security-summary>[data-settings-summary][hidden]{display:none}.settings-stack>[data-settings-panel].is-opening{animation:settings-panel-open .28s ease both}@keyframes settings-panel-open{from{opacity:0;transform:translateY(7px)}to{opacity:1;transform:none}}@media(prefers-reduced-motion:reduce){.settings-stack>[data-settings-panel].is-opening{animation:none}}
</style>
</head>
<body>
@php
    $requestedSection = request('section', 'profile');
    $section = $errors->password->any()
        ? 'security'
        : ($errors->motion->any() ? 'motion' : ($errors->profile->any() ? 'profile' : (in_array($requestedSection, ['profile', 'security', 'motion'], true) ? $requestedSection : 'profile')));
    $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
@endphp
@include('partials.admin-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Administrator account</div><h1>Settings</h1><p class="subtitle">Manage your account details, security, and motion accessibility.</p></div></header>

    @if(session('status') === 'profile-updated')<div class="alert alert-success" role="status"><strong>Profile updated</strong>Your administrator information has been saved.</div>@endif
    @if(session('status') === 'password-updated')<div class="alert alert-success" role="status"><strong>Password updated</strong>Other administrator sessions were revoked for your security.</div>@endif
    @if(session('status') === 'motion-updated')<div class="alert alert-success" role="status"><strong>Motion preference updated</strong>Your setting now applies across the administrator workspace.</div>@endif

    <nav class="settings-nav" aria-label="Settings sections" role="tablist" data-admin-settings-tabs>
        <a class="settings-nav-link {{ $section === 'profile' ? 'active' : '' }}" id="admin-settings-tab-profile" href="{{ route('admin.settings.edit', ['section' => 'profile']) }}" role="tab" aria-controls="admin-settings-panel-profile" aria-selected="{{ $section === 'profile' ? 'true' : 'false' }}" tabindex="{{ $section === 'profile' ? '0' : '-1' }}" data-admin-settings-tab="profile"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg></span><span class="settings-nav-copy"><strong>Profile</strong></span></a>
        <a class="settings-nav-link {{ $section === 'security' ? 'active' : '' }}" id="admin-settings-tab-security" href="{{ route('admin.settings.edit', ['section' => 'security']) }}" role="tab" aria-controls="admin-settings-panel-security" aria-selected="{{ $section === 'security' ? 'true' : 'false' }}" tabindex="{{ $section === 'security' ? '0' : '-1' }}" data-admin-settings-tab="security"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span><span class="settings-nav-copy"><strong>Security</strong></span></a>
        <a class="settings-nav-link {{ $section === 'motion' ? 'active' : '' }}" id="admin-settings-tab-motion" href="{{ route('admin.settings.edit', ['section' => 'motion']) }}" role="tab" aria-controls="admin-settings-panel-motion" aria-selected="{{ $section === 'motion' ? 'true' : 'false' }}" tabindex="{{ $section === 'motion' ? '0' : '-1' }}" data-admin-settings-tab="motion"><span class="settings-nav-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span class="settings-nav-copy"><strong>Accessibility</strong></span></a>
    </nav>

    <div class="settings-layout">
        <div class="settings-stack">
            <section class="panel" id="admin-settings-panel-profile" role="tabpanel" aria-labelledby="admin-settings-tab-profile" data-settings-panel="profile" @if($section !== 'profile') hidden @endif><div class="panel-header"><div><h2 class="panel-title">Profile information</h2><p class="panel-description">These details identify you throughout the administrator workspace and audit history.</p></div><span class="badge badge-neutral">{{ $user->isSuperAdmin() ? 'Superadmin' : 'Admin' }}</span></div><div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.profile.update') }}" enctype="multipart/form-data" data-settings-form>@csrf @method('PATCH')
                    <div class="admin-photo-row">
                        <span class="admin-photo-preview"><img id="admin-photo-preview" src="{{ $user->profile_photo_path ? route('profile.photo', ['v' => $user->updated_at?->timestamp]) : '' }}" alt="{{ $user->name }} profile photo" @if(! $user->profile_photo_path) hidden @endif><span id="admin-photo-initials" @if($user->profile_photo_path) hidden @endif>{{ $initials ?: 'A' }}</span></span>
                        <span class="admin-photo-copy"><strong>Profile photo</strong><span id="admin-photo-copy">Add a clear JPG, PNG, or WebP image up to 2 MB.</span><label class="photo-select-button" for="admin-profile-photo">{{ $user->profile_photo_path ? 'Replace photo' : 'Choose photo' }}</label><input class="admin-photo-input" id="admin-profile-photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"></span>
                    </div>
                    @error('profile_photo','profile')<p class="settings-error" style="margin-top:-14px;margin-bottom:16px">{{ $message }}</p>@enderror
                    <div class="settings-form-grid">
                        <div class="settings-form-group"><label class="settings-label" for="first-name">First name <span class="required-asterisk" aria-hidden="true">*</span></label><input class="settings-input @error('first_name','profile') is-invalid @enderror" id="first-name" name="first_name" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" required>@error('first_name','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="last-name">Last name <span class="required-asterisk" aria-hidden="true">*</span></label><input class="settings-input @error('last_name','profile') is-invalid @enderror" id="last-name" name="last_name" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" required>@error('last_name','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="admin-email">Email address <span class="required-asterisk" aria-hidden="true">*</span></label><input class="settings-input @error('email','profile') is-invalid @enderror" id="admin-email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>@error('email','profile')<p class="settings-error">{{ $message }}</p>@enderror<p class="settings-help">Used for administrator account notices and password recovery.</p></div>
                        <div class="settings-form-group"><label class="settings-label" for="contact-number">Contact number <span class="required-asterisk" aria-hidden="true">*</span></label><input class="settings-input @error('contact_number','profile') is-invalid @enderror" id="contact-number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" autocomplete="tel" required>@error('contact_number','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group full"><label class="settings-label" for="profile-current-password">Current password <span><span class="required-asterisk" aria-hidden="true">*</span> when changing email</span></label><div class="password-wrap"><input class="settings-input @error('current_password','profile') is-invalid @enderror" id="profile-current-password" type="password" name="current_password" autocomplete="current-password"><button class="password-toggle-setting" type="button" data-password-toggle="profile-current-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('current_password','profile')<p class="settings-error">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="settings-actions"><span class="settings-save-state" data-save-state>All changes saved</span><button class="button button-primary" type="submit" data-save-button>Save profile</button></div>
                </form>
            </div></section>
            <section class="panel" id="admin-settings-panel-security" role="tabpanel" aria-labelledby="admin-settings-tab-security" data-settings-panel="security" @if($section !== 'security') hidden @endif><div class="panel-header"><div><h2 class="panel-title">Password and sign-in security</h2><p class="panel-description">Change your password and review the protection applied to administrator access.</p></div><span class="badge badge-success">Protected</span></div><div class="panel-body">
                <div class="security-callout"><span class="security-callout-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 12l2 2 4-4"/></svg></span><span><strong>Administrator sign-in protection</strong>Accounts are protected by password verification, login throttling, active-account checks, and revocable server-side sessions.</span></div>
                <form method="POST" action="{{ route('admin.settings.password.update') }}" data-settings-form>@csrf @method('PUT')
                    <div class="settings-form-grid">
                        <div class="settings-form-group full"><label class="settings-label" for="current-password">Current password <span class="required-asterisk" aria-hidden="true">*</span></label><div class="password-wrap"><input class="settings-input @error('current_password','password') is-invalid @enderror" id="current-password" type="password" name="current_password" autocomplete="current-password" required><button class="password-toggle-setting" type="button" data-password-toggle="current-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('current_password','password')<p class="settings-error">{{ $message }}</p>@enderror</div>
                        <div class="settings-form-group"><label class="settings-label" for="new-password">New password <span class="required-asterisk" aria-hidden="true">*</span></label><div class="password-wrap"><input class="settings-input @error('password','password') is-invalid @enderror" id="new-password" type="password" name="password" autocomplete="new-password" required><button class="password-toggle-setting" type="button" data-password-toggle="new-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div>@error('password','password')<p class="settings-error">{{ $message }}</p>@enderror<div class="password-strength" id="password-strength" data-level="0"><div class="strength-track"><i></i><i></i><i></i></div><div class="strength-copy"><span>8+ characters</span><strong id="strength-label">Enter a password</strong></div></div></div>
                        <div class="settings-form-group"><label class="settings-label" for="confirm-password">Confirm new password <span class="required-asterisk" aria-hidden="true">*</span></label><div class="password-wrap"><input class="settings-input" id="confirm-password" type="password" name="password_confirmation" autocomplete="new-password" required><button class="password-toggle-setting" type="button" data-password-toggle="confirm-password" aria-label="Show password"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5" stroke-width="1.8"/></svg></button></div><div class="password-match" id="password-match">Re-enter the new password.</div></div>
                    </div>
                    <div class="settings-actions"><span class="settings-save-state" data-save-state>Secure form ready</span><button class="button button-primary" type="submit" data-save-button>Update password</button></div>
                </form>
            </div></section>
            <section class="panel" id="admin-settings-panel-motion" role="tabpanel" aria-labelledby="admin-settings-tab-motion" data-settings-panel="motion" @if($section !== 'motion') hidden @endif><div class="panel-header"><div><h2 class="panel-title">Motion accessibility</h2><p class="panel-description">Control animated transitions throughout your administrator workspace.</p></div></div><div class="panel-body">
                <form method="POST" action="{{ route('admin.settings.motion.update') }}" data-settings-form>@csrf @method('PATCH')
                    <label class="motion-option"><span class="motion-option-copy"><span class="motion-option-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span><strong>Reduce motion</strong><small>Minimize page transitions, entrance effects, and animated interface feedback. This can make navigation more comfortable.</small></span></span><span class="toggle"><input type="checkbox" name="reduce_motion" value="1" @checked(old('reduce_motion', $reduceMotion))><i class="toggle-track"></i></span></label>
                    <div class="motion-preview {{ old('reduce_motion', $reduceMotion) ? 'is-reduced' : '' }}" id="motion-preview" aria-hidden="true"><span></span><span></span><span></span></div><p class="motion-preview-note" id="motion-preview-note">{{ old('reduce_motion', $reduceMotion) ? 'Reduced motion preview' : 'Standard motion preview' }}</p>
                    @if($errors->motion->any())<div class="alert alert-error" style="margin-top:16px"><strong>Setting not saved</strong>{{ $errors->motion->first() }}</div>@endif
                    <div class="settings-actions"><span class="settings-save-state" data-save-state>Preference saved</span><button class="button button-primary" type="submit" data-save-button>Save accessibility</button></div>
                </form>
            </div></section>
        </div>

        <aside class="settings-aside">
            <section class="account-summary"><div class="account-summary-head"><span class="account-avatar-large">@if($user->profile_photo_path)<img src="{{ route('profile.photo', ['v' => $user->updated_at?->timestamp]) }}" alt="">@else{{ $initials ?: 'A' }}@endif</span><span class="account-summary-copy"><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></span></div><span class="account-role">{{ $user->isSuperAdmin() ? 'Super Administrator' : 'Administrator' }}</span></section>
            <section class="security-summary">
                <span data-settings-summary="profile" @if($section !== 'profile') hidden @endif><strong>Profile visibility</strong><p>Your name and role appear in administrator activity and audit history. Keep your contact details current.</p><span class="security-status">Account active</span></span>
                <span data-settings-summary="security" @if($section !== 'security') hidden @endif><strong>Account protection</strong><p>Changing your password signs out your other administrator sessions while keeping this session active.</p><span class="security-status">Security controls active</span></span>
                <span data-settings-summary="motion" @if($section !== 'motion') hidden @endif><strong>Accessibility preference</strong><p>Reduced motion applies across the administrator workspace and is also respected by interactive previews.</p><span class="security-status">Preference synced</span></span>
            </section>
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

document.querySelectorAll('[data-settings-form]').forEach((form) => {
    const state = form.querySelector('[data-save-state]');
    const button = form.querySelector('[data-save-button]');
    const markDirty = () => {
        if (!state) return;
        state.textContent = 'Unsaved changes';
        state.classList.add('is-dirty');
    };
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
    form.addEventListener('submit', () => {
        if (button) button.textContent = 'Saving...';
    });
});

const emailInput = document.getElementById('admin-email');
const emailPassword = document.getElementById('profile-current-password');
const originalEmail = @json($user->email);
const syncEmailPassword = () => {
    if (emailPassword) emailPassword.required = emailInput?.value.trim().toLowerCase() !== originalEmail.toLowerCase();
};
emailInput?.addEventListener('input', syncEmailPassword);
syncEmailPassword();

const photoInput = document.getElementById('admin-profile-photo');
const photoPreview = document.getElementById('admin-photo-preview');
const photoInitials = document.getElementById('admin-photo-initials');
const photoCopy = document.getElementById('admin-photo-copy');
let photoObjectUrl;
photoInput?.addEventListener('change', () => {
    const file = photoInput.files?.[0];
    if (!file || !photoPreview) return;
    if (photoObjectUrl) URL.revokeObjectURL(photoObjectUrl);
    photoObjectUrl = URL.createObjectURL(file);
    photoPreview.src = photoObjectUrl;
    photoPreview.hidden = false;
    if (photoInitials) photoInitials.hidden = true;
    if (photoCopy) photoCopy.textContent = `${file.name} is ready to upload.`;
});

const newPassword = document.getElementById('new-password');
const confirmPassword = document.getElementById('confirm-password');
const strength = document.getElementById('password-strength');
const strengthLabel = document.getElementById('strength-label');
const passwordMatch = document.getElementById('password-match');
const updatePasswordFeedback = () => {
    if (!newPassword || !strength || !strengthLabel) return;
    const value = newPassword.value;
    const meetsLength = value.length >= 8;
    const hasNumberOrSymbol = /[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value);
    const level = !value ? 0 : (!meetsLength || !hasNumberOrSymbol ? 1 : (value.length >= 12 ? 3 : 2));
    strength.dataset.level = String(level);
    strengthLabel.textContent = ['Enter a password', 'Needs work', 'Good', 'Strong'][level];
    if (!confirmPassword || !passwordMatch) return;
    const hasConfirmation = confirmPassword.value.length > 0;
    const matches = hasConfirmation && confirmPassword.value === value;
    passwordMatch.textContent = hasConfirmation ? (matches ? 'Passwords match.' : 'Passwords do not match.') : 'Re-enter the new password.';
    passwordMatch.classList.toggle('is-valid', matches);
    passwordMatch.classList.toggle('is-invalid', hasConfirmation && !matches);
};
newPassword?.addEventListener('input', updatePasswordFeedback);
confirmPassword?.addEventListener('input', updatePasswordFeedback);

const motionToggle = document.querySelector('input[name="reduce_motion"]');
const motionPreview = document.getElementById('motion-preview');
const motionPreviewNote = document.getElementById('motion-preview-note');
motionToggle?.addEventListener('change', () => {
    motionPreview?.classList.toggle('is-reduced', motionToggle.checked);
    if (motionPreviewNote) motionPreviewNote.textContent = motionToggle.checked ? 'Reduced motion preview' : 'Standard motion preview';
});
</script>
</body></html>
