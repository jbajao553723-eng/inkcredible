<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Audit Logs | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.audit-summary { grid-template-columns:repeat(3,minmax(0,1fr)); }
.audit-summary .stat-card:last-child .stat-icon { color:#b42318; background:#fef3f2; }
.audit-filter-panel { margin-bottom:20px; }
.audit-filters { display:grid; grid-template-columns:minmax(200px,1.35fr) minmax(170px,1fr) minmax(145px,.75fr) minmax(145px,.75fr) auto; gap:12px; align-items:end; }
.filter-field { min-width:0; }.filter-field label { display:block; margin-bottom:6px; color:#475467; font-size:10px; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
.filter-field .filter-select { width:100%; min-width:0; }
.filter-actions { display:flex; align-items:center; gap:8px; white-space:nowrap; }
.audit-table { table-layout:fixed; min-width:940px; }
.audit-table th:nth-child(1) { width:13%; }.audit-table th:nth-child(2) { width:20%; }.audit-table th:nth-child(3) { width:12%; }.audit-table th:nth-child(4) { width:29%; }.audit-table th:nth-child(5) { width:26%; }
.audit-table td { vertical-align:top; overflow-wrap:anywhere; word-break:normal; }
.audit-primary { color:#1d2939; line-height:1.45; }.audit-meta { margin-top:4px; color:#667085; font-size:10px; line-height:1.45; overflow-wrap:anywhere; }
.audit-action { display:inline-flex; max-width:100%; padding:5px 8px; color:#4338ca; background:#eef2ff; border-radius:7px; font-size:9px; font-weight:700; line-height:1.35; text-align:center; white-space:normal; }
.audit-network { min-width:0; }.audit-agent { display:-webkit-box; overflow:hidden; -webkit-box-orient:vertical; -webkit-line-clamp:2; }
.pagination-wrap { padding:16px 20px; border-top:1px solid #f2f4f7; }
.pagination-wrap nav { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.pagination-wrap svg { width:16px; height:16px; }
@media(max-width:1180px){.audit-filters{grid-template-columns:repeat(2,minmax(0,1fr))}.filter-actions{grid-column:1/-1}.filter-actions .button{flex:1}}
@media(max-width:900px){
    .audit-summary{grid-template-columns:1fr}.audit-table{min-width:0;table-layout:auto}.audit-table thead{display:none}.audit-table,.audit-table tbody,.audit-table tr,.audit-table td{display:block;width:100%}.audit-table tbody{display:grid;gap:12px;padding:14px}.audit-table tr{padding:6px 16px;background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:0 2px 7px rgba(16,24,40,.04)}.audit-table td{display:grid;grid-template-columns:110px minmax(0,1fr);gap:14px;padding:10px 0;border-bottom:1px solid #f2f4f7}.audit-table td:last-child{border-bottom:0}.audit-table td::before{color:#667085;content:attr(data-label);font-size:9px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}.audit-table tbody tr:hover{background:#fff}
}
@media(max-width:560px){.audit-filters{grid-template-columns:1fr}.filter-actions{grid-column:auto;flex-direction:column}.filter-actions .button{width:100%}.audit-table td{grid-template-columns:1fr;gap:5px}.panel-body{padding:18px}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'audit-logs'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
<header class="topbar"><div><div class="eyebrow">Security and accountability</div><h1>Audit logs</h1><p class="subtitle">Monitor logins and system actions by user and Philippine time.</p></div></header>
<section class="stats-grid audit-summary" aria-label="Audit summary">
<article class="stat-card"><div class="stat-head"><span class="stat-label">Events today</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16v14H4zM8 3v4m8-4v4M4 9h16"/></svg></span></div><div class="stat-value">{{ number_format($summary['today']) }}</div><div class="stat-note">Recorded in Philippine time</div></article>
<article class="stat-card"><div class="stat-head"><span class="stat-label">Successful logins</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 11V8a3 3 0 0 1 6 0v3M6 11h12v9H6zM12 15v2"/></svg></span></div><div class="stat-value">{{ number_format($summary['logins']) }}</div><div class="stat-note">All authenticated sessions</div></article>
<article class="stat-card"><div class="stat-head"><span class="stat-label">Failed sign-ins</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 8v5m0 3h.01"/></svg></span></div><div class="stat-value">{{ number_format($summary['failed_logins']) }}</div><div class="stat-note">Attempts requiring attention</div></article>
</section>
<section class="panel audit-filter-panel"><div class="panel-header"><div><h2 class="panel-title">Filter activity</h2><p class="panel-description">Narrow the log by actor, action, or date range.</p></div></div><div class="panel-body"><form class="audit-filters" method="GET" action="{{ route('admin.audit-logs.index') }}" data-async-filter data-async-filter-target="#audit-results" data-no-transition>
<div class="filter-field"><label for="user_id">User</label><select class="filter-select" id="user_id" name="user_id"><option value="">All users</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)request('user_id') === (string)$user->id)>{{ $user->name }} - {{ $user->email }}</option>@endforeach</select></div>
<div class="filter-field"><label for="event_action">Action</label><select class="filter-select" id="event_action" name="event_action"><option value="">All actions</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('event_action', request('action')) === $action)>{{ str($action)->replace('_',' ')->title() }}</option>@endforeach</select></div>
<div class="filter-field"><label for="date_from">From</label><input class="filter-select" type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"></div>
<div class="filter-field"><label for="date_to">To</label><input class="filter-select" type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"></div>
<div class="filter-actions"><button class="button button-primary" type="submit">Apply filters</button><a class="button button-secondary" href="{{ route('admin.audit-logs.index') }}" data-async-filter-link data-async-filter-target="#audit-results" data-no-transition>Clear</a></div>
</form></div></section>
<section class="panel async-filter-region" id="audit-results" data-async-filter-region data-async-filter-target="#audit-results" aria-live="polite"><div class="panel-header"><div><h2 class="panel-title">Recorded activity</h2><p class="panel-description">{{ number_format($logs->total()) }} matching event(s).</p></div></div>
@if($logs->isEmpty())<div class="empty-state"><strong>No audit activity found</strong>Try a wider date range or clear the filters.</div>@else<div class="table-wrap"><table class="audit-table"><thead><tr><th>Time (PHT)</th><th>User</th><th>Action</th><th>Description</th><th>Network</th></tr></thead><tbody>
@foreach($logs as $log)<tr><td data-label="Time (PHT)"><div class="cell-title">{{ $log->occurred_at->timezone('Asia/Manila')->format('M d, Y') }}</div><div class="audit-meta">{{ $log->occurred_at->timezone('Asia/Manila')->format('h:i:s A') }}</div></td><td data-label="User"><div class="cell-title">{{ $log->user?->name ?? 'Guest / deleted user' }}</div><div class="audit-meta">{{ $log->user?->email ?: 'No account email' }}</div></td><td data-label="Action"><span class="audit-action">{{ str($log->action)->replace('_',' ')->title() }}</span></td><td data-label="Description"><div class="audit-primary">{{ $log->description }}</div><div class="audit-meta">{{ $log->method }} {{ $log->route ?: 'unnamed route' }} @if(data_get($log->metadata,'response_status')) &middot; HTTP {{ data_get($log->metadata,'response_status') }} @endif</div></td><td class="audit-network" data-label="Network"><div class="audit-primary">{{ $log->ip_address ?: 'Unknown IP' }}</div><div class="audit-meta audit-agent" title="{{ $log->user_agent }}">{{ $log->user_agent ?: 'User agent unavailable' }}</div></td></tr>@endforeach
</tbody></table></div>@if($logs->hasPages())<div class="pagination-wrap">{{ $logs->onEachSide(1)->links() }}</div>@endif @endif
</section></div></main>
</body></html>
