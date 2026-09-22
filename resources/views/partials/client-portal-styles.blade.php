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
body { margin: 0; color: var(--navy); background: var(--canvas); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
button, input, select, textarea { font: inherit; }
.sidebar { position: fixed; inset: 0 auto 0 0; width: 248px; padding: 28px 20px; color: #fff; background: #111827; z-index: 10; }
.brand { display: flex; align-items: center; gap: 12px; margin: 0 8px 36px; font-size: 19px; font-weight: 700; }
.brand-mark { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 11px; background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 8px 20px rgba(99, 102, 241, .3); }
.nav-label { margin: 0 12px 10px; color: #667085; font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
.nav-link { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; padding: 11px 12px; color: #98a2b3; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 500; transition: .18s ease; }
.nav-link:hover, .nav-link.active { color: #fff; background: #1f2937; }
.nav-link svg { width: 19px; height: 19px; }
.main { min-height: 100vh; margin-left: 248px; padding: 32px; }
.page-shell { max-width: 1320px; margin: 0 auto; }
.topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
.topbar > *, .panel-header > *, .stat-card { min-width: 0; }
.eyebrow { margin-bottom: 6px; color: var(--primary); font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
h1 { margin: 0; font-size: 30px; letter-spacing: -.03em; }
.subtitle { margin: 8px 0 0; color: var(--muted); font-size: 14px; }
.top-actions { display: flex; align-items: center; gap: 10px; }
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
.stat-card, .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 1px 3px rgba(16, 24, 40, .04); }
.stat-card { padding: 20px; }
.stat-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.stat-label { color: var(--muted); font-size: 13px; font-weight: 500; }
.stat-icon { display: grid; place-items: center; width: 36px; height: 36px; color: var(--primary); background: #eef2ff; border-radius: 10px; }
.stat-icon svg { width: 18px; height: 18px; }
.stat-value { margin-top: 15px; overflow-wrap: anywhere; font-size: 24px; font-weight: 700; letter-spacing: -.03em; }
.stat-note { margin-top: 5px; color: #98a2b3; font-size: 12px; }
.panel { overflow: hidden; }
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
.amount { font-weight: 700; white-space: nowrap; }
.empty-state { padding: 48px 24px; color: var(--muted); text-align: center; }
.empty-state strong { display: block; margin-bottom: 7px; color: #344054; }
.verification-banner { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 22px; padding: 16px 18px; color: #854a0e; background: #fffaeb; border: 1px solid #fedf89; border-radius: 13px; font-size: 12px; }
.verification-banner strong { display: block; margin-bottom: 4px; color: #7a2e0e; font-size: 13px; }
.verification-banner a { flex: 0 0 auto; padding: 9px 13px; color: #fff; background: #dc6803; border-radius: 8px; font-weight: 600; text-decoration: none; }

@media (max-width: 1080px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 760px) {
    .sidebar { position: static; width: 100%; height: auto; padding: 16px; }
    .brand { margin: 0 0 14px; }
    .nav-label { display: none; }
    .sidebar nav { display: flex; gap: 6px; overflow-x: auto; }
    .nav-link { flex: 0 0 auto; margin: 0; }
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
}
