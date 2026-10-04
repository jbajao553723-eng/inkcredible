<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inkcredible makes loan applications, balances, schedules, and payments easier to understand and manage.">
    <title>Inkcredible | Clear, responsible lending</title>
    <style>
        :root { --navy:#17233b; --navy-2:#22385f; --gold:#c7832f; --gold-soft:#f4e2c7; --ink:#1c2638; --muted:#657084; --paper:#fbfaf7; --white:#fff; --line:#e5e2da; --green:#15724c; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; line-height:1.6; }
        a { color:inherit; }
        .shell { width:min(1160px,calc(100% - 40px)); margin:0 auto; }
        .nav { position:absolute; z-index:2; inset:0 0 auto; }
        .nav-inner { height:82px; display:flex; align-items:center; justify-content:space-between; gap:24px; color:var(--white); }
        .brand { display:inline-flex; align-items:center; gap:11px; font-size:18px; font-weight:750; text-decoration:none; letter-spacing:-.02em; }
        .brand-mark { display:block; width:34px; height:34px; flex:0 0 34px; object-fit:cover; background:#fff; border-radius:10px 10px 10px 3px; box-shadow:0 6px 16px rgba(239,68,68,.18); }
        .nav-links { display:flex; align-items:center; gap:26px; }
        .nav-link { color:#d9e0ec; font-size:14px; font-weight:600; text-decoration:none; }
        .nav-link:hover,.nav-link:focus-visible { color:#fff; }
        .button { display:inline-flex; min-height:46px; align-items:center; justify-content:center; padding:0 20px; border:1px solid transparent; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none; transition:transform .2s ease,background .2s ease; }
        .button:hover { transform:translateY(-2px); }
        .button-primary { color:var(--navy); background:var(--gold-soft); }
        .button-outline { color:#fff; border-color:rgba(255,255,255,.35); background:rgba(255,255,255,.04); }
        .hero { position:relative; overflow:hidden; min-height:690px; color:#fff; background:var(--navy); }
        .hero::before { position:absolute; content:""; width:540px; height:540px; right:-120px; top:110px; border:1px solid rgba(244,226,199,.18); border-radius:50%; box-shadow:0 0 0 90px rgba(244,226,199,.035),0 0 0 180px rgba(244,226,199,.02); }
        .hero::after { position:absolute; content:""; right:10%; bottom:-160px; width:340px; height:460px; background:linear-gradient(145deg,rgba(199,131,47,.3),rgba(199,131,47,0)); transform:rotate(24deg); border-radius:180px; }
        .hero-grid { position:relative; z-index:1; display:grid; min-height:690px; grid-template-columns:1.12fr .88fr; gap:76px; align-items:center; padding:120px 0 62px; }
        .eyebrow { color:#e6b775; font-size:12px; font-weight:800; letter-spacing:.17em; text-transform:uppercase; }
        h1 { max-width:720px; margin:18px 0 22px; font-family:Georgia,"Times New Roman",serif; font-size:clamp(44px,5.5vw,74px); font-weight:600; letter-spacing:-.045em; line-height:1.02; }
        .hero-copy { max-width:610px; margin:0; color:#cbd4e2; font-size:18px; }
        .hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:32px; }
        .trust { display:flex; flex-wrap:wrap; gap:22px; margin-top:34px; color:#aeb9ca; font-size:12px; font-weight:650; }
        .trust span::before { content:"✓"; margin-right:7px; color:#e6b775; }
        .account-card { position:relative; padding:28px; color:var(--ink); background:rgba(255,255,255,.97); border-radius:20px; box-shadow:0 30px 70px rgba(4,10,24,.3); }
        .card-kicker { color:var(--muted); font-size:11px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .balance { margin:5px 0 22px; color:var(--navy); font-family:Georgia,serif; font-size:34px; }
        .progress-label { display:flex; justify-content:space-between; color:var(--muted); font-size:12px; }
        .progress { height:8px; margin:9px 0 26px; overflow:hidden; background:#e8ebf0; border-radius:99px; }
        .progress span { display:block; width:64%; height:100%; background:var(--gold); border-radius:inherit; }
        .card-row { display:flex; justify-content:space-between; padding:14px 0; border-top:1px solid var(--line); font-size:13px; }
        .card-row span { color:var(--muted); }
        .card-row strong { color:var(--navy); }
        .card-status { display:inline-flex; margin-top:18px; padding:7px 10px; color:var(--green); background:#e8f5ef; border-radius:999px; font-size:11px; font-weight:800; }
        .section { padding:96px 0; }
        .section-head { max-width:680px; margin-bottom:42px; }
        h2 { margin:10px 0 14px; color:var(--navy); font-family:Georgia,serif; font-size:clamp(34px,4vw,48px); letter-spacing:-.035em; line-height:1.08; }
        .section-copy { margin:0; color:var(--muted); font-size:16px; }
        .features { display:grid; grid-template-columns:repeat(3,1fr); border:1px solid var(--line); border-radius:18px; overflow:hidden; background:#fff; }
        .feature { min-height:250px; padding:32px; border-right:1px solid var(--line); }
        .feature:last-child { border:0; }
        .feature-number { color:var(--gold); font:700 13px Georgia,serif; }
        .feature h3 { margin:42px 0 9px; color:var(--navy); font-family:Georgia,serif; font-size:23px; }
        .feature p { margin:0; color:var(--muted); font-size:14px; }
        .steps-section { color:#fff; background:var(--navy-2); }
        .steps-section h2 { color:#fff; }
        .steps-section .section-copy { color:#cbd4e2; }
        .steps { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; counter-reset:step; }
        .step { padding:25px 26px; border:1px solid rgba(255,255,255,.14); border-radius:14px; counter-increment:step; }
        .step::before { content:"0" counter(step); color:#e6b775; font:700 12px Georgia,serif; }
        .step h3 { margin:20px 0 7px; font-family:Georgia,serif; font-size:22px; }
        .step p { margin:0; color:#cbd4e2; font-size:14px; }
        .cta { display:flex; align-items:center; justify-content:space-between; gap:32px; padding:42px 46px; background:#fff; border:1px solid var(--line); border-radius:18px; }
        .cta h2 { margin:0 0 8px; font-size:34px; }
        .cta p { margin:0; color:var(--muted); }
        .cta .button { flex:none; color:#fff; background:var(--navy); }
        footer { padding:28px 0; color:var(--muted); border-top:1px solid var(--line); font-size:12px; }
        .footer-inner { display:flex; align-items:center; justify-content:space-between; gap:20px; }
        .footer-links { display:flex; gap:20px; }
        .footer-links a { text-decoration:none; }
        :focus-visible { outline:3px solid #e6b775; outline-offset:3px; }
        @media (max-width:900px) { .hero-grid { grid-template-columns:1fr; gap:38px; padding-top:140px; } .hero { min-height:auto; } .account-card { max-width:560px; } .features,.steps { grid-template-columns:1fr; } .feature { min-height:auto; border-right:0; border-bottom:1px solid var(--line); } .feature h3 { margin-top:20px; } }
        @media (max-width:640px) { .shell { width:min(100% - 28px,1160px); } .nav-inner { height:72px; } .nav-links .nav-link { display:none; } .nav-links { gap:8px; } .nav-links .button { min-height:40px; padding:0 13px; } .hero-grid { padding-top:116px; } h1 { font-size:43px; } .hero-copy { font-size:16px; } .account-card { padding:22px; } .section { padding:70px 0; } .cta { align-items:flex-start; flex-direction:column; padding:30px; } .footer-inner { align-items:flex-start; flex-direction:column; } }
        @media (prefers-reduced-motion:reduce) { html { scroll-behavior:auto; } .button { transition:none; } }
        @include('partials.motion-styles')
    </style>
    @vite('resources/js/app.js')
</head>
<body>
<header class="nav">
    <div class="shell nav-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Inkcredible home"><x-brand-mark /><span>Inkcredible</span></a>
        <nav class="nav-links" aria-label="Primary navigation">
            <a class="nav-link" href="#features">Why Inkcredible</a><a class="nav-link" href="#how-it-works">How it works</a>
            @auth<a class="button button-primary" href="{{ route('dashboard') }}">Go to dashboard</a>
            @else<a class="nav-link" href="{{ route('login') }}">Sign in</a><a class="button button-primary" href="{{ route('register') }}">Create account</a>@endauth
        </nav>
    </div>
</header>

<main>
    <section class="hero">
        <div class="shell hero-grid">
            <div>
                <div class="eyebrow">Clear terms. Confident decisions.</div>
                <h1>Lending that keeps you informed.</h1>
                <p class="hero-copy">Apply for a loan, follow every due date, and keep your payment history organized from one secure account.</p>
                <div class="hero-actions">
                    @auth<a class="button button-primary" href="{{ route('dashboard') }}">Open your dashboard</a>
                    @else<a class="button button-primary" href="{{ route('register') }}">Get started</a><a class="button button-outline" href="{{ route('login') }}">Sign in to your account</a>@endauth
                </div>
                <div class="trust" aria-label="Service highlights"><span>Transparent schedules</span><span>Secure payments</span><span>Real-time status</span></div>
            </div>
            <aside class="account-card" aria-label="Sample loan overview">
                <div class="card-kicker">Loan overview</div><div class="balance">PHP 12,450.00</div>
                <div class="progress-label"><span>Repayment progress</span><strong>64%</strong></div><div class="progress" aria-hidden="true"><span></span></div>
                <div class="card-row"><span>Next due date</span><strong>September 30</strong></div><div class="card-row"><span>Scheduled payment</span><strong>PHP 2,100.00</strong></div><div class="card-row"><span>Payments recorded</span><strong>8 of 12</strong></div>
                <div class="card-status">Account on schedule</div>
            </aside>
        </div>
    </section>

    <section class="section" id="features"><div class="shell">
        <div class="section-head"><div class="eyebrow">Everything in one view</div><h2>Less guesswork at every step.</h2><p class="section-copy">Inkcredible brings the details that matter together, from application status to the final payment.</p></div>
        <div class="features">
            <article class="feature"><div class="feature-number">01</div><h3>Understand your balance</h3><p>See principal, scheduled payments, paid amounts, and any applicable penalties without chasing separate records.</p></article>
            <article class="feature"><div class="feature-number">02</div><h3>Follow every due date</h3><p>Review your full repayment schedule and know which installments are pending, partial, overdue, or complete.</p></article>
            <article class="feature"><div class="feature-number">03</div><h3>Pay with confidence</h3><p>Submit cash-payment proof or continue through secure online checkout, then track verification from your account.</p></article>
        </div>
    </div></section>

    <section class="section steps-section" id="how-it-works"><div class="shell">
        <div class="section-head"><div class="eyebrow">A straightforward process</div><h2>From application to payoff.</h2><p class="section-copy">Each step stays visible, with clear requirements and a record you can return to.</p></div>
        <div class="steps">
            <article class="step"><h3>Create and verify</h3><p>Set up your profile and submit the required identity and income information for review.</p></article>
            <article class="step"><h3>Apply and review</h3><p>Choose an available loan type, review the terms, and follow the status of your application.</p></article>
            <article class="step"><h3>Repay and track</h3><p>Make payments through the available methods and monitor your balance and history in one place.</p></article>
        </div>
    </div></section>

    <section class="section"><div class="shell"><div class="cta">
        <div><h2>Ready for a clearer way to borrow?</h2><p>Create an account to review available loan options and begin verification.</p></div>
        @auth<a class="button" href="{{ route('dashboard') }}">Go to dashboard</a>@else<a class="button" href="{{ route('register') }}">Create your account</a>@endauth
    </div></div></section>
</main>

<footer><div class="shell footer-inner"><span>&copy; {{ now()->year }} Inkcredible Lending Management System</span><div class="footer-links"><a href="{{ route('terms') }}">Terms &amp; conditions</a><a href="{{ route('loan.terms') }}">Loan terms</a></div></div></footer>
</body>
</html>
