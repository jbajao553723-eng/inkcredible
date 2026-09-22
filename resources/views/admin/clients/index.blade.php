<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clients | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>@include('partials.admin-styles')</style>
</head>
<body>
@php
    $clientsWithLoans = $clients->filter(fn ($client) => $client->loans->isNotEmpty())->count();
    $activeBorrowers = $clients->filter(fn ($client) => $client->loans->contains('status', 'approved'))->count();
@endphp
@include('partials.admin-sidebar', ['active' => 'clients'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Client management</div><h1>Registered clients</h1><p class="subtitle">Review client profiles and lending activity.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.dashboard') }}">Dashboard</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="button button-secondary" type="submit">Log out</button></form></div>
    </header>

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Total clients</div><div class="stat-value">{{ $clients->count() }}</div><div class="stat-note">Registered client accounts</div></article>
        <article class="stat-card"><div class="stat-label">Clients with loans</div><div class="stat-value">{{ $clientsWithLoans }}</div><div class="stat-note">At least one application</div></article>
        <article class="stat-card"><div class="stat-label">Active borrowers</div><div class="stat-value">{{ $activeBorrowers }}</div><div class="stat-note">Currently approved balances</div></article>
        <article class="stat-card"><div class="stat-label">New this month</div><div class="stat-value">{{ $clients->filter(fn ($client) => $client->created_at?->isCurrentMonth())->count() }}</div><div class="stat-note">Recent registrations</div></article>
    </section>

    <section class="panel" data-admin-table>
        <div class="panel-header"><div><h2 class="panel-title">Client directory</h2><p class="panel-description">Newest accounts are shown first.</p></div><span class="badge badge-neutral">{{ $clients->count() }} records</span></div>
        <div class="toolbar"><input class="search-input" id="client-search" type="search" placeholder="Search name, email, or contact…" aria-label="Search clients"></div>
        @if($clients->isEmpty())
            <div class="empty-state"><strong>No clients found</strong>Registered client accounts will appear here.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Client</th><th>Contact</th><th>Address</th><th>Loan activity</th><th>Registered</th><th>Verification</th><th>Report</th></tr></thead>
                <tbody id="client-table">
                @foreach($clients as $client)
                    @php
                        $registered = $client->created_at?->copy()->timezone('Asia/Manila');
                        $verificationStatus = $client->clientVerification?->status ?? 'not submitted';
                        $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                    @endphp
                    <tr data-search="{{ strtolower($client->name.' '.$client->email.' '.$client->contact_number) }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span><span><span class="cell-title">{{ $client->name }}</span><span class="cell-secondary">{{ $client->email }}</span></span></div></td>
                        <td><div>{{ $client->contact_number ?: 'Not provided' }}</div><div class="cell-secondary">{{ $client->age ? $client->age.' years old' : 'Age not provided' }}</div></td>
                        <td>{{ $client->address ?: 'Not provided' }}</td>
                        <td><div class="cell-title">{{ $client->loans->count() }} {{ Str::plural('loan', $client->loans->count()) }}</div><div class="cell-secondary">{{ $client->loans->where('status', 'approved')->count() }} active</div></td>
                        <td><div>{{ $registered?->format('M d, Y') }}</div><div class="cell-secondary">{{ $registered?->format('h:i A') }} PHT</div></td>
                        <td>@if($client->clientVerification)<a href="{{ route('admin.verifications.show', $client->clientVerification) }}" class="badge {{ $verificationClass }}" style="text-decoration:none">{{ ucfirst($verificationStatus) }}</a>@else<span class="badge {{ $verificationClass }}">Not submitted</span>@endif</td>
                        <td><a class="button button-secondary button-small" href="{{ route('admin.clients.report', $client) }}" aria-label="Download PDF report for {{ $client->name }}">Download PDF</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="client-empty" hidden><strong>No matching clients</strong>Try a different search term.</div>
        @endif
    </section>
</div></main>
</body></html>
