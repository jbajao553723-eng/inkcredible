<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Security Dashboard | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.security-stats { grid-template-columns:repeat(4,minmax(0,1fr)); }
.security-stat { position:relative; min-height:138px; overflow:hidden; }
.security-stat::after { position:absolute; top:-42px; right:-34px; width:106px; height:106px; background:var(--stat-tint,#eef2ff); border-radius:50%; content:''; opacity:.75; }
.security-stat .stat-head,.security-stat .stat-value,.security-stat .stat-note { position:relative; z-index:1; }
.security-stat .stat-icon { color:var(--stat-color,#4f46e5); background:var(--stat-tint,#eef2ff); }
.security-stat.active { --stat-color:#067647; --stat-tint:#ecfdf3; }
.security-stat.disabled { --stat-color:#b42318; --stat-tint:#fef3f2; }
.security-stat.events { --stat-color:#0e7490; --stat-tint:#ecfeff; }
.security-grid { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(300px,.75fr); gap:18px; margin-bottom:18px; align-items:start; }
.quick-actions { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; padding:20px; }
.quick-action { display:grid; grid-template-columns:42px minmax(0,1fr) 18px; gap:12px; align-items:center; min-height:88px; padding:15px; color:#344054; background:#fff; border:1px solid #e4e7ec; border-radius:13px; text-decoration:none; transition:border-color .16s ease,box-shadow .16s ease,transform .16s ease; }
.quick-action:hover { color:#344054; border-color:#c7d2fe; box-shadow:0 8px 20px rgba(79,70,229,.08); transform:translateY(-1px); }
.quick-icon { display:grid; place-items:center; width:42px; height:42px; color:#4f46e5; background:#eef2ff; border-radius:11px; }
.quick-icon.audit { color:#0e7490; background:#ecfeff; }
.quick-icon svg,.quick-arrow { width:19px; height:19px; }
.quick-copy strong,.quick-copy span { display:block; }.quick-copy strong { font-size:12px; }.quick-copy span { margin-top:4px; color:#667085; font-size:9px; line-height:1.45; }
.quick-arrow { color:#98a2b3; }
.posture-panel { overflow:hidden; background:linear-gradient(145deg,#fafaff,#fff); border-color:#d9d6fe; }
.posture-body { padding:20px; }
.posture-score { display:flex; align-items:center; gap:14px; padding:15px; background:#fff; border:1px solid #e4e7ec; border-radius:13px; }
.posture-shield { display:grid; place-items:center; width:46px; height:46px; flex:0 0 46px; color:#067647; background:#ecfdf3; border-radius:13px; }
.posture-shield svg { width:23px; height:23px; }
.posture-copy { min-width:0; }
.posture-copy strong { display:block; color:#101828; font-size:13px; }
.posture-copy > span { display:block; margin-top:4px; color:#667085; font-size:9px; line-height:1.4; overflow-wrap:anywhere; }
.posture-list { display:grid; gap:10px; margin-top:14px; }
.posture-item { display:grid; grid-template-columns:20px 1fr; gap:9px; align-items:start; color:#475467; font-size:10px; line-height:1.45; }
.posture-check { display:grid; place-items:center; width:18px; height:18px; color:#067647; background:#d1fadf; border-radius:50%; font-size:10px; font-weight:800; }
.monitoring-layout { display:grid; grid-template-columns:minmax(0,1.05fr) minmax(340px,.95fr); gap:18px; margin-bottom:18px; align-items:start; }
.signal-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:11px; padding:18px; }
.signal-card { padding:15px; background:#f8fafc; border:1px solid #eaecf0; border-radius:12px; }.signal-card.alert { background:#fffbfa; border-color:#fecdca; }.signal-card.warning { background:#fffcf5; border-color:#fedf89; }
.signal-label { color:#667085; font-size:9px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }.signal-value { margin-top:7px; color:#101828; font-size:23px; font-weight:700; }.signal-note { margin-top:4px; color:#667085; font-size:9px; line-height:1.45; }
.control-list { display:grid; }.control-row { display:grid; grid-template-columns:20px minmax(0,1fr) auto; gap:10px; align-items:start; padding:13px 18px; border-bottom:1px solid #f2f4f7; }.control-row:last-child { border-bottom:0; }
.control-icon { display:grid; place-items:center; width:19px; height:19px; color:#067647; background:#d1fadf; border-radius:50%; font-size:10px; font-weight:800; }.control-icon.attention { color:#b54708; background:#fef0c7; }
.control-copy strong,.control-copy span { display:block; }.control-copy strong { color:#344054; font-size:10px; }.control-copy span { margin-top:3px; color:#667085; font-size:9px; line-height:1.45; }.control-state { padding:4px 7px; color:#067647; background:#ecfdf3; border-radius:999px; font-size:8px; font-weight:700; text-transform:uppercase; }.control-state.attention { color:#b54708; background:#fffaeb; }
.activity-layout { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(300px,.75fr); gap:18px; align-items:start; }
.activity-list,.admin-list { display:grid; }
.activity-row { display:grid; grid-template-columns:38px minmax(0,1fr) auto; gap:11px; align-items:center; min-height:67px; padding:11px 20px; border-bottom:1px solid #f2f4f7; }
.activity-row:last-child,.admin-row:last-child { border-bottom:0; }
.activity-symbol { display:grid; place-items:center; width:36px; height:36px; color:#475467; background:#f2f4f7; border-radius:10px; }
.activity-symbol svg { width:17px; height:17px; }.activity-symbol.risk { color:#b42318; background:#fef3f2; }.activity-symbol.access { color:#4338ca; background:#eef2ff; }.activity-symbol.login { color:#067647; background:#ecfdf3; }
.activity-copy strong,.activity-copy span { display:block; }.activity-copy strong { color:#344054; font-size:11px; }.activity-copy span { margin-top:4px; color:#667085; font-size:9px; line-height:1.4; }
.activity-time { color:#98a2b3; font-size:9px; text-align:right; white-space:nowrap; }
.admin-row { display:grid; grid-template-columns:38px minmax(0,1fr) auto; gap:10px; align-items:center; min-height:64px; padding:11px 20px; border-bottom:1px solid #f2f4f7; }
.admin-avatar { display:grid; place-items:center; width:36px; height:36px; color:#4338ca; background:#eef2ff; border-radius:10px; font-size:11px; font-weight:800; }
.admin-copy strong,.admin-copy span { display:block; }.admin-copy strong { color:#344054; font-size:11px; }.admin-copy span { margin-top:3px; color:#667085; font-size:9px; overflow-wrap:anywhere; }
.dashboard-empty { padding:30px 20px; color:#667085; font-size:10px; text-align:center; }
@media(max-width:1100px){.security-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.security-grid,.monitoring-layout,.activity-layout{grid-template-columns:1fr}}
@media(max-width:620px){.security-stats,.quick-actions,.signal-grid{grid-template-columns:1fr}.quick-actions{padding:14px}.activity-row{grid-template-columns:36px minmax(0,1fr)}.activity-time{grid-column:2;text-align:left}.top-actions{width:100%}.top-actions .button{flex:1}.control-row{grid-template-columns:20px minmax(0,1fr)}.control-state{grid-column:2;justify-self:start}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'dashboard'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Security administration</div><h1>Security dashboard</h1><p class="subtitle">Monitor administrator access, sign-in activity, and account protection from one focused workspace.</p></div>
        <div class="top-actions"><a class="button button-primary" href="{{ route('admin.access.index') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14"/></svg>Manage admins</a></div>
    </header>

    <x-flash-messages />

    <section class="stats-grid security-stats" aria-label="Security overview">
        <article class="stat-card security-stat"><div class="stat-head"><span class="stat-label">Administrator accounts</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8m8-1 2 2 4-4"/></svg></span></div><div class="stat-value">{{ $stats['total_admins'] }}</div><div class="stat-note">Standard admin accounts under management</div></article>
        <article class="stat-card security-stat active"><div class="stat-head"><span class="stat-label">Active access</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m7 12 3 3 7-7M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6z"/></svg></span></div><div class="stat-value">{{ $stats['active_admins'] }}</div><div class="stat-note">Administrators currently allowed to sign in</div></article>
        <article class="stat-card security-stat disabled"><div class="stat-head"><span class="stat-label">Disabled access</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 12h8"/></svg></span></div><div class="stat-value">{{ $stats['disabled_admins'] }}</div><div class="stat-note">Accounts blocked from authenticated modules</div></article>
        <article class="stat-card security-stat events"><div class="stat-head"><span class="stat-label">Security events today</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16v14H4zM8 3v4m8-4v4M4 9h16"/></svg></span></div><div class="stat-value">{{ $stats['events_today'] }}</div><div class="stat-note">{{ $stats['failed_logins_today'] }} failed {{ Str::plural('sign-in', $stats['failed_logins_today']) }} today</div></article>
    </section>

    <section class="security-grid">
        <article class="panel"><div class="panel-header"><div><h2 class="panel-title">Security controls</h2><p class="panel-description">Go directly to privileged account and audit tools.</p></div></div><div class="quick-actions">
            <a class="quick-action" href="{{ route('admin.access.index') }}"><span class="quick-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM8.5 12h7M12 8.5v7"/></svg></span><span class="quick-copy"><strong>Administrator access</strong><span>Add, disable, or restore standard administrator accounts.</span></span><svg class="quick-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
            <a class="quick-action" href="{{ route('admin.audit-logs.index') }}"><span class="quick-icon audit"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg></span><span class="quick-copy"><strong>Audit activity</strong><span>Review sign-ins, access changes, users, and network details.</span></span><svg class="quick-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7"/></svg></a>
        </div></article>

        <aside class="panel posture-panel"><div class="panel-header"><div><h2 class="panel-title">Access posture</h2><p class="panel-description">Current privileged-access safeguards.</p></div></div><div class="posture-body">
            <div class="posture-score"><span class="posture-shield"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m8 12 3 3 5-6M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6z"/></svg></span><span class="posture-copy"><strong>Security controls enabled</strong><span>Superadmin access is isolated from lending and finance operations.</span></span></div>
            <div class="posture-list"><div class="posture-item"><span class="posture-check">&#10003;</span><span>Only superadmins can create or change administrator access.</span></div><div class="posture-item"><span class="posture-check">&#10003;</span><span>Disabled accounts are signed out and blocked at authentication.</span></div><div class="posture-item"><span class="posture-check">&#10003;</span><span>Privileged changes are preserved in the audit trail.</span></div></div>
        </div></aside>
    </section>

    <section class="monitoring-layout" aria-label="Security monitoring and control readiness">
        <article class="panel"><div class="panel-header"><div><h2 class="panel-title">Threat signals</h2><p class="panel-description">Authentication and privileged-access indicators requiring review.</p></div><a class="button button-secondary button-small" href="{{ route('admin.audit-logs.index', ['severity' => 'high']) }}">Investigate</a></div><div class="signal-grid">
            <div class="signal-card {{ $stats['failed_logins_24h'] ? 'alert' : '' }}"><div class="signal-label">Failed sign-ins</div><div class="signal-value">{{ number_format($stats['failed_logins_24h']) }}</div><div class="signal-note">Across {{ number_format($stats['failure_sources_24h']) }} source {{ Str::plural('IP', $stats['failure_sources_24h']) }} in 24 hours.</div></div>
            <div class="signal-card {{ $stats['repeat_failure_sources'] ? 'alert' : '' }}"><div class="signal-label">Repeated sources</div><div class="signal-value">{{ number_format($stats['repeat_failure_sources']) }}</div><div class="signal-note">Sources with at least three failed sign-ins in 24 hours.</div></div>
            <div class="signal-card {{ $stats['authorization_failures_24h'] ? 'alert' : '' }}"><div class="signal-label">Denied access</div><div class="signal-value">{{ number_format($stats['authorization_failures_24h']) }}</div><div class="signal-note">Protected-route authorization failures in 24 hours.</div></div>
            <div class="signal-card {{ $stats['privileged_changes_7d'] ? 'warning' : '' }}"><div class="signal-label">Privileged changes</div><div class="signal-value">{{ number_format($stats['privileged_changes_7d']) }}</div><div class="signal-note">Administrator access changes during the last seven days.</div></div>
            <div class="signal-card"><div class="signal-label">Active admin sessions</div><div class="signal-value">{{ $stats['active_admin_sessions'] === null ? 'N/A' : number_format($stats['active_admin_sessions']) }}</div><div class="signal-note">Server-side sessions currently tied to privileged accounts.</div></div>
        </div></article>

        <aside class="panel"><div class="panel-header"><div><h2 class="panel-title">Control readiness</h2><p class="panel-description">Configuration checks for privileged access.</p></div></div><div class="control-list">@foreach($securityControls as $control)<div class="control-row"><span class="control-icon {{ $control['enabled'] ? '' : 'attention' }}">{{ $control['enabled'] ? '✓' : '!' }}</span><span class="control-copy"><strong>{{ $control['name'] }}</strong><span>{{ $control['note'] }}</span></span><span class="control-state {{ $control['enabled'] ? '' : 'attention' }}">{{ $control['enabled'] ? 'Enabled' : 'Action needed' }}</span></div>@endforeach</div></aside>
    </section>

    <section class="activity-layout">
        <article class="panel"><div class="panel-header"><div><h2 class="panel-title">Recent security activity</h2><p class="panel-description">Latest authentication and administrator-access events.</p></div><a class="button button-secondary button-small" href="{{ route('admin.audit-logs.index') }}">View all</a></div>
            @if($recentActivity->isEmpty())<div class="dashboard-empty">No security activity has been recorded yet.</div>@else<div class="activity-list">@foreach($recentActivity as $event)
                @php
                    $eventClass = $event->action === 'login_failed' ? 'risk' : (str_contains($event->action, 'admin_access') ? 'access' : ($event->action === 'login' ? 'login' : ''));
                    $eventTime = $event->occurred_at?->copy()->timezone('Asia/Manila');
                @endphp
                <div class="activity-row"><span class="activity-symbol {{ $eventClass }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 12h6"/></svg></span><span class="activity-copy"><strong>{{ $event->description }}</strong><span>{{ $event->user?->name ?? 'Guest / unknown user' }} &middot; {{ $event->ip_address ?: 'Unknown IP' }}</span></span><span class="activity-time">{{ $eventTime?->format('M d') }}<br>{{ $eventTime?->format('h:i A') }}</span></div>
            @endforeach</div>@endif
        </article>

        <aside class="panel"><div class="panel-header"><div><h2 class="panel-title">Newest administrators</h2><p class="panel-description">Recently created managed accounts.</p></div><a class="button button-secondary button-small" href="{{ route('admin.access.index') }}">Manage</a></div>
            @if($recentAdmins->isEmpty())<div class="dashboard-empty">No standard administrator accounts exist yet.</div>@else<div class="admin-list">@foreach($recentAdmins as $admin)
                <div class="admin-row"><span class="admin-avatar">{{ strtoupper(substr($admin->first_name ?: $admin->name, 0, 1)) }}</span><span class="admin-copy"><strong>{{ $admin->name }}</strong><span>{{ $admin->email }}</span></span><span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-danger' }}">{{ $admin->is_active ? 'Active' : 'Disabled' }}</span></div>
            @endforeach</div>@endif
        </aside>
    </section>
</div></main>
</body>
</html>
