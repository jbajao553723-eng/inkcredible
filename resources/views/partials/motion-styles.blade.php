html.motion-enabled .motion-reveal {
    opacity: 0;
    transform: translateY(18px) scale(.985);
    transition:
        opacity .58s cubic-bezier(.22, 1, .36, 1),
        transform .58s cubic-bezier(.22, 1, .36, 1);
    transition-delay: calc(var(--motion-order, 0) * 55ms);
}

html.motion-enabled.motion-in .motion-reveal { opacity: 1; transform: translateY(0) scale(1); }
html.motion-enabled.motion-out body { opacity: 0; transform: translateY(5px); }
body { transition: opacity .15s ease, transform .15s ease; }

html.motion-enabled .motion-row {
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .4s ease, transform .4s cubic-bezier(.22, 1, .36, 1), background-color .15s ease;
    transition-delay: calc(var(--motion-row-order, 0) * 28ms + 180ms);
}

html.motion-enabled.motion-in .motion-row { opacity: 1; transform: translateY(0); }

.button, .btn, .submit-button, .save-button, .quick-card, .nav-link, .panel-link, .verification-banner a {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    -webkit-tap-highlight-color: transparent;
}

.password-toggle {
    overflow: hidden;
    isolation: isolate;
    -webkit-tap-highlight-color: transparent;
}

.button:active, .btn:active, .submit-button:active, .save-button:active, .quick-card:active, .nav-link:active { transform: scale(.975); }
.click-ripple { position: absolute; z-index: 0; pointer-events: none; background: currentColor; border-radius: 50%; opacity: .13; transform: scale(0); animation: click-ripple .55s ease-out forwards; }
.button > :not(.click-ripple), .submit-button > :not(.click-ripple), .quick-card > :not(.click-ripple), .nav-link > :not(.click-ripple) { position: relative; z-index: 1; }
.button-primary .click-ripple, .submit-button .click-ripple, .button-glass .click-ripple { background: #fff; }

.submit-button.is-loading::after, .save-button.is-loading::after, .button.is-loading::after {
    width: 15px;
    height: 15px;
    border: 2px solid rgba(255, 255, 255, .42);
    border-top-color: #fff;
    border-radius: 50%;
    content: '';
    animation: loading-spin .65s linear infinite;
}

.brand-panel { --pointer-x: 0px; --pointer-y: 0px; }
.brand-panel::before { transform: translate(var(--pointer-x), var(--pointer-y)); transition: transform .45s ease-out; }
.brand-panel::after { transform: translate(calc(0px - var(--pointer-x)), calc(0px - var(--pointer-y))); transition: transform .55s ease-out; }
.brand-mark { animation: brand-breathe 4.5s ease-in-out infinite; }
.stat-card, .panel, .balance-card, .quick-card { transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
.stat-card:hover, .panel:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(16, 24, 40, .08); }
.stat-card:hover .stat-icon, .next-card:hover .next-icon, .quick-card:hover .quick-icon { transform: rotate(-5deg) scale(1.08); }
.stat-icon, .next-icon, .quick-icon { transition: transform .28s cubic-bezier(.22, 1, .36, 1); }
.progress-bar, .status-fill {
    will-change: width;
    transition: width 1s cubic-bezier(.22, 1, .36, 1);
}

html.motion-enabled.motion-in .stat-card.motion-reveal:hover,
html.motion-enabled.motion-in .quick-card.motion-reveal:hover,
html.motion-enabled.motion-in .panel.motion-reveal:hover { transform: translateY(-2px) scale(1); }

@keyframes click-ripple { to { opacity: 0; transform: scale(1); } }
@keyframes loading-spin { to { transform: rotate(360deg); } }
@keyframes brand-breathe { 0%, 100% { box-shadow: 0 8px 20px rgba(99, 102, 241, .25); } 50% { box-shadow: 0 10px 30px rgba(129, 140, 248, .48); } }

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; transition-delay: 0ms !important; }
    html.motion-enabled .motion-reveal { opacity: 1; transform: none; }
    html.motion-enabled .motion-row { opacity: 1; transform: none; }
}
