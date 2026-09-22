<aside class="sidebar">
    <div class="brand"><span class="brand-mark">I</span><span>Inkcredible</span></div>
    <div class="admin-chip">Administration</div>
    <div class="nav-label">Workspace</div>
    <nav aria-label="Admin navigation">
        <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if($active === 'dashboard') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
            Dashboard
        </a>
        <a class="nav-link {{ $active === 'loans' ? 'active' : '' }}" href="{{ route('admin.loans') }}" @if($active === 'loans') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4"/></svg>
            Loan requests
        </a>
        <a class="nav-link {{ $active === 'clients' ? 'active' : '' }}" href="{{ route('admin.clients') }}" @if($active === 'clients') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M3.5 19c.5-4 2.3-6 5.5-6s5 2 5.5 6M15 5.5a3 3 0 0 1 0 5.5M16 13c2.6.5 4 2.5 4.5 6"/></svg>
            Clients
        </a>
        <a class="nav-link {{ $active === 'verifications' ? 'active' : '' }}" href="{{ route('admin.verifications.index') }}" @if($active === 'verifications') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.7 7.5-7 9.5C7.7 18.5 5 15.5 5 11V6zM9 12l2 2 4-5"/></svg>
            Verifications
        </a>
        <a class="nav-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('admin.payments.index') }}" @if($active === 'payments') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg>
            Payments
        </a>
    </nav>
</aside>
