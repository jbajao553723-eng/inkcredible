<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $isApproved = $payment->status === \App\Models\Payment::STATUS_APPROVED;
        $loanLabel = $payment->loan?->loanType?->display_name
            ?? $payment->loan?->loanType?->name
            ?? 'Loan payment';
        $loanCode = $payment->loan?->loan_code ?: 'Loan #'.$payment->loan_id;
        $reference = $payment->provider_reference ?: $payment->reference ?: 'Payment #'.$payment->id;
        $paymentDate = ($payment->paid_at ?: $payment->created_at)?->timezone('Asia/Manila');
    @endphp
    <title>{{ $isApproved ? 'Payment confirmed' : 'Payment processing' }} | Inkcredible</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/js/app.js')

    <style>
        :root {
            --ink: #101828;
            --muted: #667085;
            --border: #e4e7ec;
            --surface: #ffffff;
            --canvas: #f7f8fc;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --success: #079455;
            --success-dark: #05603a;
            --success-soft: #ecfdf3;
            --success-border: #abefc6;
            --pending: #dc6803;
            --pending-dark: #93370d;
            --pending-soft: #fffaeb;
            --pending-border: #fedf89;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 13% 16%, rgba(99, 102, 241, .12), transparent 27rem),
                radial-gradient(circle at 88% 88%, rgba(16, 185, 129, .10), transparent 25rem),
                var(--canvas);
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; }
        button, a { -webkit-tap-highlight-color: transparent; }
        a:focus-visible { outline: 3px solid rgba(99, 102, 241, .30); outline-offset: 3px; }

        .page {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        .page::before,
        .page::after {
            position: absolute;
            width: 290px;
            height: 290px;
            border: 1px solid rgba(99, 102, 241, .10);
            border-radius: 50%;
            content: '';
            pointer-events: none;
        }

        .page::before { top: -165px; right: -75px; box-shadow: 0 0 0 52px rgba(99, 102, 241, .025), 0 0 0 105px rgba(99, 102, 241, .02); }
        .page::after { bottom: -210px; left: -80px; box-shadow: 0 0 0 52px rgba(16, 185, 129, .025), 0 0 0 105px rgba(16, 185, 129, .02); }

        .site-header {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            padding: 26px 0;
        }

        .brand { display: inline-flex; align-items: center; gap: 11px; text-decoration: none; font-size: 18px; font-weight: 750; letter-spacing: -.02em; }
        .brand-mark { display: block; width: 36px; height: 36px; flex: 0 0 36px; object-fit: cover; background: #fff; border-radius: 11px; box-shadow: 0 9px 22px rgba(239, 68, 68, .2); }
        .secure-note { display: inline-flex; align-items: center; gap: 7px; color: #475467; font-size: 12px; font-weight: 600; }
        .secure-note svg { width: 16px; height: 16px; color: #667085; }

        .content {
            position: relative;
            z-index: 1;
            display: grid;
            place-items: center;
            width: min(960px, calc(100% - 48px));
            margin: auto;
            padding: 32px 0 64px;
        }

        .result-card {
            width: 100%;
            overflow: hidden;
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(228, 231, 236, .9);
            border-radius: 24px;
            box-shadow: 0 28px 70px rgba(16, 24, 40, .12), 0 4px 14px rgba(16, 24, 40, .04);
            backdrop-filter: blur(12px);
        }

        .result-layout { display: grid; grid-template-columns: minmax(0, 1.08fr) minmax(330px, .92fr); }

        .message-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 540px;
            padding: 58px 56px;
        }

        .message-panel::after {
            position: absolute;
            inset: 34px 0 34px auto;
            width: 1px;
            background: linear-gradient(transparent, var(--border) 12%, var(--border) 88%, transparent);
            content: '';
        }

        .status-icon {
            position: relative;
            display: grid;
            place-items: center;
            width: 82px;
            height: 82px;
            margin-bottom: 30px;
            color: var(--success);
            background: var(--success-soft);
            border: 1px solid var(--success-border);
            border-radius: 24px;
            box-shadow: 0 0 0 9px rgba(236, 253, 243, .75);
        }

        .status-icon.pending { color: var(--pending); background: var(--pending-soft); border-color: var(--pending-border); box-shadow: 0 0 0 9px rgba(255, 250, 235, .78); }
        .status-icon svg { width: 39px; height: 39px; }
        .status-icon.pending svg { width: 34px; height: 34px; }

        .eyebrow { margin-bottom: 11px; color: {{ $isApproved ? 'var(--success-dark)' : 'var(--pending-dark)' }}; font-size: 11px; font-weight: 800; letter-spacing: .11em; text-transform: uppercase; }
        h1 { max-width: 440px; margin: 0; font-size: clamp(31px, 4vw, 43px); line-height: 1.08; letter-spacing: -.045em; }
        .lead { max-width: 480px; margin: 18px 0 0; color: var(--muted); font-size: 15px; line-height: 1.75; }
        .lead strong { color: #344054; font-weight: 650; }

        .state-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 24px;
            padding: 13px 14px;
            color: {{ $isApproved ? '#05603a' : '#854a0e' }};
            background: {{ $isApproved ? '#f6fef9' : '#fffcf5' }};
            border: 1px solid {{ $isApproved ? '#d1fadf' : '#fef0c7' }};
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.55;
        }

        .state-note svg { width: 17px; height: 17px; flex: 0 0 17px; margin-top: 1px; }

        .actions { display: flex; flex-wrap: wrap; gap: 11px; margin-top: 32px; }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 46px; padding: 11px 17px; border: 1px solid var(--border); border-radius: 11px; font-size: 13px; font-weight: 700; text-decoration: none; transition: .18s ease; }
        .button svg { width: 17px; height: 17px; }
        .button-primary { color: #fff; background: var(--primary); border-color: var(--primary); box-shadow: 0 9px 20px rgba(79, 70, 229, .20); }
        .button-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 12px 24px rgba(79, 70, 229, .24); }
        .button-secondary { color: #344054; background: #fff; box-shadow: 0 1px 2px rgba(16, 24, 40, .04); }
        .button-secondary:hover { background: #f9fafb; border-color: #d0d5dd; transform: translateY(-1px); }

        .receipt-panel { display: flex; align-items: center; padding: 46px 44px; background: linear-gradient(155deg, #fafaff 0%, #f8fafc 100%); }
        .receipt { width: 100%; overflow: hidden; background: #fff; border: 1px solid var(--border); border-radius: 17px; box-shadow: 0 12px 32px rgba(16, 24, 40, .07); }
        .receipt-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 19px 20px; border-bottom: 1px dashed #d0d5dd; }
        .receipt-title { font-size: 13px; font-weight: 750; }
        .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 9px; color: {{ $isApproved ? '#067647' : '#b54708' }}; background: {{ $isApproved ? '#ecfdf3' : '#fffaeb' }}; border-radius: 999px; font-size: 10px; font-weight: 750; white-space: nowrap; }
        .status-dot { width: 6px; height: 6px; background: currentColor; border-radius: 50%; box-shadow: 0 0 0 3px {{ $isApproved ? 'rgba(7, 148, 85, .10)' : 'rgba(220, 104, 3, .10)' }}; }

        .amount-block { padding: 25px 20px 23px; text-align: center; }
        .amount-label { color: var(--muted); font-size: 11px; font-weight: 650; text-transform: uppercase; letter-spacing: .06em; }
        .amount-value { margin-top: 7px; color: #1d2939; font-size: 31px; font-weight: 800; letter-spacing: -.04em; }
        .amount-subtitle { margin-top: 6px; color: #98a2b3; font-size: 11px; }

        .details { margin: 0; padding: 0 20px 8px; }
        .detail-row { display: grid; grid-template-columns: minmax(0, .8fr) minmax(0, 1.2fr); gap: 12px; padding: 13px 0; border-top: 1px solid #f2f4f7; }
        .detail-row dt { color: var(--muted); font-size: 11px; }
        .detail-row dd { min-width: 0; margin: 0; color: #344054; font-size: 11px; font-weight: 650; text-align: right; overflow-wrap: anywhere; }
        .detail-row dd span { display: block; margin-top: 3px; color: #98a2b3; font-size: 10px; font-weight: 500; }
        .receipt-footer { display: flex; align-items: center; gap: 8px; padding: 14px 20px; color: #667085; background: #fcfcfd; border-top: 1px solid #f2f4f7; font-size: 10px; line-height: 1.4; }
        .receipt-footer svg { width: 15px; height: 15px; flex: 0 0 15px; color: var(--primary); }

        @media (max-width: 820px) {
            .content { width: min(620px, calc(100% - 32px)); padding-top: 20px; }
            .result-layout { grid-template-columns: 1fr; }
            .message-panel { min-height: auto; padding: 48px 40px 40px; text-align: center; align-items: center; }
            .message-panel::after { display: none; }
            .lead { max-width: 520px; }
            .state-note { max-width: 500px; text-align: left; }
            .receipt-panel { padding: 36px 40px 44px; }
            .receipt { max-width: 430px; margin: 0 auto; }
        }

        @media (max-width: 520px) {
            .site-header { width: calc(100% - 32px); padding: 19px 0; }
            .secure-note span { display: none; }
            .content { width: calc(100% - 24px); padding: 14px 0 34px; }
            .result-card { border-radius: 20px; }
            .message-panel { padding: 38px 22px 30px; }
            .status-icon { width: 70px; height: 70px; margin-bottom: 25px; border-radius: 20px; }
            .status-icon svg { width: 34px; height: 34px; }
            h1 { font-size: 30px; }
            .lead { margin-top: 14px; font-size: 14px; line-height: 1.65; }
            .actions { width: 100%; margin-top: 26px; }
            .button { width: 100%; }
            .receipt-panel { padding: 28px 18px 32px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }

        @include('partials.motion-styles')
    </style>
</head>
<body>
    <div class="page">
        <header class="site-header">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Inkcredible dashboard">
                <x-brand-mark />
                <span>Inkcredible</span>
            </a>
            <div class="secure-note">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-linecap="round" stroke-width="1.8"/></svg>
                <span>Secure payment return</span>
            </div>
        </header>

        <main class="content">
            <article class="result-card" aria-labelledby="result-title">
                <div class="result-layout">
                    <section class="message-panel">
                        <div class="status-icon {{ $isApproved ? '' : 'pending' }}" aria-hidden="true">
                            @if($isApproved)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m5 12.5 4.2 4.2L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"/></svg>
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8.5" stroke-width="1.8"/><path d="M12 7.5V12l3 1.8" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                            @endif
                        </div>

                        <div class="eyebrow">{{ $isApproved ? 'Payment complete' : 'Confirmation in progress' }}</div>
                        <h1 id="result-title">{{ $isApproved ? 'Your payment is confirmed.' : 'We are confirming your payment.' }}</h1>

                        @if($isApproved)
                            <p class="lead">Your payment of <strong>&#8369;{{ number_format((float) $payment->amount, 2) }}</strong> has been applied to your loan. Your updated balance is ready to view.</p>
                            <div class="state-note" role="status">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke-linecap="round" stroke-width="1.8"/></svg>
                                <span>You can keep this page as a payment reference. A record is also available in your payment history.</span>
                            </div>
                        @else
                            <p class="lead">You returned safely from PayMongo. We are waiting for its signed confirmation before applying <strong>&#8369;{{ number_format((float) $payment->amount, 2) }}</strong> to your loan.</p>
                            <div class="state-note" role="status">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v4m0 4h.01M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke-linecap="round" stroke-width="1.8"/></svg>
                                <span>No action is needed. Please avoid making the same payment again while confirmation is in progress.</span>
                            </div>
                        @endif

                        <div class="actions" aria-label="Next steps">
                            @if($isApproved)
                                <a class="button button-primary" href="{{ route('payments.receipt', $payment) }}" download="payment-receipt-{{ $payment->id }}.pdf" data-no-transition>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 19h14" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                                    Download PNG receipt
                                </a>
                            @endif
                            <a class="button button-primary" href="{{ route('dashboard') }}">
                                View dashboard
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9"/></svg>
                            </a>
                            <a class="button button-secondary" href="{{ route('payments.index') }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16v11H4zM4 10h16M8 15h3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/></svg>
                                Payment history
                            </a>
                        </div>
                    </section>

                    <aside class="receipt-panel" aria-label="Payment details">
                        <div class="receipt">
                            <div class="receipt-header">
                                <div class="receipt-title">Payment summary</div>
                                <div class="status-pill"><span class="status-dot"></span>{{ $isApproved ? 'Confirmed' : 'Processing' }}</div>
                            </div>

                            <div class="amount-block">
                                <div class="amount-label">{{ $isApproved ? 'Amount paid' : 'Payment amount' }}</div>
                                <div class="amount-value">&#8369;{{ number_format((float) $payment->amount, 2) }}</div>
                                <div class="amount-subtitle">{{ strtoupper($payment->currency ?: 'PHP') }}</div>
                            </div>

                            <dl class="details">
                                <div class="detail-row">
                                    <dt>Loan account</dt>
                                    <dd>{{ $loanLabel }}<span>{{ $loanCode }}</span></dd>
                                </div>
                                <div class="detail-row">
                                    <dt>Payment method</dt>
                                    <dd>{{ $payment->method_label }}</dd>
                                </div>
                                <div class="detail-row">
                                    <dt>Reference</dt>
                                    <dd>{{ $reference }}</dd>
                                </div>
                                <div class="detail-row">
                                    <dt>{{ $isApproved ? 'Confirmed on' : 'Submitted on' }}</dt>
                                    <dd>{{ $paymentDate?->format('M j, Y') ?? 'Not available' }}<span>{{ $paymentDate?->format('g:i A') }} PHT</span></dd>
                                </div>
                            </dl>

                            <div class="receipt-footer">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke-width="1.8"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke-linecap="round" stroke-width="1.8"/></svg>
                                Payment information is securely recorded in your account.
                            </div>
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
