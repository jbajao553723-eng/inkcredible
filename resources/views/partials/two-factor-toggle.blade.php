@php $twoFactorEnabled = $twoFactorUser->hasTwoFactorAuthenticationEnabled(); @endphp
<style>
.two-factor-control { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:16px; background:#fff; border:1px solid #e4e7ec; border-radius:12px; }
.two-factor-control strong { display:block; color:#344054; font-size:12px; }.two-factor-control small { display:block; margin-top:5px; max-width:240px; color:#667085; font-size:11px; line-height:1.5; }
.two-factor-switch { position:relative; display:block; width:46px; height:26px; flex:0 0 46px; }.two-factor-switch input { position:absolute; inset:0; width:100%; height:100%; margin:0; opacity:0; cursor:pointer; z-index:1; }.two-factor-switch-track { position:absolute; inset:0; background:#d0d5dd; border-radius:999px; transition:background .2s; pointer-events:none; }.two-factor-switch-track::after { position:absolute; top:3px; left:3px; width:20px; height:20px; background:#fff; border-radius:50%; box-shadow:0 1px 3px #10182833; content:''; transition:transform .2s; }.two-factor-switch input:checked + .two-factor-switch-track { background:#4f46e5; }.two-factor-switch input:checked + .two-factor-switch-track::after { transform:translateX(20px); }.two-factor-switch input:focus-visible + .two-factor-switch-track { outline:3px solid #c7d2fe; outline-offset:3px; }
@media(prefers-reduced-motion:reduce){.two-factor-switch-track,.two-factor-switch-track::after{transition:none}}
</style>
<form class="two-factor-control" method="POST" action="{{ route($twoFactorEnabled ? 'two-factor.disable' : 'two-factor.enable') }}">
    @csrf
    @if($twoFactorEnabled) @method('DELETE') @endif
    <span><strong>Two-factor authentication {{ $twoFactorEnabled ? 'on' : 'off' }}</strong><small>{{ $twoFactorEnabled ? 'Your next sign-in requires an email code.' : 'Turn on, then confirm the code sent to your email.' }}</small></span>
    <label class="two-factor-switch"><input type="checkbox" role="switch" aria-label="Two-factor authentication" @checked($twoFactorEnabled) onchange="this.form.requestSubmit()"><span class="two-factor-switch-track" aria-hidden="true"></span></label>
    <noscript><button type="submit">{{ $twoFactorEnabled ? 'Turn off' : 'Turn on' }}</button></noscript>
</form>
