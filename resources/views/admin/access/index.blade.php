<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Access | Inkcredible</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.access-layout { display:grid; grid-template-columns:minmax(0,1fr) 310px; gap:20px; align-items:start; }
.access-panel { min-width:0; }
.access-toolbar .search-input { flex:1 1 260px; max-width:430px; }
.access-toolbar .filter-select { min-width:155px; }
.access-name { display:flex; align-items:center; gap:11px; min-width:200px; }
.access-avatar { display:grid; place-items:center; width:38px; height:38px; flex:0 0 38px; color:#4338ca; background:#eef2ff; border-radius:11px; font-size:12px; font-weight:800; }
.access-meta { min-width:0; }
.access-meta strong,.access-meta span { display:block; }
.access-meta strong { color:#101828; font-size:12px; }
.access-meta span { margin-top:3px; color:#667085; font-size:10px; overflow-wrap:anywhere; }
.status-dot { width:7px; height:7px; margin-right:5px; border-radius:50%; }
.status-dot.active { background:#12b76a; box-shadow:0 0 0 3px #d1fadf; }
.status-dot.disabled { background:#f04438; box-shadow:0 0 0 3px #fee4e2; }
.access-action { text-align:right; white-space:nowrap; }
.access-action-group { display:flex; justify-content:flex-end; gap:6px; }
.access-action form { display:inline; }
.button-edit { color:#4338ca; background:#f5f3ff; border-color:#c7d2fe; }
.button-edit:hover { color:#3730a3; background:#eef2ff; }
.button-enable { color:#067647; background:#ecfdf3; border-color:#abefc6; }
.button-enable:hover { color:#05603a; background:#d1fadf; }
.button-disable { color:#b42318; background:#fff; border-color:#fecdca; }
.button-disable:hover { color:#912018; background:#fef3f2; }
.security-card { position:sticky; top:20px; overflow:hidden; background:#fff; border:1px solid #e4e7ec; border-radius:16px; box-shadow:0 10px 26px rgba(16,24,40,.06); }
.security-card-head { padding:20px; color:#101828; background:linear-gradient(145deg,#f9f8ff,#f4f3ff); border-bottom:1px solid #e4e7ec; }
.security-card-icon { display:grid; place-items:center; width:40px; height:40px; margin-bottom:15px; color:#4f46e5; background:#e0e7ff; border-radius:11px; }
.security-card-icon svg { width:20px; height:20px; }
.security-card h2 { margin:0; font-size:15px; }
.security-card-head p { margin:7px 0 0; color:#667085; font-size:10px; line-height:1.55; }
.security-list { display:grid; gap:0; margin:0; padding:0; list-style:none; }
.security-list li { display:grid; grid-template-columns:22px 1fr; gap:9px; padding:13px 18px; color:#475467; border-bottom:1px solid #f2f4f7; font-size:10px; line-height:1.45; }
.security-list li:last-child { border-bottom:0; }
.security-check { display:grid; place-items:center; width:18px; height:18px; color:#067647; background:#d1fadf; border-radius:50%; font-size:10px; font-weight:800; }
.create-admin-modal .modal-dialog,.edit-admin-modal .modal-dialog { width:min(680px,calc(100% - 30px)); max-width:680px; }
.create-admin-card { overflow:hidden; background:#fff; border:0; border-radius:20px; box-shadow:0 28px 80px rgba(16,24,40,.24); }
.create-admin-head { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; padding:24px; color:#fff; background:linear-gradient(135deg,#312e81,#4f46e5); }
.create-admin-head h2 { margin:4px 0 0; font-size:21px; }
.create-admin-head p { margin:7px 0 0; max-width:480px; color:#c7d2fe; font-size:11px; line-height:1.5; }
.create-admin-head .btn-close { margin:0; padding:9px; background-color:#fff; border-radius:50%; opacity:.86; }
.create-admin-body { padding:22px 24px 24px; }
.admin-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
.admin-form-group { min-width:0; }
.admin-form-group.full { grid-column:1/-1; }
.admin-form-label { display:block; margin-bottom:6px; color:#344054; font-size:10px; font-weight:700; }
.admin-form-input { width:100%; min-height:41px; padding:9px 11px; color:#101828; background:#fff; border:1px solid #d0d5dd; border-radius:9px; outline:0; font-size:12px; }
.admin-form-input:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.admin-form-input.is-invalid { border-color:#f04438; }
.admin-field-error { margin:5px 0 0; color:#b42318; font-size:9px; }
.admin-form-hint { margin:6px 0 0; color:#98a2b3; font-size:9px; line-height:1.45; }
.create-admin-actions { display:flex; justify-content:flex-end; gap:9px; margin-top:22px; padding-top:18px; border-top:1px solid #eaecf0; }
@media(max-width:1050px){.access-layout{grid-template-columns:1fr}.security-card{position:static}.security-list{grid-template-columns:repeat(2,minmax(0,1fr))}.security-list li:nth-last-child(2){border-bottom:0}.security-list li:nth-child(odd){border-right:1px solid #f2f4f7}}
@media(max-width:620px){.admin-form-grid,.security-list{grid-template-columns:1fr}.admin-form-group.full{grid-column:auto}.security-list li:nth-child(odd){border-right:0}.security-list li:nth-last-child(2){border-bottom:1px solid #f2f4f7}.create-admin-actions{flex-direction:column-reverse}.create-admin-actions .button{width:100%}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'access-control'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar">
        <div><div class="eyebrow">Security administration</div><h1>Admin access control</h1><p class="subtitle">Create administrator accounts and immediately revoke or restore workspace access.</p></div>
        <div class="top-actions"><a class="button button-secondary" href="{{ route('admin.audit-logs.index') }}">View audit logs</a><button class="button button-primary" type="button" data-create-admin><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 5v14m-7-7h14"/></svg>Add administrator</button></div>
    </header>

    @if(session('success'))<div class="alert alert-success" role="status"><strong>Access updated</strong>{{ session('success') }}</div>@endif

    <section class="stats-grid" aria-label="Administrator access summary">
        <article class="stat-card"><div class="stat-head"><span class="stat-label">Administrator accounts</span><span class="stat-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm8-1l2 2 4-4"/></svg></span></div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-note">Accounts managed by superadmins</div></article>
        <article class="stat-card"><div class="stat-head"><span class="stat-label">Active administrators</span><span class="stat-icon" style="color:#079455;background:#ecfdf3"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span></div><div class="stat-value">{{ $stats['active'] }}</div><div class="stat-note">Currently allowed to sign in</div></article>
        <article class="stat-card"><div class="stat-head"><span class="stat-label">Disabled administrators</span><span class="stat-icon" style="color:#d92d20;background:#fef3f2"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M8 12h8"/></svg></span></div><div class="stat-value">{{ $stats['disabled'] }}</div><div class="stat-note">Blocked from all authenticated access</div></article>
        <article class="stat-card"><div class="stat-head"><span class="stat-label">Active superadmins</span><span class="stat-icon" style="color:#7f56d9;background:#f4f3ff"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg></span></div><div class="stat-value">{{ $stats['superadmins'] }}</div><div class="stat-note">Accounts permitted to manage access</div></article>
    </section>

    <div class="access-layout">
        <section class="panel access-panel" data-admin-table>
            <div class="panel-header"><div><h2 class="panel-title">Administrator directory</h2><p class="panel-description">Disabling an account signs it out and blocks future authentication.</p></div><span class="badge badge-neutral">{{ $admins->count() }} accounts</span></div>
            <div class="toolbar access-toolbar"><input class="search-input" type="search" placeholder="Search administrator or email..." aria-label="Search administrators"><select class="filter-select" id="admin-status" aria-label="Filter administrator status"><option value="">All statuses</option><option value="active">Active</option><option value="disabled">Disabled</option></select></div>
            @if($admins->isEmpty())
                <div class="empty-state"><strong>No administrator accounts</strong>Use Add administrator to create the first managed admin account.</div>
            @else
                <div class="table-wrap"><table><thead><tr><th>Administrator</th><th>Status</th><th>Created</th><th>Access history</th><th class="access-action">Action</th></tr></thead><tbody>
                @foreach($admins as $admin)
                    <tr data-search="{{ strtolower($admin->name.' '.$admin->email.' '.$admin->contact_number) }}" data-status="{{ $admin->is_active ? 'active' : 'disabled' }}">
                        <td><div class="access-name"><span class="access-avatar">{{ strtoupper(substr($admin->first_name ?: $admin->name, 0, 1)) }}</span><span class="access-meta"><strong>{{ $admin->name }}</strong><span>{{ $admin->email }}</span><span>{{ $admin->contact_number }}</span></span></div></td>
                        <td><span class="badge {{ $admin->is_active ? 'badge-success' : 'badge-danger' }}"><i class="status-dot {{ $admin->is_active ? 'active' : 'disabled' }}"></i>{{ $admin->is_active ? 'Active' : 'Disabled' }}</span></td>
                        <td><div>{{ $admin->created_at?->timezone('Asia/Manila')->format('M d, Y') }}</div><div class="cell-secondary">{{ $admin->created_at?->timezone('Asia/Manila')->format('h:i A') }} PHT</div></td>
                        <td>@if($admin->disabled_at)<div>Disabled {{ $admin->disabled_at->timezone('Asia/Manila')->format('M d, Y') }}</div><div class="cell-secondary">by {{ $admin->disabledBy?->name ?? 'a superadmin' }}</div>@else<div class="cell-title">Access granted</div><div class="cell-secondary">No restrictions recorded</div>@endif</td>
                        <td class="access-action"><div class="access-action-group"><button class="button button-small button-edit" type="button" data-edit-admin data-admin-id="{{ $admin->id }}" data-admin-first-name="{{ $admin->first_name }}" data-admin-last-name="{{ $admin->last_name }}" data-admin-email="{{ $admin->email }}" data-admin-contact="{{ $admin->contact_number === 'Not provided' ? '' : $admin->contact_number }}" data-admin-update-url="{{ route('admin.access.admins.update', $admin) }}">Edit</button><form method="POST" action="{{ route('admin.access.admins.status', $admin) }}" data-confirm="{{ $admin->is_active ? 'Disable '.$admin->name.'? This immediately blocks sign-in and active sessions.' : 'Restore administrator access for '.$admin->name.'?' }}">@csrf @method('PATCH')<input type="hidden" name="is_active" value="{{ $admin->is_active ? 0 : 1 }}"><button class="button button-small {{ $admin->is_active ? 'button-disable' : 'button-enable' }}" type="submit">{{ $admin->is_active ? 'Disable' : 'Enable' }}</button></form></div></td>
                    </tr>
                @endforeach
                </tbody></table></div>
                <div class="empty-state" hidden><strong>No matching administrators</strong>Clear the filters or search with another name or email.</div>
            @endif
        </section>

        <aside class="security-card" aria-labelledby="security-guardrails-title">
            <div class="security-card-head"><span class="security-card-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg></span><h2 id="security-guardrails-title">Security guardrails</h2><p>Admin access changes are deliberately restricted and auditable.</p></div>
            <ul class="security-list"><li><span class="security-check">✓</span><span>Only active superadmins can open this module or change administrator access.</span></li><li><span class="security-check">✓</span><span>New administrators receive verified accounts but never superadmin privileges.</span></li><li><span class="security-check">✓</span><span>Password changes revoke active sessions and pending password reset links.</span></li><li><span class="security-check">✓</span><span>Creation, profile edits, and access changes are recorded in the audit log.</span></li></ul>
        </aside>
    </div>
</div></main>

@php($editingAdminId = old('editing_admin_id'))
<div class="modal fade edit-admin-modal" id="edit-admin-modal" tabindex="-1" aria-labelledby="edit-admin-title" aria-hidden="true" @if($errors->editAdmin->any()) data-auto-open="true" data-edit-action="{{ route('admin.access.admins.update', $editingAdminId) }}" @endif>
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content create-admin-card">
        <div class="create-admin-head"><div><div class="eyebrow" style="color:#c7d2fe">Administrator profile</div><h2 id="edit-admin-title">Edit administrator</h2><p>Update the administrator's name and contact details, or set a new password. Leaving the password fields empty keeps the current password.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <form method="POST" action="{{ $errors->editAdmin->any() ? route('admin.access.admins.update', $editingAdminId) : route('admin.access.index') }}" data-edit-admin-form>@csrf @method('PATCH')<div class="create-admin-body">
            @if($errors->editAdmin->any())<div class="alert alert-error"><strong>Administrator not updated</strong>Please correct the highlighted fields.</div>@endif
            <input type="hidden" name="editing_admin_id" value="{{ old('editing_admin_id') }}" data-edit-admin-id>
            <div class="admin-form-grid">
                <div class="admin-form-group"><label class="admin-form-label" for="edit_first_name">First name</label><input class="admin-form-input @error('first_name','editAdmin') is-invalid @enderror" id="edit_first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" data-edit-first-name required>@error('first_name','editAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group"><label class="admin-form-label" for="edit_last_name">Last name</label><input class="admin-form-input @error('last_name','editAdmin') is-invalid @enderror" id="edit_last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" data-edit-last-name required>@error('last_name','editAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group full"><label class="admin-form-label" for="edit_admin_email">Email address</label><input class="admin-form-input @error('email','editAdmin') is-invalid @enderror" id="edit_admin_email" name="email" type="email" value="{{ old('email') }}" autocomplete="off" data-edit-email required>@error('email','editAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group full"><label class="admin-form-label" for="edit_contact_number">Contact number <span class="cell-secondary">Optional</span></label><input class="admin-form-input @error('contact_number','editAdmin') is-invalid @enderror" id="edit_contact_number" name="contact_number" value="{{ old('contact_number') }}" inputmode="tel" autocomplete="off" data-edit-contact>@error('contact_number','editAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group"><label class="admin-form-label" for="edit_admin_password">New password <span class="cell-secondary">Optional</span></label><input class="admin-form-input @error('password','editAdmin') is-invalid @enderror" id="edit_admin_password" name="password" type="password" autocomplete="new-password" data-edit-password>@error('password','editAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror<p class="admin-form-hint">At least 8 characters with upper and lowercase letters, a number, and a symbol.</p></div>
                <div class="admin-form-group"><label class="admin-form-label" for="edit_admin_password_confirmation">Confirm new password</label><input class="admin-form-input" id="edit_admin_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" data-edit-password-confirmation></div>
            </div>
            <div class="create-admin-actions"><button class="button button-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="button button-primary" type="submit">Save changes</button></div>
        </div></form>
    </div></div>
</div>

<div class="modal fade create-admin-modal" id="create-admin-modal" tabindex="-1" aria-labelledby="create-admin-title" aria-hidden="true" @if($errors->createAdmin->any()) data-auto-open="true" @endif>
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content create-admin-card">
        <div class="create-admin-head"><div><div class="eyebrow" style="color:#c7d2fe">Privileged account</div><h2 id="create-admin-title">Add an administrator</h2><p>Create a standard administrator with access to lending operations. Only superadmins can manage this account afterward.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <form method="POST" action="{{ route('admin.access.admins.store') }}">@csrf<div class="create-admin-body">
            @if($errors->createAdmin->any())<div class="alert alert-error"><strong>Administrator not created</strong>Please correct the highlighted fields.</div>@endif
            <div class="admin-form-grid">
                <div class="admin-form-group"><label class="admin-form-label" for="first_name">First name</label><input class="admin-form-input @error('first_name','createAdmin') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" autocomplete="given-name" required>@error('first_name','createAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group"><label class="admin-form-label" for="last_name">Last name</label><input class="admin-form-input @error('last_name','createAdmin') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" autocomplete="family-name" required>@error('last_name','createAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group full"><label class="admin-form-label" for="admin_email">Email address</label><input class="admin-form-input @error('email','createAdmin') is-invalid @enderror" id="admin_email" name="email" type="email" value="{{ old('email') }}" autocomplete="off" required>@error('email','createAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group full"><label class="admin-form-label" for="contact_number">Contact number <span class="cell-secondary">Optional</span></label><input class="admin-form-input @error('contact_number','createAdmin') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" inputmode="tel" autocomplete="off">@error('contact_number','createAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror</div>
                <div class="admin-form-group"><label class="admin-form-label" for="admin_password">Temporary password</label><input class="admin-form-input @error('password','createAdmin') is-invalid @enderror" id="admin_password" name="password" type="password" autocomplete="new-password" required>@error('password','createAdmin')<p class="admin-field-error">{{ $message }}</p>@enderror<p class="admin-form-hint">At least 8 characters with upper and lowercase letters, a number, and a symbol.</p></div>
                <div class="admin-form-group"><label class="admin-form-label" for="admin_password_confirmation">Confirm password</label><input class="admin-form-input" id="admin_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required></div>
            </div>
            <div class="create-admin-actions"><button class="button button-secondary" type="button" data-bs-dismiss="modal">Cancel</button><button class="button button-primary" type="submit">Create administrator</button></div>
        </div></form>
    </div></div>
</div>

@include('partials.admin-confirmation')
</body></html>
