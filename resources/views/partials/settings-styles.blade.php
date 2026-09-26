.settings-shell { max-width: 980px; }
.settings-tabs { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:22px; }
.settings-tab { display:flex; align-items:center; gap:12px; padding:15px 16px; color:#475467; background:#fff; border:1px solid var(--border); border-radius:13px; text-decoration:none; transition:.15s ease; }
.settings-tab:hover { border-color:#c7d2fe; transform:translateY(-1px); }
.settings-tab.active { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; box-shadow:0 5px 16px rgba(79,70,229,.08); }
.tab-icon { display:grid; place-items:center; width:36px; height:36px; flex:0 0 36px; color:#4f46e5; background:#f4f3ff; border-radius:10px; }
.tab-icon svg { width:18px; height:18px; }
.tab-title { display:block; color:#344054; font-size:12px; font-weight:700; }
.settings-tab.active .tab-title { color:#4338ca; }
.tab-note { display:block; margin-top:3px; color:#98a2b3; font-size:10px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 14px; }
.form-group { margin-bottom:18px; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; margin-bottom:7px; color:#344054; font-size:12px; font-weight:600; }
.optional { color:#98a2b3; font-size:10px; font-weight:500; }
.form-control { width:100%; min-height:44px; padding:10px 12px; color:var(--navy); background:#fff; border:1px solid #d0d5dd; border-radius:9px; outline:none; }
.form-control:focus { border-color:#818cf8; box-shadow:0 0 0 4px rgba(99,102,241,.1); }
textarea.form-control { min-height:96px; resize:vertical; }
.field-error { margin:6px 0 0; color:#b42318; font-size:11px; }
.form-help { margin:6px 0 0; color:var(--muted); font-size:10px; line-height:1.5; }
.save-button { display:inline-flex; min-height:41px; padding:9px 15px; align-items:center; justify-content:center; color:#fff; border:0; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; }
.save-button { background:var(--primary); }
.verification-notice { margin-bottom:20px; padding:14px; border-radius:10px; font-size:11px; line-height:1.55; }
.notice-pending { color:#854a0e; background:#fffaeb; border:1px solid #fedf89; }
.notice-approved { color:#05603a; background:#ecfdf3; border:1px solid #abefc6; }
.notice-rejected { color:#912018; background:#fef3f2; border:1px solid #fecdca; }
.document-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.security-grid { display:grid; grid-template-columns:1fr; gap:22px; align-items:start; }
@media(max-width:760px){.settings-tabs,.security-grid{grid-template-columns:1fr}.settings-tabs{gap:8px}.form-grid,.document-grid{grid-template-columns:1fr}.form-group.full{grid-column:auto}}
