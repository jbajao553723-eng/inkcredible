:root {
    --navy: #101828;
    --muted: #667085;
    --border: #e4e7ec;
    --surface: #ffffff;
    --canvas: #f7f8fa;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
}

* { box-sizing: border-box; }
body { margin: 0; color: var(--navy); background: radial-gradient(circle at 96% 0, rgba(99,102,241,.07), transparent 25%), var(--canvas); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
button, input, select, textarea { font: inherit; }
button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible { outline: 3px solid rgba(99,102,241,.32); outline-offset: 2px; }
.skip-link { position:fixed; top:10px; left:10px; z-index:100; padding:9px 12px; color:#fff; background:#4338ca; border-radius:8px; text-decoration:none; transform:translateY(-150%); transition:transform .15s ease; }
.skip-link:focus { transform:translateY(0); }
.sidebar { position: fixed; inset: 0 auto 0 0; width: 268px; padding: 18px 16px; color: #fff; background: linear-gradient(180deg, #111827 0%, #0b1220 100%); border-right: 1px solid rgba(255,255,255,.06); z-index: 10; }
.sidebar-inner { display: flex; flex-direction: column; height: 100%; min-height: 0; }
.sidebar-brand { display: flex; align-items: center; gap: 12px; padding: 10px 10px 22px; color: #fff; border-bottom: 1px solid rgba(255,255,255,.07); text-decoration: none; }
.brand-copy, .account-copy { display: flex; min-width: 0; flex-direction: column; }
.brand-copy strong { font-size: 18px; letter-spacing: -.02em; }
.brand-copy small { margin-top: 2px; color: #98a2b3; font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
.brand-mark { display: block; width: 36px; height: 36px; flex: 0 0 36px; object-fit: cover; border: 1px solid rgba(255,255,255,.14); border-radius: 11px; background: #fff; box-shadow: 0 8px 20px rgba(239,68,68,.24); }
.sidebar-nav { flex: 1; min-height: 0; padding: 18px 0; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #344054 transparent; }
.nav-section + .nav-section { margin-top: 20px; }
.nav-label { margin: 0 12px 8px; color: #667085; font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
.nav-link { position: relative; display: flex; align-items: center; gap: 12px; margin-bottom: 4px; padding: 10px 12px; color: #98a2b3; border: 1px solid transparent; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 500; transition: .18s ease; }
.nav-link:hover { color: #e4e7ec; background: rgba(255,255,255,.045); }
.nav-link.active { color: #fff; background: linear-gradient(90deg, rgba(99,102,241,.24), rgba(99,102,241,.10)); border-color: rgba(129,140,248,.18); box-shadow: inset 3px 0 #818cf8; }
.nav-link svg { width: 19px; height: 19px; }
.sidebar-account { display: grid; grid-template-columns: 34px minmax(0,1fr) auto; align-items: center; gap: 10px; padding: 14px 6px 4px; border-top: 1px solid rgba(255,255,255,.07); }
.account-avatar { display: grid; place-items: center; width: 34px; height: 34px; color: #fff; background: #344054; border: 1px solid rgba(255,255,255,.12); border-radius: 10px; font-size: 11px; font-weight: 700; }
.account-copy strong { overflow: hidden; color: #f2f4f7; font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
.account-copy small { margin-top: 3px; overflow: hidden; color: #667085; font-size: 9px; text-overflow: ellipsis; white-space: nowrap; }
.sidebar-logout-form { margin: 0; }
.sidebar-logout { display: grid; place-items: center; width: 34px; height: 34px; padding: 0; color: #98a2b3; background: rgba(255,255,255,.035); border: 1px solid rgba(255,255,255,.08); border-radius: 9px; cursor: pointer; transition: .18s ease; }
.sidebar-logout:hover { color: #fff; background: rgba(217,45,32,.16); border-color: rgba(248,113,113,.24); }
.sidebar-logout svg { width: 17px; height: 17px; }
.sidebar-logout span { position: absolute; width: 1px; height: 1px; padding: 0; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
.main { min-width: 0; min-height: 100vh; margin-left: 268px; padding: 32px; overflow-x: clip; }
.page-shell { width: 100%; max-width: 1320px; min-width: 0; margin: 0 auto; }
.topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
.topbar > *, .panel-header > *, .stat-card { min-width: 0; }
.eyebrow { margin-bottom: 6px; color: var(--primary); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
h1 { margin: 0; font-size: 30px; letter-spacing: -.03em; }
.subtitle { margin: 8px 0 0; color: var(--muted); font-size: 14px; }
.top-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.top-actions form { margin: 0; }
.button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 10px 16px; border: 1px solid var(--border); border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer; transition: .15s ease; }
.button svg { width: 17px; height: 17px; }
.button-secondary { color: #344054; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
.button-secondary:hover { background: #f9fafb; }
.button-primary { color: #fff; background: var(--primary); border-color: var(--primary); box-shadow: 0 8px 20px rgba(79, 70, 229, .16); }
.button-primary:hover { color: #fff; background: var(--primary-dark); }
.alert { margin-bottom: 20px; padding: 14px 16px; border: 1px solid; border-radius: 12px; font-size: 14px; }
.alert-success { color: #05603a; background: #ecfdf3; border-color: #abefc6; }
.alert-error { color: #912018; background: #fef3f2; border-color: #fecdca; }
.alert ul { margin: 0; padding-left: 20px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
.stat-card, .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 2px 8px rgba(16, 24, 40, .045); }
.stat-card { padding: 20px; transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
.stat-card:hover { border-color: #c7d2fe; box-shadow: 0 10px 24px rgba(16,24,40,.07); transform: translateY(-2px); }
.stat-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.stat-label { color: var(--muted); font-size: 13px; font-weight: 500; }
.stat-icon { display: grid; place-items: center; width: 36px; height: 36px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.stat-icon svg { width: 18px; height: 18px; }
.stat-value { margin-top: 15px; overflow-wrap: anywhere; font-size: 24px; font-weight: 700; letter-spacing: -.03em; }
.stat-note { margin-top: 5px; color: #98a2b3; font-size: 12px; }
.panel { min-width: 0; overflow: hidden; }
.panel-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 22px 24px; border-bottom: 1px solid var(--border); }
.panel-title { margin: 0; font-size: 18px; letter-spacing: -.015em; }
.panel-description { margin: 7px 0 0; color: var(--muted); font-size: 13px; }
.panel-body { padding: 24px; }
.badge { display: inline-flex; align-items: center; padding: 5px 9px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: capitalize; white-space: nowrap; }
.badge-success { color: #067647; background: #ecfdf3; }
.badge-warning { color: #b54708; background: #fffaeb; }
.badge-danger { color: #b42318; background: #fef3f2; }
.badge-neutral { color: #475467; background: #f2f4f7; }
.badge-purple { color: #6941c6; background: #f4f3ff; }
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
th { padding: 12px 18px; color: var(--muted); background: #fcfcfd; border-bottom: 1px solid var(--border); font-size: 11px; font-weight: 600; letter-spacing: .04em; text-align: left; text-transform: uppercase; white-space: nowrap; }
td { padding: 16px 18px; border-bottom: 1px solid #f2f4f7; font-size: 13px; vertical-align: middle; }
tbody tr:last-child td { border-bottom: 0; }
tbody tr:hover { background: #fcfcfd; }
.cell-title { font-weight: 600; }
.cell-secondary { margin-top: 4px; color: var(--muted); font-size: 11px; }
.rejection-reason { max-width: 230px; margin-top: 6px; color: #b42318; font-size: 11px; line-height: 1.4; white-space: normal; }
.amount { font-weight: 700; white-space: nowrap; }
.empty-state { padding: 48px 24px; color: var(--muted); text-align: center; }
.empty-state strong { display: block; margin-bottom: 7px; color: #344054; }
.verification-banner { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 22px; padding: 16px 18px; color: #854a0e; background: #fffaeb; border: 1px solid #fedf89; border-radius: 13px; font-size: 12px; }
.verification-banner strong { display: block; margin-bottom: 4px; color: #7a2e0e; font-size: 13px; }
.verification-banner a { flex: 0 0 auto; padding: 9px 13px; color: #fff; background: #dc6803; border-radius: 8px; font-weight: 600; text-decoration: none; }

.confirmation-dialog { width:min(430px, calc(100% - 32px)); max-width:430px; padding:0; overflow:visible; background:transparent; border:0; }
.confirmation-dialog::backdrop { background:rgba(52,64,84,.32); backdrop-filter:blur(2px); }
.confirmation-dialog-card { position:relative; padding:28px; color:#101828; background:#fff; border:1px solid rgba(208,213,221,.9); border-radius:20px; box-shadow:0 24px 64px rgba(16,24,40,.2); }
.confirmation-dialog-close { position:absolute; top:16px; right:16px; display:grid; place-items:center; width:32px; height:32px; padding:0; color:#667085; background:#f2f4f7; border:0; border-radius:50%; font-size:22px; line-height:1; cursor:pointer; }
.confirmation-dialog-icon { display:grid; place-items:center; width:46px; height:46px; margin-bottom:18px; color:#4f46e5; background:#eef2ff; border:8px solid #f5f3ff; border-radius:50%; box-sizing:content-box; }
.confirmation-dialog-icon svg { width:22px; height:22px; }
.confirmation-dialog-eyebrow { margin-bottom:6px; color:#4f46e5; font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
.confirmation-dialog h2 { margin:0; font-size:20px; letter-spacing:-.025em; }
.confirmation-dialog p { margin:10px 0 0; color:#667085; font-size:13px; line-height:1.6; }
.confirmation-dialog-actions { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:24px; }
.confirmation-dialog-actions .button { width:100%; }

@media (max-width: 1080px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

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
    .sidebar-logout span { position: static; width: auto; height: auto; overflow: visible; clip: auto; color: inherit; font-size: 11px; font-weight: 600; white-space: normal; }
    .main { margin-left: 0; padding: 22px 16px; }
    .topbar { align-items: flex-start; flex-direction: column; }
    .top-actions { width: 100%; align-items: stretch; flex-direction: row; flex-wrap: wrap; }
    .top-actions > a, .top-actions > form { min-width: 0; flex: 1 1 150px; }
    .top-actions > form .button { width: 100%; }
    h1 { font-size: 25px; }
    .stats-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stat-card { padding: 16px; }
    .stat-value { font-size: 19px; }
}

@media (max-width: 460px) {
    .stats-grid { grid-template-columns: 1fr; }
    .subtitle { max-width: 235px; }
    .top-actions > a, .top-actions > form { flex-basis: 100%; }
    .verification-banner { align-items: flex-start; flex-direction: column; }
    .verification-banner a { width: 100%; text-align: center; }
    .panel-header { align-items: flex-start; flex-direction: column; }
    .panel-header, .panel-body { padding-left: 18px; padding-right: 18px; }
    .confirmation-dialog-card { padding:24px 20px 20px; }
    .confirmation-dialog-actions { grid-template-columns:1fr; }
}

@include('partials.motion-styles')

@if((bool) data_get(auth()->user()?->ui_preferences, 'reduce_motion', false))
*, *::before, *::after { scroll-behavior:auto !important; transition-duration:.01ms !important; transition-delay:0ms !important; animation-duration:.01ms !important; animation-iteration-count:1 !important; }
@endif
