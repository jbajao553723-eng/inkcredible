<nav class="settings-tabs" aria-label="Settings sections">
    <a class="settings-tab {{ $activeSettings === 'profile' ? 'active' : '' }}" href="{{ route('profile.edit') }}" @if($activeSettings === 'profile') aria-current="page" @endif>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg></span>
        <span><span class="tab-title">Personal profile</span><span class="tab-note">Contact and address</span></span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'verification' ? 'active' : '' }}" href="{{ route('profile.verification.edit') }}" @if($activeSettings === 'verification') aria-current="page" @endif>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.7 7.5-7 9.5C7.7 18.5 5 15.5 5 11V6zM9 12l2 2 4-5"/></svg></span>
        <span><span class="tab-title">Verification</span><span class="tab-note">Income, payslip, ID, and signature</span></span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'security' ? 'active' : '' }}" href="{{ route('profile.security.edit') }}" @if($activeSettings === 'security') aria-current="page" @endif>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
        <span><span class="tab-title">Security</span><span class="tab-note">Password and account</span></span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'motion' ? 'active' : '' }}" href="{{ route('profile.motion.edit') }}" @if($activeSettings === 'motion') aria-current="page" @endif>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span>
        <span><span class="tab-title">Motion</span><span class="tab-note">Animation accessibility</span></span>
    </a>
</nav>
