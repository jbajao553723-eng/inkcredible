<nav class="settings-tabs" aria-label="Settings sections" role="tablist" data-partial-navigation>
    <a class="settings-tab {{ $activeSettings === 'profile' ? 'active' : '' }}" href="{{ route('profile.edit') }}" role="tab" aria-controls="settings-tab-content" aria-selected="{{ $activeSettings === 'profile' ? 'true' : 'false' }}" tabindex="{{ $activeSettings === 'profile' ? '0' : '-1' }}" data-settings-tab="profile" data-no-transition>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg></span>
        <span class="tab-title">Profile</span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'verification' ? 'active' : '' }}" href="{{ route('profile.verification.edit') }}" role="tab" aria-controls="settings-tab-content" aria-selected="{{ $activeSettings === 'verification' ? 'true' : 'false' }}" tabindex="{{ $activeSettings === 'verification' ? '0' : '-1' }}" data-settings-tab="verification" data-no-transition>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.7 7.5-7 9.5C7.7 18.5 5 15.5 5 11V6zM9 12l2 2 4-5"/></svg></span>
        <span class="tab-title">Verification</span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'security' ? 'active' : '' }}" href="{{ route('profile.security.edit') }}" role="tab" aria-controls="settings-tab-content" aria-selected="{{ $activeSettings === 'security' ? 'true' : 'false' }}" tabindex="{{ $activeSettings === 'security' ? '0' : '-1' }}" data-settings-tab="security" data-no-transition>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
        <span class="tab-title">Security</span>
    </a>
    <a class="settings-tab {{ $activeSettings === 'motion' ? 'active' : '' }}" href="{{ route('profile.motion.edit') }}" role="tab" aria-controls="settings-tab-content" aria-selected="{{ $activeSettings === 'motion' ? 'true' : 'false' }}" tabindex="{{ $activeSettings === 'motion' ? '0' : '-1' }}" data-settings-tab="motion" data-no-transition>
        <span class="tab-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/><path stroke-linecap="round" stroke-width="1.8" d="M5 7h3M5 17h3"/></svg></span>
        <span class="tab-title">Accessibility</span>
    </a>
</nav>
