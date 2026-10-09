@php
    $reduceAdminMotion = (bool) data_get(auth()->user()?->ui_preferences, 'reduce_motion', false);
@endphp
:root {
    --navy: #101828;
    --muted: #667085;
    --border: #e4e7ec;
    --surface: #fff;
    --canvas: #f7f8fa;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --primary-soft: #eef2ff;
    --primary-rgb: 79,70,229;
    --sidebar-width: 268px;
    --content-padding: 32px;
    --panel-padding: 22px;
    --success: #079455;
    --danger: #d92d20;
}

* { box-sizing: border-box; }
body { margin: 0; color: var(--navy); background: radial-gradient(circle at 96% 0, rgba(99,102,241,.07), transparent 25%), var(--canvas); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
button, input, select { font: inherit; }
button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 3px solid rgba(99,102,241,.32); outline-offset: 2px; }
.skip-link { position:fixed; top:10px; left:10px; z-index:100; padding:9px 12px; color:#fff; background:#4338ca; border-radius:8px; text-decoration:none; transform:translateY(-150%); transition:transform .15s ease; }
.skip-link:focus { transform:translateY(0); }
.sidebar { position: fixed; inset: 0 auto 0 0; width: var(--sidebar-width); padding: 18px 16px; color: #fff; background: linear-gradient(180deg, #111827 0%, #0b1220 100%); border-right: 1px solid rgba(255,255,255,.06); z-index: 10; }
.sidebar-inner { display: flex; flex-direction: column; height: 100%; min-height: 0; }
.sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 10px 10px 22px; color: #fff; border-bottom: 1px solid rgba(255,255,255,.07); text-decoration: none; }
.brand-mark { display: block; width: 36px; height: 36px; flex: 0 0 36px; object-fit: cover; border: 1px solid rgba(255,255,255,.14); border-radius: 11px; background: #fff; box-shadow: 0 8px 20px rgba(239,68,68,.24); }
.brand-copy, .account-copy { display: flex; min-width: 0; flex-direction: column; }
.brand-copy strong { font-size: 1.125rem; letter-spacing: -.02em; }
.brand-copy small { margin-top: 2px; color: #98a2b3; font-size: 0.625rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
.sidebar-nav { flex: 1; min-height: 0; padding: 18px 0; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #344054 transparent; }
.nav-section + .nav-section { margin-top: 20px; }
.nav-label { margin: 0 12px 8px; color: #667085; font-size: 0.625rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
.nav-link { position: relative; display: flex; align-items: center; gap: 12px; margin-bottom: 4px; padding: 10px 12px; color: #98a2b3; border: 1px solid transparent; border-radius: 10px; text-decoration: none; font-size: 0.8125rem; font-weight: 500; transition: .18s ease; }
.nav-link:hover { color: #e4e7ec; background: rgba(255,255,255,.045); }
.nav-link.active { color: #fff; background: linear-gradient(90deg, rgba(var(--primary-rgb),.25), rgba(var(--primary-rgb),.10)); border-color: rgba(var(--primary-rgb),.24); box-shadow: inset 3px 0 var(--primary); }
.nav-link svg { width: 19px; height: 19px; }
.sidebar-account { display: grid; grid-template-columns: 34px minmax(0,1fr) auto; align-items: center; gap: 10px; padding: 14px 6px 4px; border-top: 1px solid rgba(255,255,255,.07); }
.account-avatar { display: grid; place-items: center; width: 34px; height: 34px; flex: 0 0 34px; overflow:hidden; color: #fff; background: #344054; border: 1px solid rgba(255,255,255,.12); border-radius: 10px; font-size: 0.75rem; font-weight: 700; }.account-avatar img { width:100%; height:100%; object-fit:cover; }
.account-copy { flex: 1; }
.account-copy strong { overflow: hidden; color: #f2f4f7; font-size: 0.6875rem; text-overflow: ellipsis; white-space: nowrap; }
.account-copy small { margin-top: 3px; color: #667085; font-size: 0.5625rem; }
.sidebar-logout-form { margin: 0; }
.sidebar-logout { display: grid; place-items: center; width: 34px; height: 34px; padding: 0; color: #98a2b3; background: rgba(255,255,255,.035); border: 1px solid rgba(255,255,255,.08); border-radius: 9px; cursor: pointer; transition: .18s ease; }
.sidebar-logout:hover { color: #fff; background: rgba(217,45,32,.16); border-color: rgba(248,113,113,.24); }
.sidebar-logout svg { width: 17px; height: 17px; }
.sidebar-logout span { position: absolute; width: 1px; height: 1px; padding: 0; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
.main { min-width: 0; min-height: 100vh; margin-left: var(--sidebar-width); padding: var(--content-padding); overflow-x: clip; }
.page-shell { width: 100%; max-width: 1380px; min-width: 0; margin: 0 auto; }
.topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
.topbar > *, .panel-header > *, .stat-card { min-width: 0; }
.eyebrow { margin-bottom: 6px; color: var(--primary); font-size: 0.75rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
h1 { margin: 0; font-size: 1.875rem; font-weight: 700; letter-spacing: -.035em; }
.subtitle { margin: 8px 0 0; color: var(--muted); font-size: 0.875rem; }
.top-actions, .action-group { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
.top-actions form, .action-group form { margin: 0; }
.button { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 39px; padding: 9px 14px; border: 1px solid var(--border); border-radius: 9px; font-size: 0.75rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: .15s ease; }
.button svg { width: 16px; height: 16px; }
.button-secondary { color: #344054; background: #fff; }
.button-secondary:hover { background: #f9fafb; }
.button-primary { color: #fff; background: var(--primary); border-color: var(--primary); }
.button-primary:hover { color: #fff; background: var(--primary-dark); }
.button-success { color: #fff; background: var(--success); border-color: var(--success); }
.button-danger { color: #fff; background: var(--danger); border-color: var(--danger); }
.button-small { min-height: 32px; padding: 6px 10px; font-size: 0.6875rem; }
.alert { margin-bottom: 20px; padding: 14px 16px; border: 1px solid; border-radius: 12px; font-size: 0.8125rem; }
.alert-success { color: #05603a; background: #ecfdf3; border-color: #abefc6; }
.alert-error { color: #912018; background: #fef3f2; border-color: #fecdca; }
.alert strong { display:block; margin-bottom:5px; }
.alert ul { margin:6px 0 0; padding-left:20px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
.stat-card, .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 2px 8px rgba(16, 24, 40, .045); }
.stat-card { padding: 20px; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
.stat-card:hover { border-color: #c7d2fe; box-shadow: 0 10px 24px rgba(16,24,40,.07); transform: translateY(-2px); }
.stat-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.stat-label { color: var(--muted); font-size: 0.8125rem; font-weight: 500; }
.stat-icon { display: grid; place-items: center; width: 36px; height: 36px; color: var(--primary); background: var(--primary-soft); border-radius: 10px; }
.stat-icon svg { width: 18px; height: 18px; }
.stat-value { margin-top: 15px; overflow-wrap: anywhere; font-size: 1.5rem; font-weight: 700; letter-spacing: -.03em; }
.stat-note { margin-top: 5px; color: #98a2b3; font-size: 0.6875rem; }
.panel { min-width: 0; overflow: hidden; }
.panel + .panel { margin-top: 22px; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: var(--panel-padding); border-bottom: 1px solid var(--border); }
.panel-title { margin: 0; font-size: 1.0625rem; letter-spacing: -.015em; }
.panel-description { margin: 6px 0 0; color: var(--muted); font-size: 0.75rem; }
.panel-body { padding: var(--panel-padding); }
.toolbar { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border-bottom: 1px solid var(--border); flex-wrap: wrap; }
.search-input, .filter-select { min-height: 38px; padding: 8px 11px; color: #344054; background: #fff; border: 1px solid #d0d5dd; border-radius: 8px; outline: none; font-size: 0.75rem; }
.search-input { width: min(300px, 100%); }
.search-input:focus, .filter-select:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(99, 102, 241, .1); }
.reject-form { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.decision-input { width: min(220px, 100%); min-height: 36px; padding: 7px 10px; color: #344054; background: #fff; border: 1px solid #d0d5dd; border-radius: 8px; outline: none; font-size: 0.6875rem; }
.decision-input:focus { border-color: #f04438; box-shadow: 0 0 0 3px rgba(217, 45, 32, .1); }
.filter-count { margin-left: auto; color: var(--muted); font-size: 0.75rem; white-space: nowrap; }
.async-filter-region { position: relative; transition: opacity .15s ease; }
.async-filter-region.is-loading { min-height: 180px; pointer-events: none; }
.async-filter-region.is-loading::before { position:absolute; inset:0; z-index:50; background:rgba(255,255,255,.78); content:''; backdrop-filter:blur(1px); }
.async-filter-region.is-loading::after { position:absolute; top:50%; left:50%; z-index:51; width:28px; height:28px; margin:-14px 0 0 -14px; border:3px solid #e0e7ff; border-top-color:var(--primary); border-radius:50%; content:''; animation:async-filter-spin .7s linear infinite; }
@keyframes async-filter-spin { to { transform:rotate(360deg); } }
.toolbar .btn[hidden] { display: none; }
.table-wrap tr[hidden], .empty-state[hidden] { display: none; }
.badge { display: inline-flex; align-items: center; padding: 5px 9px; border-radius: 999px; font-size: 0.6875rem; font-weight: 600; text-transform: capitalize; white-space: nowrap; }
.badge-success { color: #067647; background: #ecfdf3; }
.badge-warning { color: #b54708; background: #fffaeb; }
.badge-danger { color: #b42318; background: #fef3f2; }
.badge-purple { color: #6941c6; background: #f4f3ff; }
.badge-neutral { color: #475467; background: #f2f4f7; }
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { padding: 12px 16px; color: var(--muted); background: #fcfcfd; border-bottom: 1px solid var(--border); font-size: 0.625rem; font-weight: 700; letter-spacing: .05em; text-align: left; text-transform: uppercase; white-space: nowrap; }
td { padding: 15px 16px; border-bottom: 1px solid #f2f4f7; font-size: 0.75rem; vertical-align: middle; }
tbody tr:last-child td { border-bottom: 0; }
tbody tr:hover { background: #fcfcfd; }
.cell-title { font-weight: 600; }
.cell-secondary { margin-top: 4px; color: var(--muted); font-size: 0.625rem; }
.amount { font-weight: 700; white-space: nowrap; }
.danger-text { color: var(--danger); }
.empty-state { padding: 48px 24px; color: var(--muted); text-align: center; }
.empty-state strong { display: block; margin-bottom: 6px; color: #344054; }
.avatar { display: grid; place-items: center; width: 36px; height: 36px; flex: 0 0 36px; color: #4338ca; background: #eef2ff; border-radius: 50%; font-size: 0.75rem; font-weight: 700; }
.identity { display: flex; align-items: center; gap: 10px; min-width: 150px; }
.identity .cell-title { display: block; line-height: 1.35; }
.email-cell { min-width: 190px; }
.email-value { display: block; color: #475467; font-size: 0.6875rem; line-height: 1.45; overflow-wrap: anywhere; }
.preview { width: 54px; height: 42px; object-fit: cover; background: #f2f4f7; border: 1px solid var(--border); border-radius: 8px; }
.detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 26px; }
.detail-item { padding: 15px 0; border-bottom: 1px solid #f2f4f7; }
.detail-label { color: var(--muted); font-size: 0.625rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
.detail-value { margin-top: 6px; color: #344054; font-size: 0.8125rem; font-weight: 500; word-break: break-word; }
.detail-layout { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(300px, .8fr); gap: 22px; align-items: start; }
.proof-image { display: block; width: 100%; max-height: 440px; object-fit: contain; background: #f9fafb; border: 1px solid var(--border); border-radius: 12px; }
.notice { padding: 14px; color: #912018; background: #fef3f2; border: 1px solid #fecdca; border-radius: 10px; font-size: 0.75rem; line-height: 1.5; }
.metric-layout { display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(300px, .7fr); gap: 22px; margin-bottom: 22px; }
.status-list { display: grid; gap: 15px; }
.status-row { display: grid; grid-template-columns: 82px 1fr 30px; gap: 12px; align-items: center; font-size: 0.6875rem; }
.status-track { height: 7px; overflow: hidden; background: #eaecf0; border-radius: 999px; }
.status-fill { height: 100%; background: var(--primary); border-radius: inherit; }
.quick-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
.quick-card { padding: 18px; color: var(--navy); background: #fff; border: 1px solid var(--border); border-radius: 14px; text-decoration: none; transition: .15s ease; }
.quick-card:hover { border-color: #c7d2fe; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(16, 24, 40, .06); }
.quick-icon { display: grid; place-items: center; width: 36px; height: 36px; color: var(--primary); background: var(--primary-soft); border-radius: 9px; }
.quick-icon svg { width: 18px; height: 18px; }
.quick-title { margin-top: 14px; font-size: 0.8125rem; font-weight: 700; }
.quick-note { margin-top: 5px; color: var(--muted); font-size: 0.6875rem; }

.confirmation-modal { z-index: 1060; padding-right: 0 !important; }
.confirmation-modal .modal-dialog { width: min(430px, calc(100% - 32px)); max-width: 430px; margin-right: auto; margin-left: auto; }
.confirmation-card { position:relative; padding:28px; overflow:hidden; background:#fff; border:1px solid rgba(208,213,221,.9); border-radius:20px; box-shadow:0 24px 64px rgba(16,24,40,.2); }
.confirmation-close { position:absolute; top:18px; right:18px; z-index:2; width:30px; height:30px; padding:8px; background-color:#f2f4f7; border-radius:50%; opacity:.72; }
.confirmation-close:hover { opacity:1; }
.confirmation-icon { display:grid; place-items:center; width:46px; height:46px; margin-bottom:18px; color:#4f46e5; background:#eef2ff; border:8px solid #f5f3ff; border-radius:50%; box-sizing:content-box; }
.confirmation-icon svg { width:22px; height:22px; }
.confirmation-copy { padding-right:22px; }
.confirmation-eyebrow { margin-bottom:6px; color:#4f46e5; font-size:0.625rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
.confirmation-card .modal-title { margin:0; color:#101828; font-size:1.25rem; font-weight:700; letter-spacing:-.025em; }
.confirmation-card [data-confirm-message] { margin:10px 0 0; color:#667085; font-size:0.8125rem; line-height:1.6; }
.confirmation-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:24px; }
.confirmation-actions .btn { min-height:42px; border-radius:10px; font-size:0.75rem; font-weight:700; }
.confirmation-cancel { color:#344054; background:#fff; border:1px solid #d0d5dd; }
.confirmation-cancel:hover { color:#101828; background:#f9fafb; border-color:#98a2b3; }
.confirmation-modal.is-danger .confirmation-icon { color:#d92d20; background:#fef3f2; border-color:#fff5f4; }
.confirmation-modal.is-danger .confirmation-eyebrow { color:#b42318; }
.confirmation-modal.is-danger .confirmation-proceed { background:#d92d20; border-color:#d92d20; }
.confirmation-modal.is-danger .confirmation-proceed:hover { background:#b42318; border-color:#b42318; }

@media (max-width: 1100px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .metric-layout, .detail-layout { grid-template-columns: 1fr; } }
@media (max-width: 800px) { .quick-grid { grid-template-columns: 1fr; } }
@media (max-width: 760px) {
    .sidebar { position: static; width: 100%; height: auto; padding: 12px 14px; }
    .sidebar-inner { height: auto; }
    .sidebar-brand { padding: 2px 2px 12px; border-bottom: 0; }
    .brand-copy small, .nav-label, .account-copy { display: none; }
    .sidebar-nav { display: flex; gap: 6px; padding: 0; overflow-x: auto; }
    .nav-section { display: contents; }
    .nav-section + .nav-section { margin-top: 0; }
    .nav-link { flex: 0 0 auto; margin: 0; }
    .sidebar-account { display: flex; padding: 10px 2px 0; margin-top: 10px; }
    .sidebar-account .account-avatar { width: 30px; height: 30px; }
    .sidebar-logout-form { margin-left: auto; }
    .sidebar-logout { display: inline-flex; width: auto; height: 32px; gap: 7px; padding: 0 11px; }
    .sidebar-logout span { position: static; width: auto; height: auto; overflow: visible; clip: auto; color: inherit; font-size: 0.6875rem; font-weight: 600; white-space: normal; }
    .main { margin-left: 0; padding: 22px 16px; }
    .topbar { align-items: flex-start; flex-direction: column; }
    .top-actions { width: 100%; align-items: stretch; flex-direction: row; flex-wrap: wrap; }
    .top-actions > a, .top-actions > form { min-width: 0; flex: 1 1 150px; }
    .top-actions > form .button { width: 100%; }
    h1 { font-size: 1.5625rem; }
    .detail-grid { grid-template-columns: 1fr; }
}
@media (max-width: 470px) { .stats-grid { grid-template-columns: 1fr; } .top-actions > a, .top-actions > form { flex-basis: 100%; } .panel-header { align-items: flex-start; flex-direction: column; } .confirmation-card { padding:24px 20px 20px; } .confirmation-actions { grid-template-columns:1fr; } }

@if($reduceAdminMotion)
:root { --app-reduce-motion:1; }
*, *::before, *::after { scroll-behavior:auto !important; transition-duration:.01ms !important; animation-duration:.01ms !important; animation-iteration-count:1 !important; }
@endif

@include('partials.motion-styles')
