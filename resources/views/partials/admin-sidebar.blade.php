<a class="skip-link" href="#main-content">Skip to main content</a>
<aside class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-brand">
            <span class="brand-mark" aria-hidden="true">I</span>
            <span class="brand-copy"><strong>Inkcredible</strong><small>Admin workspace</small></span>
        </div>

        <nav class="sidebar-nav" aria-label="Admin navigation">
            <div class="nav-section">
                <div class="nav-label">Overview</div>
                <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if($active === 'dashboard') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Lending</div>
                <a class="nav-link {{ $active === 'loans' ? 'active' : '' }}" href="{{ route('admin.loans') }}" @if($active === 'loans') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg>
                    <span>Loan requests</span>
                </a>
                <a class="nav-link {{ $active === 'clients' ? 'active' : '' }}" href="{{ route('admin.clients') }}" @if($active === 'clients') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3.5 19c.5-4 2.3-6 5.5-6s5 2 5.5 6M15 5.5a3 3 0 0 1 0 5.5M16 13c2.6.5 4 2.5 4.5 6"/></svg>
                    <span>Clients</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Finance</div>
                <a class="nav-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" @if($active === 'payments') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg>
                    <span>Payments</span>
                </a>
                <a class="nav-link {{ $active === 'reports' ? 'active' : '' }}" href="{{ route('admin.reports.index') }}" @if($active === 'reports') aria-current="page" @endif>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3.5h9l3 3V20.5H6zM15 3.5v4h4M9 12h6M9 16h6"/></svg>
                    <span>Reports</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-account">
            <span class="account-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</span>
            <span class="account-copy"><strong>{{ auth()->user()?->name ?? 'Administrator' }}</strong><small>Administrator</small></span>
            <span class="account-status" title="Signed in"></span>
        </div>
    </div>
</aside>
