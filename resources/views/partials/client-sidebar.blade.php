<a class="skip-link" href="#main-content">Skip to main content</a>
<aside class="sidebar">
    <div class="brand"><span class="brand-mark">I</span><span>Inkcredible</span></div>
    <div class="nav-label">Menu</div>
    <nav aria-label="Main navigation">
        <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" @if($active === 'dashboard') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 11.5 12 4l9 7.5M5.5 10v10h13V10M9.5 20v-6h5v6"/></svg>
            Dashboard
        </a>
        <a class="nav-link {{ $active === 'loans' ? 'active' : '' }}" href="{{ route('loan.create') }}" @if($active === 'loans') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6M4 4h16v16H4z"/></svg>
            Request Loan
        </a>
        <a class="nav-link {{ $active === 'payments' ? 'active' : '' }}" href="{{ route('payments.index') }}" @if($active === 'payments') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 7.5h17v10h-17zM3.5 10.5h17M7 15h3"/></svg>
            Payments
        </a>
        <a class="nav-link {{ $active === 'settings' ? 'active' : '' }}" href="{{ route('profile.edit') }}" @if($active === 'settings') aria-current="page" @endif>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="3" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20c.5-4.5 2.8-7 7-7s6.5 2.5 7 7"/></svg>
            Settings
        </a>
    </nav>
</aside>
