<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports | Inkcredible Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite('resources/js/admin.js')
<style>
@include('partials.admin-styles')
.report-button { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; }
.report-button:hover { color:#3730a3; background:#e0e7ff; border-color:#a5b4fc; }
.report-button svg { width:15px; height:15px; }
.report-summary { min-width:180px; }
.reports-topbar { padding:4px 0; }
.reports-topbar .subtitle { max-width:680px; line-height:1.55; }
.report-action-menu { position:relative; }
.report-action-menu > summary { list-style:none; }
.report-action-menu > summary::-webkit-details-marker { display:none; }
.report-action-menu > summary svg { transition:.2s ease; }
.report-action-menu[open] > summary svg { transform:rotate(180deg); }
.report-action-dropdown { position:absolute; top:calc(100% + 8px); right:0; z-index:30; width:190px; padding:6px; background:#fff; border:1px solid #e4e7ec; border-radius:11px; box-shadow:0 18px 40px rgba(16,24,40,.16); }
.report-action-dropdown a,.report-action-dropdown button { display:flex; align-items:center; gap:9px; width:100%; padding:9px 10px; color:#344054; background:transparent; border:0; border-radius:8px; font-size:11px; font-weight:600; text-align:left; text-decoration:none; cursor:pointer; }
.report-action-dropdown a:hover,.report-action-dropdown button:hover { color:#101828; background:#f8fafc; }
.report-action-dropdown svg { width:15px; height:15px; color:#667085; }
.report-overview { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:22px; }
.report-overview-card { position:relative; min-width:0; min-height:108px; padding:18px 64px 18px 18px; background:#fff; border:1px solid #e4e7ec; border-radius:14px; box-shadow:0 1px 2px rgba(16,24,40,.03); }
.report-overview-icon { position:absolute; top:16px; right:16px; display:grid; place-items:center; width:36px; height:36px; color:var(--card-accent,#4f46e5); background:var(--card-glow,#eef2ff); border-radius:10px; }
.report-overview-icon svg { width:17px; height:17px; }
.report-overview-card strong { display:block; margin-top:3px; color:#101828; font-size:24px; letter-spacing:-.035em; }
.report-overview-card span { display:block; margin-top:3px; color:#667085; font-size:10px; }
.analytics-shell { border-color:#d9d6fe; background:linear-gradient(180deg,#ffffff 0%,#fbfaff 100%); }
.analytics-header { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; padding:24px; border-bottom:1px solid #e9e7ff; }
.analytics-header h2 { margin:0; color:#101828; font-size:20px; letter-spacing:-.025em; }
.analytics-header p { max-width:680px; margin:7px 0 0; color:var(--muted); font-size:12px; line-height:1.55; }
.analytics-freshness { flex:0 0 auto; display:flex; align-items:center; gap:7px; padding:7px 10px; color:#475467; background:#fff; border:1px solid #e4e7ec; border-radius:999px; font-size:10px; font-weight:600; white-space:nowrap; }
.analytics-freshness::before { width:7px; height:7px; background:#12b76a; border-radius:50%; box-shadow:0 0 0 3px #d1fadf; content:''; }
.analytics-tabs { display:flex; gap:6px; padding:10px 12px; overflow-x:auto; background:#f8fafc; border-bottom:1px solid #e4e7ec; scrollbar-width:none; }
.analytics-tabs::-webkit-scrollbar { display:none; }
.analytics-tab { display:inline-flex; align-items:center; gap:7px; min-height:36px; padding:8px 13px; color:#667085; background:transparent; border:1px solid transparent; border-radius:9px; font-size:11px; font-weight:700; white-space:nowrap; cursor:pointer; transition:.15s ease; }
.analytics-tab:hover { color:#344054; background:#fff; border-color:#e4e7ec; }
.analytics-tab[aria-selected="true"] { color:#4338ca; background:#fff; border-color:#c7d2fe; box-shadow:0 1px 2px rgba(16,24,40,.05); }
.analytics-tab svg { width:15px; height:15px; }
.analytics-panel[hidden] { display:none; }
.analytics-panel { animation:analytics-panel-in .18s ease; }
.analytics-panel > .analytics-wide:first-child,.analytics-panel > .analytics-section-head:first-child { margin-top:0; }
@keyframes analytics-panel-in { from { opacity:0; transform:translateY(3px); } to { opacity:1; transform:none; } }
.analytics-body { padding:20px 22px 22px; }
.analytics-kpis { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
.analytics-kpi { min-width:0; padding:16px; background:#fff; border:1px solid #e4e7ec; border-radius:13px; box-shadow:0 1px 2px rgba(16,24,40,.03); }
.analytics-kpi-label { display:flex; align-items:center; gap:7px; color:#667085; font-size:11px; font-weight:600; }
.analytics-kpi-dot { width:8px; height:8px; border-radius:50%; }
.analytics-kpi strong { display:block; margin-top:9px; color:#101828; font-size:20px; letter-spacing:-.03em; overflow-wrap:anywhere; }
.analytics-kpi small { display:block; margin-top:5px; color:#98a2b3; font-size:10px; line-height:1.4; }
.analytics-grid { display:grid; grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr); gap:14px; margin-top:14px; }
.analytics-card { min-width:0; padding:18px; background:#fff; border:1px solid #e4e7ec; border-radius:14px; }
.analytics-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px; }
.analytics-card-head h3 { margin:0; color:#101828; font-size:14px; }
.analytics-card-head p { margin:4px 0 0; color:#98a2b3; font-size:10px; line-height:1.45; }
.profit-visual { display:grid; grid-template-columns:150px minmax(0,1fr); gap:20px; align-items:center; }
.profit-ring { --interest-share:0%; --remaining-color:#f79009; position:relative; display:grid; place-items:center; width:142px; aspect-ratio:1; margin:auto; background:conic-gradient(#12b76a 0 var(--interest-share),var(--remaining-color) var(--interest-share) 100%); border-radius:50%; }
.profit-ring::before { position:absolute; width:102px; aspect-ratio:1; background:#fff; border-radius:50%; box-shadow:inset 0 0 0 1px #f2f4f7; content:''; }
.profit-ring-center { position:relative; z-index:1; text-align:center; }
.profit-ring-center strong { display:block; color:#101828; font-size:17px; letter-spacing:-.03em; }
.profit-ring-center span { display:block; margin-top:3px; color:#98a2b3; font-size:9px; }
.analytics-legend { display:grid; gap:12px; }
.analytics-legend-row { display:grid; grid-template-columns:auto 1fr; gap:9px; align-items:start; }
.analytics-legend-swatch { width:9px; height:9px; margin-top:3px; border-radius:3px; }
.analytics-legend-row span { display:block; color:#667085; font-size:10px; }
.analytics-legend-row strong { display:block; margin-top:3px; color:#344054; font-size:12px; }
.health-list { display:grid; gap:16px; }
.health-copy { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:7px; }
.health-copy span { color:#475467; font-size:11px; font-weight:600; }
.health-copy strong { color:#101828; font-size:11px; }
.health-track { height:7px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.health-fill { display:block; height:100%; min-width:0; border-radius:inherit; }
.health-note { margin-top:6px; color:#98a2b3; font-size:9px; }
.analytics-wide { margin-top:14px; }
.trend-legend { display:flex; align-items:center; gap:12px; color:#667085; font-size:9px; }
.trend-legend span { display:flex; align-items:center; gap:5px; }
.trend-legend i { width:8px; height:8px; border-radius:2px; }
.trend-list { display:grid; gap:11px; }
.trend-row { display:grid; grid-template-columns:42px minmax(120px,1fr) 210px; gap:12px; align-items:center; }
.trend-month { color:#475467; font-size:10px; font-weight:700; }
.trend-bars { display:grid; gap:4px; }
.trend-track { height:6px; overflow:hidden; background:#f2f4f7; border-radius:999px; }
.trend-fill { display:block; height:100%; border-radius:inherit; }
.trend-values { display:flex; justify-content:flex-end; gap:13px; color:#667085; font-size:9px; white-space:nowrap; }
.trend-values strong { color:#344054; font-size:10px; }
.analytics-detail-grid { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:14px; margin-top:14px; }
.analytics-disclosure { min-width:0; padding:0; background:#fff; border:1px solid #e4e7ec; border-radius:14px; overflow:hidden; }
.analytics-disclosure > summary { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:17px 18px; cursor:pointer; list-style:none; transition:.15s ease; }
.analytics-disclosure > summary::-webkit-details-marker { display:none; }
.analytics-disclosure > summary:hover { background:#fcfcfd; }
.analytics-disclosure > summary::after { flex:0 0 auto; width:8px; height:8px; margin-right:3px; border-right:2px solid #667085; border-bottom:2px solid #667085; content:''; transform:rotate(45deg); transition:.2s ease; }
.analytics-disclosure[open] > summary { border-bottom:1px solid #f2f4f7; }
.analytics-disclosure[open] > summary::after { transform:rotate(225deg); }
.analytics-disclosure-copy { min-width:0; }
.analytics-disclosure-copy h3 { margin:0; color:#101828; font-size:13px; }
.analytics-disclosure-copy p { margin:4px 0 0; color:#98a2b3; font-size:9px; line-height:1.45; }
.analytics-disclosure-body { padding:14px; }
.rank-list { display:grid; gap:11px; }
.rank-row { padding:11px 12px; background:#fcfcfd; border:1px solid #f2f4f7; border-radius:10px; }
.rank-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.rank-name { color:#344054; font-size:11px; font-weight:700; }
.rank-meta { margin-top:4px; color:#98a2b3; font-size:9px; }
.rank-value { color:#101828; font-size:11px; font-weight:700; text-align:right; white-space:nowrap; }
.rank-track { height:5px; margin-top:9px; overflow:hidden; background:#eaecf0; border-radius:999px; }
.rank-track span { display:block; height:100%; background:linear-gradient(90deg,#4f46e5,#818cf8); border-radius:inherit; }
.analytics-empty { padding:22px; color:#98a2b3; background:#fcfcfd; border:1px dashed #d0d5dd; border-radius:10px; font-size:11px; text-align:center; }
.analytics-section-head { display:flex; align-items:flex-end; justify-content:space-between; gap:14px; margin:18px 2px 10px; }
.analytics-section-head h3 { margin:0; color:#101828; font-size:13px; }
.analytics-section-head p { margin:3px 0 0; color:#98a2b3; font-size:9px; }
.pipeline-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; }
.pipeline-item { position:relative; overflow:hidden; padding:14px; background:#fff; border:1px solid #e4e7ec; border-radius:11px; }
.pipeline-item::before { position:absolute; inset:0 auto 0 0; width:3px; background:var(--pipeline-color,#98a2b3); content:''; }
.pipeline-item span { display:block; color:#667085; font-size:10px; font-weight:600; }
.pipeline-item strong { display:block; margin-top:6px; color:#101828; font-size:18px; }
.pipeline-item small { display:block; margin-top:4px; color:#98a2b3; font-size:9px; }
.control-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:10px; margin-top:14px; }
.control-item { display:flex; align-items:center; gap:10px; min-width:0; padding:12px; background:#fff; border:1px solid #e4e7ec; border-radius:11px; }
.control-status { flex:0 0 auto; display:grid; place-items:center; width:28px; height:28px; color:#027a48; background:#ecfdf3; border-radius:9px; font-size:12px; font-weight:800; }
.control-item.has-issues .control-status { color:#b54708; background:#fffaeb; }
.control-item strong { display:block; color:#344054; font-size:10px; line-height:1.3; }
.control-item span { display:block; margin-top:2px; color:#98a2b3; font-size:9px; }
.client-directory { scroll-margin-top:18px; }
.client-directory .panel-header { background:linear-gradient(135deg,#fff 0%,#f8fafc 100%); }
.directory-heading { display:flex; align-items:center; gap:12px; }
.directory-heading-icon { display:grid; place-items:center; width:40px; height:40px; color:#4f46e5; background:#eef2ff; border-radius:11px; }
.directory-heading-icon svg { width:19px; height:19px; }
.directory-toolbar { background:#fcfcfd; }
.directory-toolbar .search-input { flex:1 1 260px; max-width:420px; }
.directory-toolbar .filter-select { min-width:160px; }
.client-directory tbody tr { transition:.15s ease; }
.client-directory tbody tr:hover { background:#f8faff; }
.report-actions-cell { text-align:right; }
.report-guide-modal .modal-dialog { width:min(620px,calc(100% - 30px)); max-width:620px; }
.report-guide-card { overflow:hidden; background:#fff; border:0; border-radius:20px; box-shadow:0 28px 80px rgba(16,24,40,.24); }
.report-guide-head { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:24px; color:#fff; background:linear-gradient(135deg,#312e81,#4f46e5); }
.report-guide-head h2 { margin:4px 0 0; font-size:21px; }
.report-guide-head p { margin:6px 0 0; color:#c7d2fe; font-size:11px; line-height:1.55; }
.report-guide-head .btn-close { margin:0; padding:9px; background-color:#fff; border-radius:50%; opacity:.85; }
.report-guide-body { padding:22px 24px 24px; }
.report-guide-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:11px; }
.report-guide-item { padding:14px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:12px; }
.report-guide-item strong { display:block; color:#344054; font-size:11px; }
.report-guide-item span { display:block; margin-top:5px; color:#667085; font-size:10px; line-height:1.5; }
.business-report { border-color:#c7d2fe; background:linear-gradient(135deg,#ffffff 0%,#f5f3ff 100%); }
.business-report-body { display:grid; grid-template-columns:minmax(260px,.8fr) minmax(0,1.2fr); gap:24px; align-items:center; padding:22px; }
.business-report-copy h3 { margin:0; font-size:18px; }
.business-report-copy p { margin:8px 0 16px; max-width:600px; color:var(--muted); font-size:12px; line-height:1.6; }
.business-metrics { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
.business-metric { padding:13px 14px; background:rgba(255,255,255,.82); border:1px solid #e0e7ff; border-radius:11px; }
.business-metric span { display:block; color:var(--muted); font-size:10px; }
.business-metric strong { display:block; margin-top:5px; color:#312e81; font-size:16px; }
@media(max-width:1100px){.report-overview{grid-template-columns:repeat(2,minmax(0,1fr))}.analytics-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.analytics-grid,.analytics-detail-grid{grid-template-columns:1fr}.pipeline-grid,.control-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:850px){.business-report-body{grid-template-columns:1fr}.trend-row{grid-template-columns:38px minmax(120px,1fr)}.trend-values{grid-column:2; justify-content:flex-start}}
@media(max-width:620px){.analytics-header{flex-direction:column}.analytics-body{padding:16px}.analytics-kpis{grid-template-columns:1fr 1fr}.profit-visual{grid-template-columns:1fr}.pipeline-grid,.control-grid{grid-template-columns:1fr}.analytics-card-head{flex-direction:column}.report-guide-grid{grid-template-columns:1fr}}
@media(max-width:480px){.report-overview,.business-metrics,.analytics-kpis{grid-template-columns:1fr}.trend-row{grid-template-columns:34px minmax(100px,1fr)}.trend-values{gap:8px; flex-wrap:wrap}}
</style>
</head>
<body>
@include('partials.admin-sidebar', ['active' => 'reports'])
<main class="main" id="main-content" tabindex="-1"><div class="page-shell">
    <header class="topbar reports-topbar">
        <div><div class="eyebrow">Management intelligence</div><h1>Reports center</h1><p class="subtitle">A clear operational view of profitability, collections, portfolio risk, and individual client records.</p></div>
        <div class="top-actions">
            <button class="button button-secondary" type="button" data-report-guide><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 17h.01M9.1 9a3 3 0 115.83 1c0 2-2.93 2-2.93 4M12 22a10 10 0 100-20 10 10 0 000 20z"/></svg>Report guide</button>
            <a class="button button-primary" href="{{ route('admin.reports.business.download') }}" download data-no-transition><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>Download business PDF</a>
        </div>
    </header>

    <section class="report-overview" aria-label="Report summary">
        <article class="report-overview-card"><div class="report-overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm13 10v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div><strong>{{ $stats['clients'] }}</strong><span>Client reports available</span></article>
        <article class="report-overview-card" style="--card-accent:#079455;--card-glow:#ecfdf3"><div class="report-overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><strong>{{ $stats['verified'] }}</strong><span>Identity-verified clients</span></article>
        <article class="report-overview-card" style="--card-accent:#7f56d9;--card-glow:#f4f3ff"><div class="report-overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/></svg></div><strong>{{ $stats['applications'] }}</strong><span>Loan applications analyzed</span></article>
        <article class="report-overview-card" style="--card-accent:#f79009;--card-glow:#fffaeb"><div class="report-overview-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h2m4 0h2M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div><strong>{{ $stats['active_borrowers'] }}</strong><span>Borrowers with active balances</span></article>
    </section>

    @php
        $businessSummary = $business['summary'];
        $projectedProfit = (float) $businessSummary['contract_interest'] + (float) $businessSummary['penalties'];
        $projectedRevenue = (float) $businessSummary['scheduled_payable'] + (float) $businessSummary['penalties'];
        $profitMargin = $projectedRevenue > 0 ? ($projectedProfit / $projectedRevenue) * 100 : 0;
        $interestShare = $projectedProfit > 0 ? ((float) $businessSummary['contract_interest'] / $projectedProfit) * 100 : 0;
        $trendMaximum = max(1, collect($business['monthlyTrend'])->max(fn ($month) => max((float) $month['principal_released'], (float) $month['collections'])) ?? 0);
    @endphp

    <section class="panel analytics-shell" id="business-analytics" aria-labelledby="business-analytics-title">
        <div class="analytics-header">
            <div>
                <div class="eyebrow">Decision dashboard</div>
                <h2 id="business-analytics-title">Business analytics</h2>
                <p>Monitor projected loan profit, portfolio health, cash movement, product performance, payment channels, and operating controls from one live view.</p>
            </div>
            <span class="analytics-freshness">Updated {{ $business['preparedAt']->copy()->timezone('Asia/Manila')->format('M d, Y · h:i A') }} PHT</span>
        </div>
        <div class="analytics-tabs" role="tablist" aria-label="Business analytics sections">
            <button class="analytics-tab" id="analytics-overview-tab" type="button" role="tab" aria-selected="true" aria-controls="analytics-overview-panel" data-analytics-tab="overview"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>Overview</button>
            <button class="analytics-tab" id="analytics-performance-tab" type="button" role="tab" aria-selected="false" aria-controls="analytics-performance-panel" data-analytics-tab="performance" tabindex="-1"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg>Performance</button>
            <button class="analytics-tab" id="analytics-operations-tab" type="button" role="tab" aria-selected="false" aria-controls="analytics-operations-panel" data-analytics-tab="operations" tabindex="-1"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3.5 19 6v5c0 4.5-2.8 7.8-7 9.5C7.8 18.8 5 15.5 5 11V6zM9 11.5l2 2 4-4"/></svg>Risk &amp; operations</button>
        </div>
        <div class="analytics-body">
            <div class="analytics-panel" id="analytics-overview-panel" role="tabpanel" aria-labelledby="analytics-overview-tab" data-analytics-panel="overview">
            <div class="analytics-kpis" aria-label="Business performance indicators">
                <article class="analytics-kpi">
                    <div class="analytics-kpi-label"><i class="analytics-kpi-dot" style="background:#12b76a"></i>Projected gross profit</div>
                    <strong>PHP {{ number_format($projectedProfit, 2) }}</strong>
                    <small>{{ number_format($profitMargin, 1) }}% of scheduled revenue, before operating costs and defaults</small>
                </article>
                <article class="analytics-kpi">
                    <div class="analytics-kpi-label"><i class="analytics-kpi-dot" style="background:#4f46e5"></i>Approved collections</div>
                    <strong>PHP {{ number_format($businessSummary['collections'], 2) }}</strong>
                    <small>{{ number_format($businessSummary['collection_rate'], 1) }}% collection rate across originated loans</small>
                </article>
                <article class="analytics-kpi">
                    <div class="analytics-kpi-label"><i class="analytics-kpi-dot" style="background:#f79009"></i>Outstanding portfolio</div>
                    <strong>PHP {{ number_format($businessSummary['outstanding'], 2) }}</strong>
                    <small>{{ $businessSummary['active_loans'] }} active {{ Str::plural('loan', $businessSummary['active_loans']) }} still generating receivables</small>
                </article>
                <article class="analytics-kpi">
                    <div class="analytics-kpi-label"><i class="analytics-kpi-dot" style="background:#f04438"></i>Overdue exposure</div>
                    <strong>PHP {{ number_format($businessSummary['overdue_amount'], 2) }}</strong>
                    <small>{{ $businessSummary['overdue_loans'] }} overdue {{ Str::plural('loan', $businessSummary['overdue_loans']) }} · {{ number_format($businessSummary['overdue_share'], 1) }}% portfolio share</small>
                </article>
            </div>

            <div class="analytics-grid">
                <article class="analytics-card">
                    <div class="analytics-card-head">
                        <div><h3>Profit composition</h3><p>Projected earnings from contracted interest and recorded penalties.</p></div>
                        <span class="badge badge-success">{{ number_format($profitMargin, 1) }}% margin</span>
                    </div>
                    <div class="profit-visual">
                        <div class="profit-ring" style="--interest-share:{{ number_format($interestShare, 2, '.', '') }}%;--remaining-color:{{ $projectedProfit > 0 ? '#f79009' : '#eaecf0' }}" role="img" aria-label="{{ number_format($interestShare, 1) }} percent of projected profit is contract interest">
                            <div class="profit-ring-center"><strong>PHP {{ number_format($projectedProfit, 0) }}</strong><span>Projected profit</span></div>
                        </div>
                        <div class="analytics-legend">
                            <div class="analytics-legend-row"><i class="analytics-legend-swatch" style="background:#12b76a"></i><div><span>Contract interest</span><strong>PHP {{ number_format($businessSummary['contract_interest'], 2) }}</strong></div></div>
                            <div class="analytics-legend-row"><i class="analytics-legend-swatch" style="background:#f79009"></i><div><span>Recorded penalties</span><strong>PHP {{ number_format($businessSummary['penalties'], 2) }}</strong></div></div>
                            <div class="analytics-legend-row"><i class="analytics-legend-swatch" style="background:#e4e7ec"></i><div><span>Principal released</span><strong>PHP {{ number_format($businessSummary['principal_released'], 2) }}</strong></div></div>
                        </div>
                    </div>
                </article>

                <article class="analytics-card">
                    <div class="analytics-card-head"><div><h3>Portfolio health</h3><p>Conversion, repayment, and credit-risk indicators.</p></div><span class="badge {{ $businessSummary['overdue_loans'] > 0 ? 'badge-warning' : 'badge-success' }}">{{ $businessSummary['overdue_loans'] > 0 ? 'Needs attention' : 'Healthy' }}</span></div>
                    <div class="health-list">
                        <div>
                            <div class="health-copy"><span>Collection progress</span><strong>{{ number_format($businessSummary['collection_rate'], 1) }}%</strong></div>
                            <div class="health-track"><span class="health-fill" style="width:{{ min(100, $businessSummary['collection_rate']) }}%;background:#4f46e5"></span></div>
                            <div class="health-note">PHP {{ number_format($businessSummary['collections'], 2) }} collected of PHP {{ number_format($businessSummary['scheduled_payable'] + $businessSummary['penalties'], 2) }} due</div>
                        </div>
                        <div>
                            <div class="health-copy"><span>Loan completion rate</span><strong>{{ number_format($businessSummary['completion_rate'], 1) }}%</strong></div>
                            <div class="health-track"><span class="health-fill" style="width:{{ min(100, $businessSummary['completion_rate']) }}%;background:#12b76a"></span></div>
                            <div class="health-note">{{ $businessSummary['completed_loans'] }} completed of {{ $businessSummary['originated_loans'] }} originated loans</div>
                        </div>
                        <div>
                            <div class="health-copy"><span>Portfolio at risk</span><strong>{{ number_format($businessSummary['overdue_share'], 1) }}%</strong></div>
                            <div class="health-track"><span class="health-fill" style="width:{{ min(100, $businessSummary['overdue_share']) }}%;background:#f04438"></span></div>
                            <div class="health-note">{{ $businessSummary['overdue_installments'] }} overdue {{ Str::plural('installment', $businessSummary['overdue_installments']) }}</div>
                        </div>
                    </div>
                </article>
            </div>
            </div>

            <div class="analytics-panel" id="analytics-performance-panel" role="tabpanel" aria-labelledby="analytics-performance-tab" data-analytics-panel="performance" hidden>
            <article class="analytics-card analytics-wide">
                <div class="analytics-card-head">
                    <div><h3>Six-month cash movement</h3><p>Principal released compared with approved collections by month.</p></div>
                    <div class="trend-legend"><span><i style="background:#818cf8"></i>Released</span><span><i style="background:#12b76a"></i>Collected</span></div>
                </div>
                <div class="trend-list">
                    @foreach($business['monthlyTrend'] as $month)
                        @php
                            $releaseWidth = ((float) $month['principal_released'] / $trendMaximum) * 100;
                            $collectionWidth = ((float) $month['collections'] / $trendMaximum) * 100;
                        @endphp
                        <div class="trend-row">
                            <span class="trend-month">{{ $month['month'] }}</span>
                            <div class="trend-bars" aria-label="{{ $month['month'] }}: PHP {{ number_format($month['principal_released'], 2) }} released and PHP {{ number_format($month['collections'], 2) }} collected">
                                <div class="trend-track"><span class="trend-fill" style="width:{{ $releaseWidth }}%;background:#818cf8"></span></div>
                                <div class="trend-track"><span class="trend-fill" style="width:{{ $collectionWidth }}%;background:#12b76a"></span></div>
                            </div>
                            <div class="trend-values"><span><strong>PHP {{ number_format($month['principal_released'], 0) }}</strong> released</span><span><strong>PHP {{ number_format($month['collections'], 0) }}</strong> collected</span></div>
                        </div>
                    @endforeach
                </div>
            </article>

            <div class="analytics-detail-grid">
                <details class="analytics-disclosure" open>
                    <summary><div class="analytics-disclosure-copy"><h3>Loan product performance</h3><p>Receivables and collection efficiency by product.</p></div><span class="badge badge-neutral">{{ count($business['products']) }} products</span></summary>
                    <div class="analytics-disclosure-body">
                    @if(empty($business['products']))
                        <div class="analytics-empty">Product analytics will appear after loan applications are recorded.</div>
                    @else
                        <div class="rank-list">
                            @foreach($business['products'] as $product)
                                <div class="rank-row">
                                    <div class="rank-top"><div><div class="rank-name">{{ $product['name'] }}</div><div class="rank-meta">{{ $product['originated'] }} originated · {{ $product['active'] }} active · {{ $product['completed'] }} completed</div></div><div class="rank-value">PHP {{ number_format($product['collected'], 2) }}<div class="rank-meta">{{ number_format($product['collection_rate'], 1) }}% collected</div></div></div>
                                    <div class="rank-track"><span style="width:{{ min(100, $product['collection_rate']) }}%"></span></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </details>

                <details class="analytics-disclosure" open>
                    <summary><div class="analytics-disclosure-copy"><h3>Payment channel activity</h3><p>Approved value and pending workload by payment method.</p></div><span class="badge {{ $businessSummary['pending_payment_count'] > 0 ? 'badge-warning' : 'badge-success' }}">{{ $businessSummary['pending_payment_count'] }} pending</span></summary>
                    <div class="analytics-disclosure-body">
                    @if(empty($business['paymentMethods']))
                        <div class="analytics-empty">Payment channel analytics will appear after payments are submitted.</div>
                    @else
                        <div class="rank-list">
                            @foreach($business['paymentMethods'] as $method)
                                <div class="rank-row">
                                    <div class="rank-top"><div><div class="rank-name">{{ $method['label'] }}</div><div class="rank-meta">{{ $method['approved_count'] }} approved · {{ $method['rejected_count'] }} rejected</div></div><div class="rank-value">PHP {{ number_format($method['approved_amount'], 2) }}<div class="rank-meta">PHP {{ number_format($method['pending_amount'], 2) }} pending</div></div></div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </details>
            </div>
            </div>

            <div class="analytics-panel" id="analytics-operations-panel" role="tabpanel" aria-labelledby="analytics-operations-tab" data-analytics-panel="operations" hidden>
            <div class="analytics-section-head"><div><h3>Application pipeline</h3><p>Volume and requested principal across every decision stage.</p></div><span class="badge badge-neutral">{{ $businessSummary['applications'] }} total</span></div>
            <div class="pipeline-grid" aria-label="Loan application status distribution">
                @foreach($business['statuses'] as $status)
                    @php
                        $pipelineColor = match ($status['status']) {
                            'approved' => '#12b76a',
                            'paid' => '#4f46e5',
                            'rejected' => '#f04438',
                            default => '#f79009',
                        };
                    @endphp
                    <div class="pipeline-item" style="--pipeline-color:{{ $pipelineColor }}">
                        <span>{{ $status['label'] }}</span>
                        <strong>{{ $status['count'] }}</strong>
                        <small>PHP {{ number_format($status['principal'], 2) }} requested</small>
                    </div>
                @endforeach
            </div>

            <div class="analytics-section-head"><div><h3>Operations &amp; data quality</h3><p>Exceptions that may affect portfolio accuracy or require follow-up.</p></div></div>
            <div class="control-grid" aria-label="Data quality and operating controls">
                @foreach($business['controls'] as $control)
                    <div class="control-item {{ $control['count'] > 0 ? 'has-issues' : '' }}">
                        <span class="control-status">{{ $control['count'] > 0 ? $control['count'] : '✓' }}</span>
                        <div><strong>{{ $control['label'] }}</strong><span>{{ $control['count'] > 0 ? 'Review recommended' : 'No issues detected' }}</span></div>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </section>

    <section class="panel business-report" id="business-export">
        <div class="business-report-body">
            <div class="business-report-copy">
                <div class="eyebrow">Management reporting</div>
                <h3>Business performance report</h3>
                <p>A structured company-wide PDF covering portfolio size, releases, collections, outstanding and overdue balances, product performance, payment channels, six-month trends, and data-quality controls.</p>
                <a class="button button-primary" href="{{ route('admin.reports.business.download') }}" download data-no-transition><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>Download business PDF</a>
            </div>
            <div class="business-metrics" aria-label="Current business report snapshot">
                <div class="business-metric"><span>Principal released</span><strong>PHP {{ number_format($business['summary']['principal_released'], 2) }}</strong></div>
                <div class="business-metric"><span>Approved collections</span><strong>PHP {{ number_format($business['summary']['collections'], 2) }}</strong></div>
                <div class="business-metric"><span>Outstanding portfolio</span><strong>PHP {{ number_format($business['summary']['outstanding'], 2) }}</strong></div>
                <div class="business-metric"><span>Portfolio at risk</span><strong>{{ $business['summary']['overdue_loans'] }} overdue {{ Str::plural('loan', $business['summary']['overdue_loans']) }}</strong></div>
            </div>
        </div>
    </section>

    <section class="panel client-directory" id="client-reports" data-admin-table>
        <div class="panel-header">
            <div class="directory-heading">
                <span class="directory-heading-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zm8-1l2 2 4-4"/></svg></span>
                <div><h2 class="panel-title">Client report directory</h2><p class="panel-description">Search, filter, and download a complete account report for any client.</p></div>
            </div>
            <span class="badge badge-neutral">{{ $clients->count() }} available</span>
        </div>
        <div class="toolbar directory-toolbar">
            <input class="search-input" id="report-search" type="search" placeholder="Search name, email, or contact..." aria-label="Search client reports">
            <select class="filter-select" id="report-verification" aria-label="Filter by verification status">
                <option value="">All verification</option>
                <option value="approved">Verified</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
                <option value="not submitted">Not submitted</option>
            </select>
            <select class="filter-select" id="report-loans" aria-label="Filter by loan activity">
                <option value="">All loan activity</option>
                <option value="active">Active balance</option>
                <option value="completed">Completed only</option>
                <option value="none">No originated loan</option>
            </select>
        </div>
        @if($clients->isEmpty())
            <div class="empty-state"><strong>No client reports available</strong>Reports will appear after a client account is registered.</div>
        @else
            <div class="table-wrap"><table>
                <thead><tr><th>Client account</th><th>Verification</th><th>Loan portfolio</th><th>Current balance</th><th>Latest activity</th><th class="report-actions-cell">Report</th></tr></thead>
                <tbody id="report-table">
                @foreach($clients as $client)
                    @php
                        $verificationStatus = $client->clientVerification?->status ?? 'not submitted';
                        $verificationClass = match ($verificationStatus) { 'approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger', default => 'badge-neutral' };
                        $financialLoans = $client->loans->whereIn('status', ['approved', 'paid']);
                        $activeLoanCount = $client->loans->where('status', 'approved')->count();
                        $completedLoanCount = $client->loans->where('status', 'paid')->count();
                        $loanActivity = $activeLoanCount > 0 ? 'active' : ($completedLoanCount > 0 ? 'completed' : 'none');
                        $outstanding = $financialLoans->sum(function ($loan) {
                            $penalties = $loan->paymentSchedules->sum(fn ($schedule) => (float) $schedule->penalty_amount);
                            return max(0, (float) $loan->total_payable + $penalties - (float) $loan->paid_amount);
                        });
                        $latestLoanAt = $client->loans->max('updated_at');
                        $latestPaymentAt = $client->loans->flatMap->payments->max(fn ($payment) => $payment->paid_at ?? $payment->created_at);
                        $latestActivity = collect([$client->updated_at, $latestLoanAt, $latestPaymentAt])->filter()->max();
                    @endphp
                    <tr data-search="{{ strtolower($client->name.' '.$client->email.' '.$client->contact_number) }}" data-verification="{{ $verificationStatus }}" data-loans="{{ $loanActivity }}">
                        <td><div class="identity"><span class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</span><span><span class="cell-title">{{ $client->name }}</span><span class="email-value">{{ $client->email }}</span></span></div></td>
                        <td><span class="badge {{ $verificationClass }}">{{ $verificationStatus === 'approved' ? 'Verified' : ucfirst($verificationStatus) }}</span></td>
                        <td class="report-summary"><div class="cell-title">{{ $client->loans->count() }} {{ Str::plural('application', $client->loans->count()) }}</div><div class="cell-secondary">{{ $activeLoanCount }} active · {{ $completedLoanCount }} completed</div></td>
                        <td class="amount">PHP {{ number_format($outstanding, 2) }}</td>
                        <td><div>{{ $latestActivity?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? 'No activity' }}</div><div class="cell-secondary">{{ $latestActivity?->copy()->timezone('Asia/Manila')->format('h:i A') }} PHT</div></td>
                        <td class="report-actions-cell"><a class="button button-small report-button" href="{{ route('admin.reports.download', $client) }}" download data-no-transition aria-label="Download PDF report for {{ $client->name }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14"/></svg>PDF</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            <div class="empty-state" id="report-empty" hidden><strong>No matching reports</strong>Clear a filter or try a different client name, email, or contact number.</div>
        @endif
    </section>
</div></main>

<div class="modal fade report-guide-modal" id="report-guide-modal" tabindex="-1" aria-labelledby="report-guide-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content report-guide-card">
        <div class="report-guide-head"><div><div class="eyebrow" style="color:#c7d2fe">Reporting guide</div><h2 id="report-guide-title">Read the numbers with confidence</h2><p>Every dashboard value and PDF export is generated from the same reporting snapshot.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="report-guide-body"><div class="report-guide-grid">
            <div class="report-guide-item"><strong>Projected gross profit</strong><span>Contract interest plus recorded penalties, before operating expenses and credit losses.</span></div>
            <div class="report-guide-item"><strong>Approved collections</strong><span>Only approved payment records are recognized. Pending or rejected payments are excluded.</span></div>
            <div class="report-guide-item"><strong>Outstanding portfolio</strong><span>Scheduled receivables and penalties less approved collections, floored at zero per loan.</span></div>
            <div class="report-guide-item"><strong>Overdue exposure</strong><span>Unpaid scheduled amounts and penalties for installments already due or marked overdue.</span></div>
        </div></div>
    </div></div>
</div>
</body></html>
