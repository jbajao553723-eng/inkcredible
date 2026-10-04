@php
    $sidebarUser = auth()->user();
    $isSuperAdmin = $sidebarUser?->isSuperAdmin();
    $sidebarName = $sidebarUser?->name ?? 'Administrator';
    $sidebarInitials = collect(preg_split('/\s+/', trim($sidebarName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp

<a class="skip-link" href="#main-content">Skip to main content</a>
<aside class="sidebar">
    <div class="sidebar-inner">
        <a class="sidebar-brand" href="{{ $isSuperAdmin ? route('admin.security.dashboard') : route('admin.dashboard') }}" aria-label="Inkcredible {{ $isSuperAdmin ? 'security' : 'admin' }} dashboard">
            <x-brand-mark />
            <span class="brand-copy"><strong>Inkcredible</strong><small>{{ $isSuperAdmin ? 'Security workspace' : 'Admin workspace' }}</small></span>
        </a>

        <nav class="sidebar-nav" aria-label="Admin navigation">
            <div class="nav-section">
                <div class="nav-label">Overview</div>
                <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ $isSuperAdmin ? route('admin.security.dashboard') : route('admin.dashboard') }}" @if($active === 'dashboard') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>

            @unless($isSuperAdmin)
                <div class="nav-section">
                    <div class="nav-label">Lending</div>
                    <a class="nav-link {{ $active === 'loans' ? 'active' : '' }}" href="{{ route('admin.loans') }}" @if($active === 'loans') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg>
                        <span>Loan requests</span>
                    </a>
                    <a class="nav-link {{ $active === 'clients' ? 'active' : '' }}" href="{{ route('admin.clients') }}" @if($active === 'clients') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3.5 19c.5-4 2.3-6 5.5-6s5 2 5.5 6M15 5.5a3 3 0 0 1 0 5.5M16 13c2.6.5 4 2.5 4.5 6"/></svg>
                        <span>Clients</span>
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-label">Finance</div>
                    <a class="nav-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" @if($active === 'payments') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg>
                        <span>Payments</span>
                    </a>
                    <a class="nav-link {{ $active === 'reports' ? 'active' : '' }}" href="{{ route('admin.reports.index') }}" @if($active === 'reports') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3.5h9l3 3V20.5H6zM15 3.5v4h4M9 12h6M9 16h6"/></svg>
                        <span>Reports</span>
                    </a>
                </div>
            @endunless

            @if($isSuperAdmin)
                <div class="nav-section">
                    <div class="nav-label">Security</div>
                    <a class="nav-link {{ $active === 'access-control' ? 'active' : '' }}" href="{{ route('admin.access.index') }}" @if($active === 'access-control') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM8.5 12h7M12 8.5v7"/></svg>
                        <span>Admin access</span>
                    </a>
                    <a class="nav-link {{ $active === 'audit-logs' ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}" @if($active === 'audit-logs') aria-current="page" @endif>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg>
                        <span>Audit logs</span>
                    </a>
                </div>
            @endif

            <div class="nav-section">
                <div class="nav-label">Account</div>
                <a class="nav-link {{ $active === 'settings' ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}" @if($active === 'settings') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg>
                    <span>Settings</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-account">
            <span class="account-avatar" aria-hidden="true">{{ $sidebarInitials ?: 'A' }}</span>
            <span class="account-copy"><strong>{{ $sidebarName }}</strong><small>{{ $isSuperAdmin ? 'Super Administrator' : 'Administrator' }}</small></span>
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
