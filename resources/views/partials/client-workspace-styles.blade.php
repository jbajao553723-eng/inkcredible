/* Dashboard and payment workspace navigation. */
.workspace-nav { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 24px; padding:7px; background:#fff; border:1px solid var(--border); border-radius:14px; }
.workspace-nav a { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; color:#475467; border-radius:9px; font-size:12px; font-weight:600; text-decoration:none; }
.workspace-nav a:hover { color:#3730a3; background:#eef2ff; }
.workspace-nav .nav-current { color:#3730a3; background:#eef2ff; }
.workspace-nav a span { padding:2px 6px; color:#667085; background:#f2f4f7; border-radius:5px; font-size:10px; }
.action-banner { display:flex; align-items:center; justify-content:space-between; gap:18px; margin:0 0 22px; padding:18px 20px; background:#fff; border:1px solid #c7d2fe; border-left:4px solid #6366f1; border-radius:12px; }
.action-banner strong { display:block; font-size:13px; }
.action-banner p { margin:5px 0 0; color:#667085; font-size:12px; line-height:1.6; }
.action-banner .button { flex:0 0 auto; }
.record-toolbar { display:flex; align-items:center; flex-wrap:wrap; gap:12px; padding:16px 22px; background:#fcfcfd; border-bottom:1px solid var(--border); }
.record-search { flex:1 1 220px; }
.record-toolbar label { display:block; margin-bottom:5px; color:#667085; font-size:10px; font-weight:600; }
.record-toolbar input,.record-toolbar select { width:100%; min-height:40px; padding:9px 12px; color:#344054; background:#fff; border:1px solid #d0d5dd; border-radius:9px; font-size:12px; }
.record-toolbar input:focus,.record-toolbar select:focus { outline:3px solid #e0e7ff; border-color:#818cf8; }
.record-toolbar .record-status { flex:0 1 180px; }
.record-count { flex:1 1 100%; margin:0; color:#667085; font-size:11px; }
.record-empty { padding:24px; color:#667085; font-size:12px; text-align:center; }
[data-record-row][hidden],.record-empty[hidden] { display:none!important; }
.row-actions { display:flex; flex-wrap:wrap; gap:7px; margin-top:8px; }
.row-action { display:inline-flex; align-items:center; min-height:30px; padding:5px 9px; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:7px; font-size:10px; font-weight:600; text-decoration:none; }
.row-action:hover { background:#e0e7ff; }
.row-action.is-loading,.receipt-link.is-loading { opacity:.6; pointer-events:none; }
.account-preview { grid-template-columns:repeat(2,minmax(0,1fr)); }
.payment-review { display:flex; justify-content:space-between; gap:18px; margin:20px 0 12px; padding:16px; background:#f8fafc; border:1px solid var(--border); border-radius:10px; }
.payment-review-label { margin-bottom:4px; color:#667085; font-size:11px; }
.payment-review strong { font-size:16px; }
.payment-review small { display:block; margin-top:4px; color:#667085; font-size:11px; }
.payment-guidance { margin:10px 0 0; color:#667085; font-size:11px; line-height:1.6; }
.payment-error { margin:14px 0; padding:12px 14px; color:#b42318; background:#fef3f2; border:1px solid #fecdca; border-radius:10px; font-size:12px; line-height:1.5; }
.payment-error[hidden] { display:none; }
.payment-section .method-card { min-height:86px; }
.method-option input:checked + .method-card::after { margin-left:auto; color:#4f46e5; font-weight:700; content:'\2713'; }
.payment-section .method-note { line-height:1.5; }
.history-panel { scroll-margin-top:20px; }
[id^="dashboard-"] { scroll-margin-top:20px; }
@media(max-width:760px) {
    .workspace-nav { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); margin-bottom:18px; }
    .workspace-nav a { justify-content:space-between; padding:9px 10px; font-size:11px; }
    .action-banner { align-items:stretch; flex-direction:column; }
    .action-banner .button { text-align:center; }
    .record-toolbar { padding:14px; gap:10px; }
    .record-toolbar .record-status { flex:1 1 120px; }
    .record-toolbar .record-search { flex-basis:100%; }
    .payment-history .table-wrap { overflow:visible; }
    .payment-history table,.payment-history tbody,.payment-history tr,.payment-history td { display:block; width:100%; }
    .payment-history thead { display:none; }
    .payment-history tbody { display:grid; gap:12px; padding:14px; }
    .payment-history tr { padding:4px 14px; border:1px solid var(--border); border-radius:12px; background:#fff; }
    .payment-history td { display:grid; grid-template-columns:95px minmax(0,1fr); gap:8px; padding:10px 0; border-bottom:1px solid #f2f4f7; white-space:normal; }
    .payment-history td > .cell-secondary { grid-column:2; }
    .payment-history td::before { color:#667085; font-size:10px; font-weight:600; content:attr(data-label); }
    .payment-history td:last-child { border:0; }
    .payment-history .transaction-ref { max-width:100%; }
    .payment-history .amount { text-align:left; }
    .payment-history .receipt-link { justify-self:start; }
}
@media(prefers-reduced-motion:reduce) { html { scroll-behavior:auto; } }
