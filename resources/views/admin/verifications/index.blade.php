<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Verifications | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@php
    $pendingCount = $verifications->where('status', 'pending')->count();
    $approvedCount = $verifications->where('status', 'approved')->count();
    $rejectedCount = $verifications->where('status', 'rejected')->count();
@endphp
@include('partials.admin-sidebar', ['active' => 'verifications'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Identity and eligibility</div><h1>Client verifications</h1><p class="subtitle">Review submitted employment, income, identity, and supporting documents before enabling loan requests.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a></div>
    </header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Awaiting review</div><div class="stat-value">{{ $pendingCount }}</div><div class="stat-note">Pending admin decision</div></article>
        <article class="stat-card"><div class="stat-label">Approved</div><div class="stat-value">{{ $approvedCount }}</div><div class="stat-note">Eligible to request loans</div></article>
        <article class="stat-card"><div class="stat-label">Rejected</div><div class="stat-value">{{ $rejectedCount }}</div><div class="stat-note">May correct and resubmit</div></article>
        <article class="stat-card"><div class="stat-label">Total submissions</div><div class="stat-value">{{ $verifications->count() }}</div><div class="stat-note">All verification records</div></article>
    </section>

    <section class="panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Review queue</h2><p class="panel-description">Pending submissions appear first, followed by the latest reviewed records.</p></div><span class="badge badge-warning">{{ $pendingCount }} pending</span></div>
        <div class="toolbar">
            <input class="search-input" id="verification-search" type="search" placeholder="Search client, company, or job..." aria-label="Search verifications">
            <select class="filter-select" id="verification-status" aria-label="Filter by status"><option value="">All statuses</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option></select>
        </div>
        @if($verifications->isEmpty())
            <div class="empty-state"><strong>No verification submissions</strong>Client submissions will appear here for review.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Client</th><th>Employment</th><th>Declared income</th><th>Submitted</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="verification-table">
                @foreach($verifications as $verification)
                    @php
                        $submitted = $verification->submitted_at?->copy()->timezone('Asia/Manila');
                        $statusClass = match ($verification->status) { 'approved' => 'badge-success', 'rejected' => 'badge-danger', default => 'badge-warning' };
                    @endphp
                    <tr data-status="{{ $verification->status }}" data-search="{{ strtolower(($verification->user?->first_name ?? '').' '.($verification->user?->last_name ?? '').' '.($verification->user?->email ?? '').' '.$verification->company_name.' '.$verification->job_title) }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($verification->user?->first_name ?? '?', 0, 1)) }}</span><span><span class="cell-title">{{ $verification->user?->full_name ?? 'Deleted client' }}</span><span class="cell-secondary">{{ $verification->user?->email ?? 'Account unavailable' }}</span></span></div></td>
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
</div></main>
</body></html>
