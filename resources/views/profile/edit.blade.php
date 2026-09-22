<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personal Profile | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>@include('partials.client-portal-styles') @include('partials.settings-styles')</style>
</head>
<body>
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Personal profile</h1><p class="subtitle">Keep your identity and contact information accurate.</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></header>
    @include('partials.settings-tabs', ['activeSettings' => 'profile'])
    @if(session('status') === 'profile-updated')<div class="alert alert-success" role="status">Profile information updated.</div>@endif
    <section class="panel">
        <div class="panel-header"><div><h2 class="panel-title">Contact information</h2><p class="panel-description">These details are required and are used for account and lending communication.</p></div></div>
        <div class="panel-body"><form method="POST" action="{{ route('profile.update') }}">@csrf @method('patch')
            <div class="form-grid">
                <div class="form-group"><label class="form-label" for="first-name">First name</label><input class="form-control" id="first-name" name="first_name" value="{{ old('first_name', $user->first_name) }}" autocomplete="given-name" maxlength="100" required>@error('first_name')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-group"><label class="form-label" for="last-name">Last name</label><input class="form-control" id="last-name" name="last_name" value="{{ old('last_name', $user->last_name) }}" autocomplete="family-name" maxlength="100" required>@error('last_name')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-group"><label class="form-label" for="email">Email address</label><input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-group"><label class="form-label" for="contact-number">Contact number</label><input class="form-control" type="tel" id="contact-number" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" autocomplete="tel" maxlength="30" required>@error('contact_number')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-group"><label class="form-label" for="age">Age</label><input class="form-control" type="number" id="age" name="age" min="18" max="120" value="{{ old('age', $user->age) }}" required>@error('age')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="form-group full"><label class="form-label" for="address">Complete address</label><textarea class="form-control" id="address" name="address" maxlength="500" autocomplete="street-address" required>{{ old('address', $user->address) }}</textarea>@error('address')<p class="field-error">{{ $message }}</p>@enderror</div>
            </div>
            <button class="save-button" type="submit">Save personal profile</button>
        </form></div>
    </section>
</div></main>
</body></html>
