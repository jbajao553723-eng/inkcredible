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
textarea { width:100%; min-height:96px; padding:11px; color:#344054; border:1px solid #d0d5dd; border-radius:9px; resize:vertical; font:inherit; font-size:0.75rem; }
textarea:focus { outline:none; border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.review-identity { display:grid; grid-template-columns:auto minmax(0,1fr) minmax(185px,.38fr); gap:20px; align-items:center; margin-bottom:22px; padding:22px; border-color:#dfe3ea; box-shadow:0 8px 24px rgba(16,24,40,.045); }
.review-photo { display:grid; place-items:center; width:86px; height:86px; overflow:hidden; color:#4338ca; background:linear-gradient(145deg,#eef2ff,#e0e7ff); border:4px solid #fff; border-radius:20px; box-shadow:0 6px 18px rgba(16,24,40,.12); font-size:1.5rem; font-weight:700; }
.review-photo img { width:100%; height:100%; object-fit:cover; }
.review-kicker { color:#6366f1; font-size:0.5625rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
.review-name { margin:5px 0 0; font-size:1.25rem; letter-spacing:-.025em; }
.review-contact { display:flex; gap:8px 16px; margin-top:10px; color:#667085; font-size:0.625rem; flex-wrap:wrap; }
.review-address { margin-top:8px; color:#98a2b3; font-size:0.625rem; line-height:1.45; }
.review-account { display:grid; gap:10px; padding-left:20px; border-left:1px solid #f2f4f7; }
.review-account-row { display:flex; justify-content:space-between; gap:12px; }
.review-account-row span { color:#98a2b3; font-size:0.5625rem; }
.review-account-row strong { color:#344054; font-size:0.625rem; font-weight:600; text-align:right; }
.document-card { display:block; padding:16px; color:#344054; background:#f9fafb; border:1px solid var(--border); border-radius:12px; text-decoration:none; }
.document-card:hover { border-color:#c7d2fe; }
.document-card strong { display:block; margin-bottom:5px; }
.privacy-note { padding:14px; color:#344054; background:#f8fafc; border:1px solid var(--border); border-radius:10px; font-size:0.6875rem; line-height:1.55; }
.signature-review { padding:14px; background:#fff; border:1px solid #d9d6fe; border-radius:12px; text-align:center; }
.signature-review img { width:100%; height:72px; object-fit:contain; }
.signature-review span { display:block; margin-top:7px; color:#667085; font-size:0.5625rem; }
.decision-body { display:grid; gap:16px; }
.decision-reason { display:grid; gap:7px; }
.decision-help { margin:0; color:var(--muted); font-size:0.6875rem; line-height:1.45; }
.decision-actions { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:10px; padding-top:2px; }
.decision-actions .button { width:100%; min-height:42px; }
@media (max-width:760px) { .review-identity { grid-template-columns:auto minmax(0,1fr); } .review-account { grid-column:1/-1; padding:16px 0 0; border-top:1px solid #f2f4f7; border-left:0; } }
@media (max-width:480px) { .review-identity { grid-template-columns:1fr; justify-items:start; } .review-photo { width:74px; height:74px; } }
@media (max-width:380px) { .decision-actions { grid-template-columns:1fr; } }
</style>
</head>
<body>
@php
    $submitted = $verification->submitted_at?->copy()->timezone('Asia/Manila');
    $reviewed = $verification->reviewed_at?->copy()->timezone('Asia/Manila');
    $client = $verification->user;
    $loanCount = $client?->loans->count() ?? 0;
    $activeLoanCount = $client?->loans->where('status', 'approved')->count() ?? 0;
    $registered = $client?->created_at?->copy()->timezone('Asia/Manila');
    $initials = $client
        ? collect([$client->first_name, $client->last_name])->filter()->map(fn ($name) => Str::upper(Str::substr($name, 0, 1)))->implode('')
        : '?';
@endphp
@include('partials.admin-sidebar', ['active' => 'clients'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Verification review</div><h1>Review details</h1><p class="subtitle">Confirm {{ $client?->full_name ?? 'the client' }}'s profile and private evidence before recording a decision.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.clients', ['section' => 'verifications']) }}">Back to queue</a><x-status-badge :status="$verification->status" /></div>
    </header>

    <x-flash-messages />
    @if($errors->any())<div class="alert alert-error" role="alert">{{ $errors->first() }}</div>@endif

    @if($client)
        <section class="panel review-identity">
            <div class="review-photo">@if($client->profile_photo_path)<img src="{{ route('admin.clients.photo', ['client' => $client, 'v' => $client->updated_at?->timestamp]) }}" alt="{{ $client->full_name }} profile photo">@else<span>{{ $initials ?: 'U' }}</span>@endif</div>
            <div>
                <div class="review-kicker">Client #{{ $client->id }}</div>
                <h2 class="review-name">{{ $client->full_name }}</h2>
                <div class="review-contact"><span>{{ $client->email }}</span><span>{{ $client->contact_number ?: 'No contact number' }}</span><span>Age {{ $client->age ?: 'not provided' }}</span></div>
                <div class="review-address">{{ $client->address ?: 'Address not provided' }}</div>
            </div>
            <div class="review-account">
                <div class="review-account-row"><span>Verification</span><strong><x-status-badge :status="$verification->status" /></strong></div>
                <div class="review-account-row"><span>Email</span><strong>{{ $client->email_verified_at ? 'Verified' : 'Not verified' }}</strong></div>
                <div class="review-account-row"><span>Registered</span><strong>{{ $registered?->format('M d, Y') ?? 'Unavailable' }}</strong></div>
                <div class="review-account-row"><span>Active loans</span><strong>{{ $activeLoanCount }}</strong></div>
            </div>
        </section>
    @endif

    <section class="stats-grid">
        <article class="stat-card"><div class="stat-label">Monthly income</div><div class="stat-value">PHP {{ number_format((float) $verification->monthly_income, 2) }}</div><div class="stat-note">Client-declared amount</div></article>
        <article class="stat-card"><div class="stat-label">Employment length</div><div class="stat-value">{{ $verification->employment_length_months }}</div><div class="stat-note">Months declared</div></article>
        <article class="stat-card"><div class="stat-label">Existing loans</div><div class="stat-value">{{ $loanCount }}</div><div class="stat-note">Loans attached to this client</div></article>
        <article class="stat-card"><div class="stat-label">Submitted</div><div class="stat-value" style="font-size:1.125rem">{{ $submitted?->format('M d, Y') }}</div><div class="stat-note">{{ $submitted?->format('h:i A') }} PHT</div></article>
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
                    <div class="detail-item"><div class="detail-label">Age</div><div class="detail-value">{{ $verification->user?->age ?: 'Not provided' }}</div></div>
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
                    <div class="detail-item"><div class="detail-label">Current result</div><div class="detail-value"><x-status-badge :status="$verification->status" /></div></div>
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
                    @if($verification->payslip_path)<a class="document-card" href="{{ route('admin.verifications.document', [$verification, 'payslip']) }}" target="_blank" rel="noopener"><strong>Recent payslip</strong><span class="cell-secondary">Review income evidence before approval</span></a>@else<div class="document-card"><strong>No payslip provided</strong><span class="cell-secondary">The assessment receives no verified-income-evidence bonus.</span></div>@endif
                    @if($verification->digital_signature)<div class="signature-review"><img src="{{ $verification->digital_signature }}" alt="{{ $client?->full_name }} digital signature"><span>Digital signature captured {{ $verification->signature_captured_at?->timezone('Asia/Manila')->format('M d, Y - h:i A') }} PHT</span></div>@else<div class="alert alert-error" style="margin:0">Digital signature missing. Request changes before approving this verification.</div>@endif
                    <div class="privacy-note"><strong>Restricted personal data.</strong> Access these files only for authorized verification, do not download or share them unnecessarily, and record decisions based on relevant lending criteria.</div>
                </div>
            </section>

            @if($verification->status === 'pending')
                <section class="panel">
                    <div class="panel-header"><div><h2 class="panel-title">Verification decision</h2><p class="panel-description">Approval immediately enables loan requests.</p></div></div>
                    <div class="panel-body decision-body">
                        <form id="approve-verification" method="POST" action="{{ route('admin.verifications.approve', $verification) }}" data-confirm="Approve this client verification?">@csrf</form>
                        <form id="reject-verification" class="decision-reason" method="POST" action="{{ route('admin.verifications.reject', $verification) }}" data-confirm="Reject this submission and return it to the client?">
                            @csrf
                            <label class="detail-label" for="rejection_reason">Reason for rejection</label>
                            <textarea id="rejection_reason" name="rejection_reason" required maxlength="1000" placeholder="Explain what must be corrected or resubmitted...">{{ old('rejection_reason') }}</textarea>
                            <p class="decision-help">Required only when requesting changes. The client will see this message.</p>
                        </form>
                        <div class="decision-actions">
                            <button class="button button-danger" type="submit" form="reject-verification">Request changes</button>
                            <button class="button button-success" type="submit" form="approve-verification">Approve verification</button>
                        </div>
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
