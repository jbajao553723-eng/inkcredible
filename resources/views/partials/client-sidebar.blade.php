@php
    $sidebarUser = auth()->user();
    $sidebarName = $sidebarUser?->name ?? 'Client';
    $sidebarInitials = collect(preg_split('/\s+/', trim($sidebarName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp

<a class="skip-link" href="#main-content">Skip to main content</a>
<aside class="sidebar">
    <div class="sidebar-inner">
        <a class="sidebar-brand" href="{{ route('dashboard') }}" aria-label="Inkcredible client dashboard">
            <x-brand-mark />
            <span class="brand-copy"><strong>Inkcredible</strong><small>Client workspace</small></span>
        </a>

        <nav class="sidebar-nav" aria-label="Client navigation">
            <div class="nav-section">
                <div class="nav-label">Overview</div>
                <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" @if($active === 'dashboard') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 11.5 12 4l9 7.5M5.5 10v10h13V10M9.5 20v-6h5v6"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Lending</div>
                <a class="nav-link {{ $active === 'loans' ? 'active' : '' }}" href="{{ route('loan.create') }}" @if($active === 'loans') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6M4 4h16v16H4z"/></svg>
                    <span>Request loan</span>
                </a>
                <a class="nav-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('payments.index') }}" @if($active === 'payments') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg>
                    <span>Payments</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Account</div>
                <a class="nav-link {{ $active === 'settings' ? 'active' : '' }}" href="{{ route('profile.edit') }}" @if($active === 'settings') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg>
                    <span>Settings</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-account">
            <span class="account-avatar" aria-hidden="true">{{ $sidebarInitials ?: 'C' }}</span>
            <span class="account-copy"><strong>{{ $sidebarName }}</strong><small>{{ $sidebarUser?->email ?? 'Client account' }}</small></span>
            <form class="sidebar-logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-logout" type="submit" title="Log out" aria-label="Log out of Inkcredible">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 17l5-5-5-5m5 5H3m12-9h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
                    <span>Log out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
