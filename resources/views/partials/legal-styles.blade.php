:root { --navy:#101828; --muted:#667085; --border:#e4e7ec; --canvas:#f7f8fa; --primary:#4f46e5; }
* { box-sizing:border-box; }
body { margin:0; color:var(--navy); background:var(--canvas); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
.legal-header { color:#fff; background:linear-gradient(145deg,#111827,#312e81 65%,#4f46e5); }
.header-inner { max-width:940px; margin:0 auto; padding:38px 24px 56px; }
.brand { display:inline-flex; align-items:center; gap:11px; color:#fff; font-size:18px; font-weight:700; text-decoration:none; }
.brand-mark { display:grid; place-items:center; width:36px; height:36px; background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.16); border-radius:11px; }
.eyebrow { margin-top:54px; color:#c7d2fe; font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
h1 { max-width:720px; margin:10px 0 0; font-size:38px; line-height:1.15; letter-spacing:-.04em; }
.header-description { max-width:720px; margin:16px 0 0; color:#c7d2fe; font-size:14px; line-height:1.65; }
.legal-main { max-width:940px; margin:-24px auto 0; padding:0 24px 60px; }
.legal-card { padding:34px 38px; background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:0 10px 30px rgba(16,24,40,.07); }
.legal-meta { display:flex; justify-content:space-between; gap:16px; margin-bottom:28px; padding-bottom:20px; color:var(--muted); border-bottom:1px solid var(--border); font-size:11px; flex-wrap:wrap; }
.legal-notice { margin-bottom:25px; padding:14px 16px; color:#475467; background:#f9fafb; border:1px solid var(--border); border-radius:10px; font-size:11px; line-height:1.55; }
.section { scroll-margin-top:20px; }
.section + .section { margin-top:28px; padding-top:26px; border-top:1px solid #f2f4f7; }
h2 { margin:0 0 10px; font-size:17px; }
p, li { color:#475467; font-size:13px; line-height:1.7; }
p { margin:8px 0; }
ul { margin:10px 0 0; padding-left:20px; }
li + li { margin-top:7px; }
.highlight { margin-top:14px; padding:14px 16px; color:#344054; background:#fffaeb; border-left:3px solid #f79009; border-radius:7px; font-size:12px; line-height:1.65; }
.legal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:24px; }
.button { display:inline-flex; min-height:40px; padding:10px 15px; align-items:center; justify-content:center; color:#344054; background:#fff; border:1px solid var(--border); border-radius:9px; font-size:12px; font-weight:600; text-decoration:none; }
.button-primary { color:#fff; background:var(--primary); border-color:var(--primary); }
@media(max-width:600px) { h1{font-size:30px}.legal-card{padding:25px 20px}.header-inner{padding-bottom:48px}.legal-actions{align-items:stretch;flex-direction:column}.button{width:100%} }

@include('partials.motion-styles')
