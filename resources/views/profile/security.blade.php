<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Security | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.client-portal-styles') @include('partials.settings-styles')</style>
</head>
<body>
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Security</h1><p class="subtitle">Manage your password and account access.</p></div></header>
    @include('partials.settings-tabs', ['activeSettings' => 'security'])
    @if(session('status') === 'password-updated')<div class="alert alert-success" role="status">Password updated successfully.</div>@endif
    <div class="security-grid">
        <section class="panel"><div class="panel-header"><div><h2 class="panel-title">Update password</h2><p class="panel-description">Use a strong password that you do not use elsewhere.</p></div></div><div class="panel-body">
            <form method="POST" action="{{ route('password.update') }}">@csrf @method('put')
                <div class="form-group"><label class="form-label" for="current-password">Current password</label><input class="form-control" type="password" name="current_password" id="current-password" autocomplete="current-password" required>@foreach($errors->updatePassword->get('current_password') as $message)<p class="field-error">{{ $message }}</p>@endforeach</div>
                <div class="form-group"><label class="form-label" for="new-password">New password</label><input class="form-control" type="password" name="password" id="new-password" autocomplete="new-password" required>@foreach($errors->updatePassword->get('password') as $message)<p class="field-error">{{ $message }}</p>@endforeach</div>
                <div class="form-group"><label class="form-label" for="confirm-password">Confirm new password</label><input class="form-control" type="password" name="password_confirmation" id="confirm-password" autocomplete="new-password" required></div>
                <button class="save-button" type="submit">Update password</button>
            </form>
        </div></section>
        <section class="panel danger-zone"><div class="panel-header"><div><h2 class="panel-title">Delete account</h2><p class="panel-description">Permanently remove your account and associated records.</p></div></div><div class="panel-body">
            @foreach($errors->userDeletion->get('account_deletion') as $message)<div class="verification-notice notice-rejected" role="alert"><strong>Account deletion unavailable.</strong> {{ $message }}</div>@endforeach
            @if($hasExistingLoans)
                <div class="verification-notice notice-pending"><strong>Account deletion is locked.</strong> You currently have existing loan records. Contact the administrator if you need assistance with your account.</div>
            @else
                <div class="verification-notice notice-rejected"><strong>This cannot be undone.</strong> Your profile and verification records will be permanently removed.</div>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Permanently delete your account?')">@csrf @method('delete')
                    <div class="form-group"><label class="form-label" for="delete-password">Confirm your password</label><input class="form-control" type="password" name="password" id="delete-password" required>@foreach($errors->userDeletion->get('password') as $message)<p class="field-error">{{ $message }}</p>@endforeach</div>
                    <button class="danger-button" type="submit">Delete account</button>
                </form>
            @endif
        </div></section>
    </div>
</div></main>
</body></html>
