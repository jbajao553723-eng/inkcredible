.settings-shell { max-width: 1080px; }
.settings-tabs { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:22px; }
.settings-tab { display:flex; min-width:0; align-items:center; gap:12px; padding:15px 16px; color:#475467; background:#fff; border:1px solid var(--border); border-radius:13px; text-decoration:none; transition:.15s ease; }
.settings-tab:hover { border-color:#c7d2fe; transform:translateY(-1px); }
.settings-tab.active { color:#4338ca; background:#eef2ff; border-color:#c7d2fe; box-shadow:0 5px 16px rgba(79,70,229,.08); }
.tab-icon { display:grid; place-items:center; width:36px; height:36px; flex:0 0 36px; color:#4f46e5; background:#f4f3ff; border-radius:10px; }
.tab-icon svg { width:18px; height:18px; }
.tab-title { display:block; color:#344054; font-size:12px; font-weight:700; }
.settings-tab.active .tab-title { color:#4338ca; }
.tab-note { display:block; margin-top:3px; color:#98a2b3; font-size:10px; }
.settings-tab > span:last-child { min-width:0; }
.tab-title,.tab-note { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:0 14px; }
.form-group { margin-bottom:18px; }
.form-group.full { grid-column:1/-1; }
.form-label { display:block; margin-bottom:7px; color:#344054; font-size:12px; font-weight:600; }
.optional { color:#98a2b3; font-size:10px; font-weight:500; }
.form-control { width:100%; min-width:0; min-height:44px; padding:10px 12px; color:var(--navy); background:#fff; border:1px solid #d0d5dd; border-radius:9px; outline:none; }
.form-control:focus { border-color:#818cf8; box-shadow:0 0 0 4px rgba(99,102,241,.1); }
input[type="file"].form-control { padding:5px 6px; color:#667085; font-size:11px; }
input[type="file"].form-control::file-selector-button { min-height:32px; margin-right:10px; padding:6px 11px; color:#344054; background:#f8fafc; border:1px solid #d0d5dd; border-radius:7px; font:inherit; font-weight:600; cursor:pointer; }
input[type="file"].form-control:hover::file-selector-button { background:#eef2ff; border-color:#c7d2fe; }
textarea.form-control { min-height:96px; resize:vertical; }
.field-error { margin:6px 0 0; color:#b42318; font-size:11px; }
.form-help { margin:6px 0 0; color:var(--muted); font-size:10px; line-height:1.5; }
.save-button { display:inline-flex; min-height:41px; padding:9px 15px; align-items:center; justify-content:center; color:#fff; background:var(--primary); border:0; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; box-shadow:0 6px 14px rgba(79,70,229,.16); transition:.15s ease; }
.save-button:hover { background:var(--primary-dark); transform:translateY(-1px); }
.form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:14px; }
.verification-notice { margin-bottom:20px; padding:14px; border-radius:10px; font-size:11px; line-height:1.55; }
.notice-pending { color:#854a0e; background:#fffaeb; border:1px solid #fedf89; }
.notice-approved { color:#05603a; background:#ecfdf3; border:1px solid #abefc6; }
.notice-rejected { color:#912018; background:#fef3f2; border:1px solid #fecdca; }
.document-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.security-grid { display:grid; grid-template-columns:1fr; gap:22px; align-items:start; }
.motion-setting { display:flex; align-items:center; justify-content:space-between; gap:22px; padding:20px; background:#f8fafc; border:1px solid #e4e7ec; border-radius:12px; }
.motion-setting-copy { display:flex; align-items:flex-start; gap:13px; }
.motion-setting-icon { display:grid; place-items:center; width:40px; height:40px; flex:0 0 40px; color:#4f46e5; background:#eef2ff; border-radius:11px; }
.motion-setting-icon svg { width:20px; height:20px; }
.motion-setting-copy strong,.motion-setting-copy small { display:block; }
.motion-setting-copy strong { color:#344054; font-size:13px; }
.motion-setting-copy small { max-width:560px; margin-top:5px; color:#667085; font-size:10px; line-height:1.55; }
.motion-toggle { position:relative; width:44px; height:25px; flex:0 0 44px; }
.motion-toggle input { position:absolute; opacity:0; }
.motion-toggle-track { position:absolute; inset:0; background:#d0d5dd; border-radius:999px; cursor:pointer; transition:.15s ease; }
.motion-toggle-track::after { position:absolute; top:3px; left:3px; width:19px; height:19px; background:#fff; border-radius:50%; box-shadow:0 1px 3px rgba(16,24,40,.25); content:''; transition:.15s ease; }
.motion-toggle input:focus-visible + .motion-toggle-track { outline:3px solid rgba(99,102,241,.25); outline-offset:2px; }
.motion-toggle input:checked + .motion-toggle-track { background:#4f46e5; }
.motion-toggle input:checked + .motion-toggle-track::after { transform:translateX(19px); }
@media(max-width:900px){.settings-tabs{grid-template-columns:repeat(2,1fr)}}
@media(max-width:760px){.settings-tabs,.security-grid{grid-template-columns:1fr}.settings-tabs{gap:8px}.settings-tab{padding:12px 14px}.form-grid,.document-grid{grid-template-columns:1fr}.form-group.full{grid-column:auto}.form-actions .save-button{width:100%}.motion-setting{align-items:flex-start}}
