:root {
    --navy: #101828;
    --muted: #667085;
    --border: #d0d5dd;
    --canvas: #f7f8fa;
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --danger: #b42318;
}

* { box-sizing: border-box; }
body { min-height: 100vh; margin: 0; color: var(--navy); background: var(--canvas); font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
button, input, textarea { font: inherit; }
.auth-page { display: grid; min-height: 100vh; grid-template-columns: minmax(390px, .92fr) minmax(520px, 1.08fr); }
.brand-panel { position: relative; display: flex; min-height: 100vh; padding: 42px 48px; overflow: hidden; color: #fff; background: linear-gradient(145deg, #111827 0%, #312e81 55%, #4f46e5 100%); flex-direction: column; }
.brand-panel::before, .brand-panel::after { position: absolute; border: 1px solid rgba(255, 255, 255, .09); border-radius: 50%; content: ''; }
.brand-panel::before { right: -180px; bottom: -170px; width: 480px; height: 480px; }
.brand-panel::after { right: -80px; bottom: -70px; width: 280px; height: 280px; }
.brand { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 12px; color: #fff; font-size: 19px; font-weight: 700; text-decoration: none; }
.brand-mark { display: grid; place-items: center; width: 38px; height: 38px; border: 1px solid rgba(255, 255, 255, .16); border-radius: 11px; background: rgba(255, 255, 255, .12); box-shadow: 0 8px 20px rgba(0, 0, 0, .12); }
.brand-content { position: relative; z-index: 1; max-width: 470px; margin: auto 0; padding: 70px 0; }
.brand-eyebrow { margin-bottom: 16px; color: #c7d2fe; font-size: 12px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
.brand-title { max-width: 450px; margin: 0; font-size: clamp(36px, 4vw, 56px); line-height: 1.08; letter-spacing: -.05em; }
.brand-description { max-width: 410px; margin: 22px 0 0; color: #c7d2fe; font-size: 15px; line-height: 1.7; }
.feature-list { display: grid; gap: 14px; margin: 34px 0 0; padding: 0; list-style: none; }
.feature-list li { display: flex; align-items: center; gap: 11px; color: #e0e7ff; font-size: 13px; }
.feature-check { display: grid; place-items: center; width: 24px; height: 24px; flex: 0 0 24px; color: #fff; background: rgba(255, 255, 255, .13); border-radius: 50%; }
.feature-check svg { width: 13px; height: 13px; }
.brand-footer { position: relative; z-index: 1; color: #a5b4fc; font-size: 11px; }
.form-panel { display: flex; min-height: 100vh; padding: 48px clamp(32px, 7vw, 100px); background: #fff; align-items: center; justify-content: center; }
.form-shell { width: 100%; max-width: 440px; }
.form-shell.register { max-width: 540px; }
.mobile-brand { display: none; margin-bottom: 34px; color: var(--navy); }
.mobile-brand .brand-mark { color: #fff; background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.form-eyebrow { margin-bottom: 8px; color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; }
h1 { margin: 0; font-size: 30px; letter-spacing: -.035em; }
.form-description { margin: 10px 0 30px; color: var(--muted); font-size: 14px; line-height: 1.55; }
.alert { margin-bottom: 20px; padding: 13px 14px; border: 1px solid; border-radius: 10px; font-size: 12px; line-height: 1.5; }
.alert-error { color: #912018; background: #fef3f2; border-color: #fecdca; }
.alert-success { color: #05603a; background: #ecfdf3; border-color: #abefc6; }
.alert ul { margin: 0; padding-left: 18px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 17px; }
.form-label { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 7px; color: #344054; font-size: 12px; font-weight: 600; }
.optional { color: #98a2b3; font-size: 10px; font-weight: 500; }
.input-wrap { position: relative; }
.form-control { width: 100%; min-height: 46px; padding: 11px 13px; color: var(--navy); background: #fff; border: 1px solid var(--border); border-radius: 10px; outline: none; transition: border-color .15s, box-shadow .15s; }
.form-control::placeholder { color: #98a2b3; }
.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 4px rgba(99, 102, 241, .1); }
.form-control.is-invalid { border-color: #f04438; }
textarea.form-control { min-height: 82px; resize: vertical; }
.password-input { padding-right: 72px; }
.password-toggle { position: absolute; top: 50%; right: 12px; padding: 4px; color: var(--primary); background: transparent; border: 0; transform: translateY(-50%); font-size: 11px; font-weight: 600; cursor: pointer; }
.field-error { margin: 6px 0 0; color: var(--danger); font-size: 11px; }
.field-help { margin: 6px 0 0; color: #98a2b3; font-size: 11px; }
.form-options { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 3px 0 21px; }
.checkbox-label { display: inline-flex; align-items: center; gap: 8px; color: #475467; font-size: 12px; cursor: pointer; }
.checkbox-label input { width: 15px; height: 15px; accent-color: var(--primary); }
.text-link { color: var(--primary); font-size: 12px; font-weight: 600; text-decoration: none; }
.text-link:hover { color: var(--primary-dark); text-decoration: underline; }
.submit-button { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; min-height: 47px; color: #fff; background: var(--primary); border: 0; border-radius: 10px; font-weight: 600; cursor: pointer; box-shadow: 0 8px 20px rgba(79, 70, 229, .18); transition: .15s ease; }
.submit-button:hover { background: var(--primary-dark); transform: translateY(-1px); }
.submit-button:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.auth-switch { margin: 23px 0 0; color: var(--muted); font-size: 12px; text-align: center; }
.terms-note { margin: 17px 0 0; color: #98a2b3; font-size: 10px; line-height: 1.5; text-align: center; }
.consent-box { display: flex; gap: 10px; margin: 4px 0 18px; padding: 13px; color: #475467; background: #f9fafb; border: 1px solid #eaecf0; border-radius: 10px; font-size: 11px; line-height: 1.55; }
.consent-box input { width: 16px; height: 16px; margin-top: 1px; flex: 0 0 16px; accent-color: var(--primary); }
.consent-box a { color: var(--primary); font-weight: 600; }
.divider { display: flex; align-items: center; gap: 12px; margin: 24px 0; color: #98a2b3; font-size: 10px; text-transform: uppercase; }
.divider::before, .divider::after { height: 1px; background: #eaecf0; content: ''; flex: 1; }

@media (max-width: 960px) {
    .auth-page { grid-template-columns: minmax(330px, .72fr) minmax(460px, 1fr); }
    .brand-panel { padding: 36px; }
    .brand-title { font-size: 38px; }
    .form-panel { padding: 42px; }
}

@media (max-width: 760px) {
    .auth-page { display: block; }
    .brand-panel { display: none; }
    .form-panel { min-height: 100vh; padding: 34px 22px; }
    .mobile-brand { display: inline-flex; }
}

@media (max-width: 520px) {
    .form-row { grid-template-columns: 1fr; gap: 0; }
    h1 { font-size: 27px; }
}
