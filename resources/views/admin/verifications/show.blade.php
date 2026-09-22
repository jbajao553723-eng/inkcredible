<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verification Review | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
textarea { width:100%; min-height:120px; padding:11px; color:#344054; border:1px solid #d0d5dd; border-radius:9px; resize:vertical; font:inherit; font-size:12px; }
textarea:focus { outline:none; border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.document-card { display:block; padding:16px; color:#344054; background:#f9fafb; border:1px solid var(--border); border-radius:12px; text-decoration:none; }
.document-card:hover { border-color:#c7d2fe; }
.document-card strong { display:block; margin-bottom:5px; }
.privacy-note { padding:14px; color:#344054; background:#f8fafc; border:1px solid var(--border); border-radius:10px; font-size:11px; line-height:1.55; }
</style>
</head>
<body>
@php
    $statusClass = match ($verification->status) { 'approved' => 'badge-success', 'rejected' => 'badge-danger', default => 'badge-warning' };
    $submitted = $verification->submitted_at?->copy()->timezone('Asia/Manila');
    $reviewed = $verification->reviewed_at?->copy()->timezone('Asia/Manila');
    $loanCount = $verification->user?->loans->count() ?? 0;
@endphp
@include('partials.admin-sidebar', ['active' => 'verifications'])
<main class="main"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Verification review</div><h1>{{ $verification->user?->full_name ?? 'Client record' }}</h1><p class="subtitle">Compare the submitted profile and private evidence before recording a decision.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.verifications.index') }}">Back to queue</a><span class="badge {{ $statusClass }}">{{ $verification->status }}</span></div>
    </header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Monthly income</div><div class="stat-value">PHP {{ number_format((float) $verification->monthly_income, 2) }}</div><div class="stat-note">Client-declared amount</div></article>
        <article class="stat-card"><div class="stat-label">Employment length</div><div class="stat-value">{{ $verification->employment_length_months }}</div><div class="stat-note">Months declared</div></article>
        <article class="stat-card"><div class="stat-label">Existing loans</div><div class="stat-value">{{ $loanCount }}</div><div class="stat-note">Loans attached to this client</div></article>
        <article class="stat-card"><div class="stat-label">Submitted</div><div class="stat-value" style="font-size:18px">{{ $submitted?->format('M d, Y') }}</div><div class="stat-note">{{ $submitted?->format('h:i A') }} PHT</div></article>
    </section>

    <div class="detail-layout">
        <div>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Client and employment profile</h2><p class="panel-description">Personal and financial details provided by the client.</p></div></div>
                <div class="panel-body detail-grid">
                    <div class="detail-item"><div class="detail-label">First name</div><div class="detail-value">{{ $verification->user?->first_name ?? 'Unavailable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Last name</div><div class="detail-value">{{ $verification->user?->last_name ?? 'Unavailable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Email address</div><div class="detail-value">{{ $verification->user?->email ?? 'Unavailable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Contact number</div><div class="detail-value">{{ $verification->user?->contact_number ?: 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Address</div><div class="detail-value">{{ $verification->user?->address ?: 'Not provided' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Employment status</div><div class="detail-value">{{ str($verification->employment_status)->replace('_', ' ')->title() }}</div></div>
                    <div class="detail-item"><div class="detail-label">Company / business</div><div class="detail-value">{{ $verification->company_name ?: 'Not applicable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Position / occupation</div><div class="detail-value">{{ $verification->job_title ?: 'Not applicable' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Income source</div><div class="detail-value">{{ $verification->source_of_income }}</div></div>
                    <div class="detail-item"><div class="detail-label">Valid ID type</div><div class="detail-value">{{ $verification->valid_id_type }}</div></div>
                    <div class="detail-item"><div class="detail-label">Valid ID number</div><div class="detail-value">{{ $verification->valid_id_number }}</div></div>
                </div>
                @if($verification->additional_information)
                    <div class="panel-body" style="border-top:1px solid var(--border)"><div class="detail-label">Additional background</div><div class="detail-value" style="line-height:1.65">{{ $verification->additional_information }}</div></div>
                @endif
            </section>

            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Review history</h2><p class="panel-description">Submission and decision audit information.</p></div></div>
                <div class="panel-body detail-grid">
                    <div class="detail-item"><div class="detail-label">Submitted at</div><div class="detail-value">{{ $submitted?->format('M d, Y - h:i A') }} PHT</div></div>
                    <div class="detail-item"><div class="detail-label">Reviewed at</div><div class="detail-value">{{ $reviewed ? $reviewed->format('M d, Y - h:i A').' PHT' : 'Awaiting review' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Reviewed by</div><div class="detail-value">{{ $verification->reviewer?->full_name ?? 'Not reviewed' }}</div></div>
                    <div class="detail-item"><div class="detail-label">Current result</div><div class="detail-value"><span class="badge {{ $statusClass }}">{{ $verification->status }}</span></div></div>
                </div>
                @if($verification->rejection_reason)<div class="panel-body" style="border-top:1px solid var(--border)"><div class="notice"><strong>Rejection reason</strong><br>{{ $verification->rejection_reason }}</div></div>@endif
            </section>
        </div>

        <aside>
            <section class="panel">
                <div class="panel-header"><div><h2 class="panel-title">Private documents</h2><p class="panel-description">Open each protected file to verify identity.</p></div></div>
                <div class="panel-body" style="display:grid;gap:12px">
                    <a class="document-card" href="{{ route('admin.verifications.document', [$verification, 'valid-id']) }}" target="_blank" rel="noopener"><strong>Valid identification</strong><span class="cell-secondary">Open the submitted ID image or PDF</span></a>
                    <a class="document-card" href="{{ route('admin.verifications.document', [$verification, 'selfie']) }}" target="_blank" rel="noopener"><strong>Selfie holding valid ID</strong><span class="cell-secondary">Open the submitted identity selfie</span></a>
                    <div class="privacy-note"><strong>Restricted personal data.</strong> Access these files only for authorized verification, do not download or share them unnecessarily, and record decisions based on relevant lending criteria.</div>
                </div>
            </section>

            @if($verification->status === 'pending')
                <section class="panel">
                    <div class="panel-header"><div><h2 class="panel-title">Verification decision</h2><p class="panel-description">Approval immediately enables loan requests.</p></div></div>
                    <div class="panel-body" style="display:grid;gap:18px">
                        <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}" data-confirm="Approve this client verification?">@csrf<button class="button button-success" style="width:100%" type="submit">Approve verification</button></form>
                        <form method="POST" action="{{ route('admin.verifications.reject', $verification) }}" data-confirm="Reject this submission and return it to the client?">
                            @csrf
                            <label class="detail-label" for="rejection_reason">Reason for rejection</label>
                            <textarea id="rejection_reason" name="rejection_reason" required maxlength="1000" placeholder="Explain what must be corrected or resubmitted...">{{ old('rejection_reason') }}</textarea>
                            <button class="button button-danger" style="width:100%;margin-top:10px" type="submit">Reject and request changes</button>
                        </form>
                    </div>
                </section>
            @else
                <section class="panel"><div class="panel-body"><div class="alert {{ $verification->status === 'approved' ? 'alert-success' : 'alert-error' }}" style="margin:0">This submission has been reviewed. A rejected client may update the form and resubmit it from Settings.</div></div></section>
            @endif
        </aside>
    </div>
</div></main>
@include('partials.admin-confirmation')
</body></html>
