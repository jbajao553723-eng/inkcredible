<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $loanLabel = $payment->loan?->loanType?->display_name
            ?? $payment->loan?->loanType?->name
            ?? 'Loan payment';
        $loanCode = $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id;
        $reference = $payment->provider_reference ?: $payment->reference ?: 'Payment #'.$payment->id;
        $paymentDate = $payment->created_at?->timezone('Asia/Manila');
    @endphp
    <title>Payment not completed | Inkcredible</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/js/app.js')
    <style>
        :root { --ink:#101828; --muted:#667085; --border:#e4e7ec; --canvas:#f7f8fc; --primary:#4f46e5; --primary-dark:#4338ca; --danger:#d92d20; --danger-dark:#b42318; --danger-soft:#fef3f2; --danger-border:#fecdca; }
        * { box-sizing:border-box; }
        body { min-height:100vh; margin:0; color:var(--ink); background:radial-gradient(circle at 13% 16%,rgba(99,102,241,.11),transparent 27rem),radial-gradient(circle at 88% 88%,rgba(240,68,56,.08),transparent 25rem),var(--canvas); font-family:'Inter',sans-serif; -webkit-font-smoothing:antialiased; }
        a { color:inherit; }
        a:focus-visible { outline:3px solid rgba(99,102,241,.3); outline-offset:3px; }
        .page { position:relative; display:flex; min-height:100vh; flex-direction:column; overflow:hidden; }
        .page::before,.page::after { position:absolute; width:290px; height:290px; border:1px solid rgba(99,102,241,.1); border-radius:50%; content:''; pointer-events:none; }
        .page::before { top:-165px; right:-75px; box-shadow:0 0 0 52px rgba(99,102,241,.025),0 0 0 105px rgba(99,102,241,.02); }
        .page::after { bottom:-210px; left:-80px; border-color:rgba(240,68,56,.08); box-shadow:0 0 0 52px rgba(240,68,56,.02),0 0 0 105px rgba(240,68,56,.015); }
        .site-header { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; width:min(1120px,calc(100% - 48px)); margin:0 auto; padding:26px 0; }
        .brand { display:inline-flex; align-items:center; gap:11px; font-size:18px; font-weight:750; letter-spacing:-.02em; text-decoration:none; }
        .brand-mark { display:block; width:36px; height:36px; flex:0 0 36px; object-fit:cover; background:#fff; border-radius:11px; box-shadow:0 9px 22px rgba(239,68,68,.2); }
        .secure-note { display:inline-flex; align-items:center; gap:7px; color:#475467; font-size:12px; font-weight:600; }
        .secure-note svg { width:16px; height:16px; color:#667085; }
        .content { position:relative; z-index:1; display:grid; place-items:center; width:min(960px,calc(100% - 48px)); margin:auto; padding:32px 0 64px; }
        .result-card { width:100%; overflow:hidden; background:rgba(255,255,255,.94); border:1px solid rgba(228,231,236,.9); border-radius:24px; box-shadow:0 28px 70px rgba(16,24,40,.12),0 4px 14px rgba(16,24,40,.04); backdrop-filter:blur(12px); }
        .result-layout { display:grid; grid-template-columns:minmax(0,1.08fr) minmax(330px,.92fr); }
        .message-panel { position:relative; display:flex; min-height:540px; padding:58px 56px; flex-direction:column; justify-content:center; }
        .message-panel::after { position:absolute; inset:34px 0 34px auto; width:1px; background:linear-gradient(transparent,var(--border) 12%,var(--border) 88%,transparent); content:''; }
        .status-icon { display:grid; place-items:center; width:82px; height:82px; margin-bottom:30px; color:var(--danger); background:var(--danger-soft); border:1px solid var(--danger-border); border-radius:24px; box-shadow:0 0 0 9px rgba(254,243,242,.78); }
        .status-icon svg { width:37px; height:37px; }
        .eyebrow { margin-bottom:11px; color:var(--danger-dark); font-size:11px; font-weight:800; letter-spacing:.11em; text-transform:uppercase; }
        h1 { max-width:440px; margin:0; font-size:clamp(31px,4vw,43px); line-height:1.08; letter-spacing:-.045em; }
        .lead { max-width:480px; margin:18px 0 0; color:var(--muted); font-size:15px; line-height:1.75; }
        .lead strong { color:#344054; font-weight:650; }
        .state-note { display:flex; align-items:flex-start; gap:10px; margin-top:24px; padding:13px 14px; color:#912018; background:#fff7f6; border:1px solid #fee4e2; border-radius:12px; font-size:12px; line-height:1.55; }
        .state-note svg { width:17px; height:17px; flex:0 0 17px; margin-top:1px; }
        .actions { display:flex; flex-wrap:wrap; gap:11px; margin-top:32px; }
        .button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:46px; padding:11px 17px; border:1px solid var(--border); border-radius:11px; font-size:13px; font-weight:700; text-decoration:none; transition:.18s ease; }
        .button svg { width:17px; height:17px; }
        .button-primary { color:#fff; background:var(--primary); border-color:var(--primary); box-shadow:0 9px 20px rgba(79,70,229,.2); }
        .button-primary:hover { background:var(--primary-dark); border-color:var(--primary-dark); transform:translateY(-1px); box-shadow:0 12px 24px rgba(79,70,229,.24); }
        .button-secondary { color:#344054; background:#fff; box-shadow:0 1px 2px rgba(16,24,40,.04); }
        .button-secondary:hover { background:#f9fafb; border-color:#d0d5dd; transform:translateY(-1px); }
        .receipt-panel { display:flex; align-items:center; padding:46px 44px; background:linear-gradient(155deg,#fffafa 0%,#f8fafc 100%); }
        .receipt { width:100%; overflow:hidden; background:#fff; border:1px solid var(--border); border-radius:17px; box-shadow:0 12px 32px rgba(16,24,40,.07); }
        .receipt-header { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:19px 20px; border-bottom:1px dashed #d0d5dd; }
        .receipt-title { font-size:13px; font-weight:750; }
        .status-pill { display:inline-flex; align-items:center; gap:6px; padding:6px 9px; color:#b42318; background:#fef3f2; border-radius:999px; font-size:10px; font-weight:750; white-space:nowrap; }
        .status-dot { width:6px; height:6px; background:currentColor; border-radius:50%; box-shadow:0 0 0 3px rgba(217,45,32,.1); }
        .amount-block { padding:25px 20px 23px; text-align:center; }
        .amount-label { color:var(--muted); font-size:11px; font-weight:650; letter-spacing:.06em; text-transform:uppercase; }
        .amount-value { margin-top:7px; color:#1d2939; font-size:31px; font-weight:800; letter-spacing:-.04em; }
        .amount-subtitle { margin-top:6px; color:#98a2b3; font-size:11px; }
        .details { margin:0; padding:0 20px 8px; }
        .detail-row { display:grid; grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr); gap:12px; padding:13px 0; border-top:1px solid #f2f4f7; }
        .detail-row dt { color:var(--muted); font-size:11px; }
        .detail-row dd { min-width:0; margin:0; color:#344054; font-size:11px; font-weight:650; text-align:right; overflow-wrap:anywhere; }
        .detail-row dd span { display:block; margin-top:3px; color:#98a2b3; font-size:10px; font-weight:500; }
        .receipt-footer { display:flex; align-items:flex-start; gap:8px; padding:14px 20px; color:#667085; background:#fcfcfd; border-top:1px solid #f2f4f7; font-size:10px; line-height:1.45; }
        .receipt-footer svg { width:15px; height:15px; flex:0 0 15px; color:var(--danger); }
        @media(max-width:820px){.content{width:min(620px,calc(100% - 32px));padding-top:20px}.result-layout{grid-template-columns:1fr}.message-panel{min-height:auto;padding:48px 40px 40px;align-items:center;text-align:center}.message-panel::after{display:none}.state-note{max-width:500px;text-align:left}.receipt-panel{padding:36px 40px 44px}.receipt{max-width:430px;margin:0 auto}}
        @media(max-width:520px){.site-header{width:calc(100% - 32px);padding:19px 0}.secure-note span{display:none}.content{width:calc(100% - 24px);padding:14px 0 34px}.result-card{border-radius:20px}.message-panel{padding:38px 22px 30px}.status-icon{width:70px;height:70px;margin-bottom:25px;border-radius:20px}.status-icon svg{width:33px;height:33px}h1{font-size:30px}.lead{margin-top:14px;font-size:14px;line-height:1.65}.actions{width:100%;margin-top:26px}.button{width:100%}.receipt-panel{padding:28px 18px 32px}}
        @media(prefers-reduced-motion:reduce){*,*::before,*::after{scroll-behavior:auto!important;animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}}
        @include('partials.motion-styles')
    </style>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Inkcredible dashboard"><x-brand-mark /><span>Inkcredible</span></a>
            <div class="secure-note"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-linecap="round" stroke-width="1.8"/></svg><span>Secure payment return</span></div>
        </header>
        <main class="content">
            <article class="result-card" aria-labelledby="result-title">
                <div class="result-layout">
                    <section class="message-panel">
                        <div class="status-icon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" stroke-width="1.8"/><path d="m9 9 6 6m0-6-6 6" stroke-linecap="round" stroke-width="2"/></svg></div>
                        <div class="eyebrow">Payment stopped</div>
                        <h1 id="result-title">Your payment was not completed.</h1>
                        <p class="lead">The checkout was cancelled or declined. <strong>No payment was applied</strong> to your Inkcredible loan balance.</p>
                        <div class="state-note" role="status"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke-linecap="round" stroke-width="1.8"/></svg><span>You can safely start another payment whenever you are ready. A new attempt will create a fresh secure checkout session.</span></div>
                        <div class="actions" aria-label="Next steps">
                            <a class="button button-primary" href="{{ route('payments.index') }}">Try payment again<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9"/></svg></a>
                            <a class="button button-secondary" href="{{ route('dashboard') }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20h-5v-6H9v6H4z" stroke-linejoin="round" stroke-width="1.8"/></svg>Back to dashboard</a>
                        </div>
                    </section>
                    <aside class="receipt-panel" aria-label="Cancelled payment details">
                        <div class="receipt">
                            <div class="receipt-header"><div class="receipt-title">Payment summary</div><div class="status-pill"><span class="status-dot"></span>Not completed</div></div>
                            <div class="amount-block"><div class="amount-label">Attempted amount</div><div class="amount-value">&#8369;{{ number_format((float) $payment->amount, 2) }}</div><div class="amount-subtitle">{{ strtoupper($payment->currency ?: 'PHP') }}</div></div>
                            <dl class="details">
                                <div class="detail-row"><dt>Loan account</dt><dd>{{ $loanLabel }}<span>{{ $loanCode }}</span></dd></div>
                                <div class="detail-row"><dt>Payment method</dt><dd>{{ $payment->method_label }}</dd></div>
                                <div class="detail-row"><dt>Reference</dt><dd>{{ $reference }}</dd></div>
                                <div class="detail-row"><dt>Attempted on</dt><dd>{{ $paymentDate?->format('M j, Y') ?? 'Not available' }}<span>{{ $paymentDate?->format('g:i A') }} PHT</span></dd></div>
                            </dl>
                            <div class="receipt-footer"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke-linecap="round" stroke-width="1.8"/></svg>This attempt is closed and will not affect your outstanding loan balance.</div>
                        </div>
                    </aside>
                </div>
            </article>
        </main>
    </div>
    <script>
        if (window.opener && !window.opener.closed) {
            window.opener.location.replace(window.location.href);
            window.close();
        }
    </script>
</body>
</html>
