<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accessibility | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/app.js')
<style data-partial-page-style>
@include('partials.client-portal-styles')
@include('partials.settings-styles')
.motion-panel { max-width:780px; }
.motion-preview { display:flex; align-items:center; justify-content:center; gap:8px; min-height:96px; margin-top:14px; overflow:hidden; background:linear-gradient(135deg,#f8fafc,#eef2ff); border:1px solid #e4e7ec; border-radius:12px; }
.motion-preview span { width:12px; height:12px; background:#6366f1; border-radius:50%; animation:client-motion-preview 1.25s ease-in-out infinite alternate; }
.motion-preview span:nth-child(2) { animation-delay:.16s; }
.motion-preview span:nth-child(3) { animation-delay:.32s; }
.motion-preview.is-reduced span { animation:none; opacity:.62; transform:none; }
.motion-preview-note { margin:8px 0 0; color:#667085; font-size:0.5625rem; text-align:center; }
@keyframes client-motion-preview { from { opacity:.35; transform:translateY(7px) scale(.86); } to { opacity:1; transform:translateY(-7px) scale(1); } }
@media(prefers-reduced-motion:reduce){.motion-preview span{animation:none;opacity:.62;transform:none}}
</style>
</head>
<body>
@include('partials.client-sidebar', ['active' => 'settings'])
<main class="main" id="main-content" tabindex="-1" data-partial-page><div class="page-shell settings-shell">
    <header class="topbar"><div><div class="eyebrow">Account settings</div><h1>Settings</h1><p class="subtitle">Manage your profile, verification, security, and accessibility preferences.</p></div><div class="top-actions"><a class="button button-secondary" href="{{ route('dashboard') }}">Back to dashboard</a></div></header>
    @include('partials.settings-tabs', ['activeSettings' => 'motion'])

    <div class="settings-tab-content" id="settings-tab-content" role="tabpanel" tabindex="-1" data-partial-content>

    @if(session('status') === 'motion-updated')<div class="alert alert-success" role="status">Your accessibility preferences were saved.</div>@endif

    <section class="panel motion-panel">
        <div class="panel-header"><div><h2 class="panel-title">Reading and motion</h2><p class="panel-description">Adjust text size and movement to make your workspace comfortable to use.</p></div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('profile.motion.update') }}">@csrf @method('PATCH')
                @include('partials.text-size-setting')
                @error('text_size')<p class="field-error">{{ $message }}</p>@enderror
                <label class="motion-setting"><span class="motion-setting-copy"><span class="motion-setting-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span><span><strong>Reduce motion</strong><small>Minimize page transitions, entrance effects, and animated interface feedback. Enable this if less movement makes the workspace more comfortable to use.</small></span></span><span class="motion-toggle"><input type="checkbox" name="reduce_motion" value="1" @checked(old('reduce_motion', $reduceMotion))><i class="motion-toggle-track"></i></span></label>
                <div class="motion-preview {{ old('reduce_motion', $reduceMotion) ? 'is-reduced' : '' }}" id="client-motion-preview" aria-hidden="true"><span></span><span></span><span></span></div>
                <p class="motion-preview-note" id="client-motion-preview-note">{{ old('reduce_motion', $reduceMotion) ? 'Reduced motion preview' : 'Standard motion preview' }}</p>
                @error('reduce_motion')<p class="field-error">{{ $message }}</p>@enderror
                <div class="form-actions"><button class="save-button" type="submit">Save accessibility</button></div>
            </form>
        </div>
    </section>
    </div>
</div></main>
<script data-partial-page-script>
const clientMotionToggle=document.querySelector('#settings-tab-content input[name="reduce_motion"]');
const clientMotionPreview=document.getElementById('client-motion-preview');
const clientMotionPreviewNote=document.getElementById('client-motion-preview-note');
clientMotionToggle?.addEventListener('change',()=>{
    clientMotionPreview?.classList.toggle('is-reduced',clientMotionToggle.checked);
    if(clientMotionPreviewNote)clientMotionPreviewNote.textContent=clientMotionToggle.checked?'Reduced motion preview':'Standard motion preview';
});
</script>
</body></html>
