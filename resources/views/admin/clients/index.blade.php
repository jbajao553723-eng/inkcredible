<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clients | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.client-tabs { display:flex; gap:6px; margin-bottom:22px; padding:6px; width:max-content; max-width:100%; background:#eef1f6; border-radius:12px; }
.client-tab { display:flex; align-items:center; gap:8px; min-height:40px; padding:9px 14px; color:#475467; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; }
.client-tab:hover { color:#344054; background:#f8fafc; }
.client-tab.active { color:#4338ca; background:#fff; box-shadow:0 1px 3px rgba(16,24,40,.12); }
.tab-count { display:inline-flex; align-items:center; justify-content:center; min-width:21px; height:21px; padding:0 6px; color:#475467; background:#e9edf3; border-radius:999px; font-size:10px; }
.client-tab.active .tab-count { color:#4338ca; background:#eef2ff; }
.client-photo { object-fit:cover; box-shadow:0 0 0 2px #fff,0 0 0 3px #e4e7ec; }
@media (max-width:560px) { .client-tabs { width:100%; } .client-tab { flex:1; justify-content:center; } }
</style>
</head>
<body>
@php
    $clientsWithLoans = $clients->filter(fn ($client) => $client->loans->isNotEmpty())->count();
    $activeBorrowers = $clients->filter(fn ($client) => $client->loans->contains('status', 'approved'))->count();
    $pendingCount = $verifications->where('status', 'pending')->count();
    $approvedCount = $verifications->where('status', 'approved')->count();
@endphp
@include('partials.admin-sidebar', ['active' => 'clients'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Client management</div><h1>Clients and verification</h1><p class="subtitle">Manage registered clients, lending activity, and verification reviews in one workspace.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></div>
    </header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Total clients</div><div class="stat-value">{{ $clients->count() }}</div><div class="stat-note">Registered client accounts</div></article>
        <article class="stat-card"><div class="stat-label">Awaiting review</div><div class="stat-value">{{ $pendingCount }}</div><div class="stat-note">Pending verification decisions</div></article>
        <article class="stat-card"><div class="stat-label">Verified clients</div><div class="stat-value">{{ $approvedCount }}</div><div class="stat-note">Eligible to request loans</div></article>
        <article class="stat-card"><div class="stat-label">Active borrowers</div><div class="stat-value">{{ $activeBorrowers }}</div><div class="stat-note">Currently approved loans</div></article>
    </section>

    <nav class="client-tabs" aria-label="Client management sections">
        <a class="client-tab {{ $section === 'directory' ? 'active' : '' }}" href="{{ route('admin.clients') }}" @if($section === 'directory') aria-current="page" @endif>Client directory <span class="tab-count">{{ $clients->count() }}</span></a>
        <a class="client-tab {{ $section === 'verifications' ? 'active' : '' }}" href="{{ route('admin.clients', ['section' => 'verifications']) }}" @if($section === 'verifications') aria-current="page" @endif>Verification queue <span class="tab-count">{{ $pendingCount }}</span></a>
    </nav>

    @if($section === 'directory')
        <section class="panel" data-admin-table>
            <div class="panel-header"><div><h2 class="panel-title">Client directory</h2><p class="panel-description">View profiles, verification status, and loan activity. Newest accounts appear first.</p></div><span class="badge badge-neutral">{{ $clientsWithLoans }} with loans</span></div>
            <div class="toolbar"><input class="search-input" id="client-search" type="search" placeholder="Search name, email, or contact..." aria-label="Search clients"><select class="filter-select" id="client-status" aria-label="Filter verification status"><option value="">All verification statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option><option value="not-submitted">Not submitted</option></select></div>
            @if($clients->isEmpty())
                <div class="empty-state"><strong>No clients found</strong>Registered client accounts will appear here.</div>
            @else
                <div class="table-wrap"><table><thead><tr><th>Client</th><th>Contact</th><th>Loan activity</th><th>Registered</th><th>Verification</th><th>Action</th></tr></thead>
                    <tbody id="client-table">
                    @foreach($clients as $client)
                        @php
                            $registered = $client->created_at?->copy()->timezone('Asia/Manila');
                            $verificationStatus = $client->clientVerification?->status ?? 'not-submitted';
                            $verificationLabel = $verificationStatus === 'not-submitted' ? 'Not submitted' : ucfirst($verificationStatus);
                            $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                        @endphp
                        <tr data-status="{{ $verificationStatus }}" data-search="{{ strtolower($client->name.' '.$client->email.' '.$client->contact_number) }}">
                            <td><div class="identity">@if($client->profile_photo_path)<img class="avatar client-photo" src="{{ route('admin.clients.photo', ['client' => $client, 'v' => $client->updated_at?->timestamp]) }}" alt="{{ $client->name }} profile photo">@else<span class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span>@endif<span><span class="cell-title">{{ $client->name }}</span><span class="cell-secondary">{{ $client->email }}</span></span></div></td>
                            <td><div>{{ $client->contact_number ?: 'Not provided' }}</div><div class="cell-secondary">{{ $client->address ?: 'Address not provided' }}</div></td>
                            <td><div class="cell-title">{{ $client->loans->count() }} {{ Str::plural('loan', $client->loans->count()) }}</div><div class="cell-secondary">{{ $client->loans->where('status', 'approved')->count() }} active</div></td>
                            <td><div>{{ $registered?->format('M d, Y') }}</div><div class="cell-secondary">{{ $registered?->format('h:i A') }} PHT</div></td>
                            <td><span class="badge {{ $verificationClass }}">{{ $verificationLabel }}</span></td>
                            <td>@if($client->clientVerification)<a class="button button-secondary button-small" href="{{ route('admin.verifications.show', $client->clientVerification) }}">Review details</a>@else<span class="cell-secondary">Awaiting submission</span>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
                <div class="empty-state" id="client-empty" hidden><strong>No matching clients</strong>Try a different search term or verification status.</div>
            @endif
        </section>
    @else
        <section class="panel" data-admin-table>
            <div class="panel-header"><div><h2 class="panel-title">Verification queue</h2><p class="panel-description">Pending submissions appear first, followed by the latest reviewed records.</p></div><span class="badge badge-warning">{{ $pendingCount }} pending</span></div>
            <div class="toolbar">
                <input class="search-input" id="verification-search" type="search" placeholder="Search client, company, or job..." aria-label="Search verifications">
                <select class="filter-select" id="verification-status" aria-label="Filter by status"><option value="">All statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select>
            </div>
            @if($verifications->isEmpty())
                <div class="empty-state"><strong>No verification submissions</strong>Client submissions will appear here for review.</div>
            @else
                <div class="table-wrap"><table><thead><tr><th>Client</th><th>Email</th><th>Employment</th><th>Declared income</th><th>Submitted</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody id="verification-table">
                    @foreach($verifications as $verification)
                        @php
                            $submitted = $verification->submitted_at?->copy()->timezone('Asia/Manila');
                            $statusClass = match ($verification->status) { 'approved' => 'badge-success', 'rejected' => 'badge-danger', default => 'badge-warning' };
                        @endphp
                        <tr data-status="{{ $verification->status }}" data-search="{{ strtolower(($verification->user?->first_name ?? '').' '.($verification->user?->last_name ?? '').' '.($verification->user?->email ?? '').' '.$verification->company_name.' '.$verification->job_title) }}">
                            <td><div class="identity">@if($verification->user?->profile_photo_path)<img class="avatar client-photo" src="{{ route('admin.clients.photo', ['client' => $verification->user, 'v' => $verification->user->updated_at?->timestamp]) }}" alt="{{ $verification->user->full_name }} profile photo">@else<span class="avatar">{{ strtoupper(substr($verification->user?->first_name ?? '?', 0, 1)) }}</span>@endif<span class="cell-title">{{ $verification->user?->full_name ?? 'Deleted client' }}</span></div></td>
                            <td class="email-cell"><span class="email-value">{{ $verification->user?->email ?? 'Account unavailable' }}</span></td>
                            <td><div class="cell-title">{{ $verification->job_title ?: str($verification->employment_status)->replace('_', ' ')->title() }}</div><div class="cell-secondary">{{ $verification->company_name ?: $verification->source_of_income }}</div></td>
                            <td class="amount">PHP {{ number_format((float) $verification->monthly_income, 2) }}<div class="cell-secondary">per month</div></td>
                            <td><div>{{ $submitted?->format('M d, Y') }}</div><div class="cell-secondary">{{ $submitted?->format('h:i A') }} PHT</div></td>
                            <td><span class="badge {{ $statusClass }}">{{ $verification->status }}</span></td>
                            <td><a class="button button-secondary button-small" href="{{ route('admin.verifications.show', $verification) }}">Review details</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table></div>
                <div class="empty-state" id="verification-empty" hidden><strong>No matching submissions</strong>Change the search text or status filter.</div>
            @endif
        </section>
    @endif
</div></main>
</body></html>
