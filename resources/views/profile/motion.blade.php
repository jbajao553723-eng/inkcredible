<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Motion Accessibility | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style>
@include('partials.client-portal-styles')
@include('partials.settings-styles')
.motion-panel { max-width:780px; }
</style>
</head>
<body>
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Motion</h1><p class="subtitle">Choose how animated effects behave throughout your client workspace.</p></div></header>
    @include('partials.settings-tabs', ['activeSettings' => 'motion'])

    @if(session('status') === 'motion-updated')<div class="alert alert-success" role="status">Your motion preference was updated successfully.</div>@endif

    <section class="panel motion-panel">
        <div class="panel-header"><div><h2 class="panel-title">Motion accessibility</h2><p class="panel-description">Choose whether animated effects are minimized throughout your client workspace.</p></div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.motion.update') }}">@csrf @method('PATCH')
                <label class="motion-setting"><span class="motion-setting-copy"><span class="motion-setting-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span><strong>Reduce motion</strong><small>Minimize page transitions, entrance effects, and animated interface feedback. Enable this if less movement makes the workspace more comfortable to use.</small></span></span><span class="motion-toggle"><input type="checkbox" name="reduce_motion" value="1" @checked(old('reduce_motion', $reduceMotion))><i class="motion-toggle-track"></i></span></label>
                @error('reduce_motion')<p class="field-error">{{ $message }}</p>@enderror
                <div class="form-actions"><button class="save-button" type="submit">Save motion setting</button></div>
            </form>
        </div>
    </section>
</div></main>
</body></html>
