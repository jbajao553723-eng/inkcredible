@php
    $textScale = match (data_get(auth()->user()?->ui_preferences, 'text_size', 'normal')) {
        'small' => .875,
        'large' => 1.125,
        'extra-large' => 1.25,
        default => 1,
    };
@endphp
html { --app-text-scale:{{ $textScale }}; font-size:calc(16px * var(--app-text-scale)); scroll-behavior: smooth; }

body {
    animation: motion-first-paint .68s ease-out 45ms backwards;
}

html.motion-enabled body {
    opacity: 0;
    transform: translateY(5px) scale(.997);
    transition: opacity .22s ease, transform .22s ease, filter .22s ease;
}

html.motion-enabled.motion-in body {
    opacity: 1;
    filter: none;
    transform: none;
}

html.motion-enabled.motion-out body {
    opacity: 0;
    filter: blur(1px);
    transform: translateY(5px) scale(.997);
}

html.motion-enabled .motion-reveal {
    opacity: 0;
    filter: blur(2px);
    transform: translateY(18px) scale(.992);
    transition:
        opacity .78s cubic-bezier(.22, 1, .36, 1),
        transform .78s cubic-bezier(.22, 1, .36, 1),
        filter .62s ease;
    transition-delay: calc(var(--motion-order, 0) * 62ms + 70ms);
    will-change: opacity, transform;
}

html.motion-enabled .motion-reveal.motion-from-left { transform: translateX(-18px); }
html.motion-enabled .motion-reveal.motion-scale { transform: translateY(12px) scale(.975); }

html.motion-enabled.motion-in .motion-reveal {
    opacity: 1;
    filter: blur(0);
    transform: translate(0) scale(1);
}

html.motion-enabled .motion-scroll {
    opacity: 0;
    filter: blur(1.5px);
    transform: translateY(24px);
    transition:
        opacity .62s cubic-bezier(.22, 1, .36, 1),
        transform .62s cubic-bezier(.22, 1, .36, 1),
        filter .5s ease;
    transition-delay: calc(var(--motion-child-order, 0) * 42ms);
    will-change: opacity, transform;
}

html.motion-enabled .motion-scroll.motion-visible {
    opacity: 1;
    filter: blur(0);
    transform: translateY(0);
}

html.motion-enabled .motion-row {
    opacity: 0;
    transform: translateY(10px);
    transition:
        opacity .42s ease,
        transform .42s cubic-bezier(.22, 1, .36, 1),
        background-color .16s ease,
        box-shadow .16s ease;
    transition-delay: calc(var(--motion-row-order, 0) * 28ms + 180ms);
}

html.motion-enabled.motion-in .motion-row { opacity: 1; transform: translateY(0); }

.page-transition-bar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 10000;
    width: 100%;
    height: 3px;
    pointer-events: none;
    background: linear-gradient(90deg, #6366f1, #8b5cf6, #22c55e);
    box-shadow: 0 2px 12px rgba(99, 102, 241, .4);
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left;
}

.page-transition-bar.is-active {
    opacity: 1;
    animation: page-transition-progress .9s cubic-bezier(.22, 1, .36, 1) forwards;
}

.motion-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    width: 100%;
    height: 2px;
    pointer-events: none;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
    box-shadow: 0 1px 7px rgba(99, 102, 241, .32);
    opacity: 0;
    transform: scaleX(0);
    transform-origin: left;
    transition: opacity .18s ease;
}

.motion-scroll-progress.is-visible { opacity: 1; }

.button,
.btn,
.submit-button,
.save-button,
.logout-button,
.quick-card,
.quick-amount,
.full-balance-button,
.method-card,
.loan-card,
.loan-type-card,
.settings-tab,
.nav-link,
.panel-link,
.verification-banner a,
[role="button"] {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    -webkit-tap-highlight-color: transparent;
}

.password-toggle,
.photo-upload {
    overflow: hidden;
    isolation: isolate;
    -webkit-tap-highlight-color: transparent;
}

.button,
.btn,
.submit-button,
.save-button,
.logout-button,
.quick-amount,
.full-balance-button,
.settings-tab,
.nav-link,
.panel-link,
.verification-banner a,
.password-toggle,
.photo-upload {
    transition:
        color .18s ease,
        background-color .18s ease,
        border-color .18s ease,
        box-shadow .22s ease,
        transform .2s cubic-bezier(.22, 1, .36, 1);
}

.topbar > *,
.panel-header > *,
.history-header > *,
.receipt-header > *,
.form-row > *,
.form-grid > *,
.stats-grid > *,
.overview-grid > *,
.workspace-grid > *,
.profile-layout > *,
.detail-layout > *,
.application-grid > * { min-width: 0; }

.button,
.btn,
.submit-button,
.save-button,
.logout-button { max-width: 100%; }

.button:active,
.btn:active,
.submit-button:active,
.save-button:active,
.logout-button:active,
.quick-card:active,
.quick-amount:active,
.full-balance-button:active,
.method-card:active,
.loan-card:active,
.loan-type-card:active,
.settings-tab:active,
.nav-link:active,
.photo-upload:active { transform: scale(.972); }

.click-ripple {
    position: absolute;
    z-index: 0;
    pointer-events: none;
    background: currentColor;
    border-radius: 50%;
    opacity: .14;
    transform: scale(0);
    animation: click-ripple .58s ease-out forwards;
}

.button > :not(.click-ripple),
.btn > :not(.click-ripple),
.submit-button > :not(.click-ripple),
.save-button > :not(.click-ripple),
.quick-card > :not(.click-ripple),
.method-card > :not(.click-ripple),
.loan-card > :not(.click-ripple),
.loan-type-card > :not(.click-ripple),
.settings-tab > :not(.click-ripple),
.nav-link > :not(.click-ripple) { position: relative; z-index: 1; }

.button-primary .click-ripple,
.submit-button .click-ripple,
.save-button .click-ripple,
.button-glass .click-ripple,
.btn-primary .click-ripple,
.btn-danger .click-ripple { background: #fff; }

.submit-button.is-loading,
.save-button.is-loading,
.button.is-loading,
.btn.is-loading { cursor: wait; }

.submit-button.is-loading::after,
.save-button.is-loading::after,
.button.is-loading::after,
.btn.is-loading::after {
    width: 15px;
    height: 15px;
    flex: 0 0 15px;
    border: 2px solid rgba(255, 255, 255, .42);
    border-top-color: currentColor;
    border-radius: 50%;
    content: '';
    animation: loading-spin .65s linear infinite;
}

.brand-panel { --pointer-x: 0px; --pointer-y: 0px; }
.brand-panel::before { transform: translate(var(--pointer-x), var(--pointer-y)); transition: transform .45s ease-out; }
.brand-panel::after { transform: translate(calc(0px - var(--pointer-x)), calc(0px - var(--pointer-y))); transition: transform .55s ease-out; }
.brand-mark { animation: brand-breathe 4.5s ease-in-out infinite; }

.stat-card,
.overview-card,
.panel,
.balance-card,
.next-card,
.quick-card,
.loan-card,
.method-card,
.loan-type-card,
.profile-summary,
.verification-notice,
.form-section,
.detail-item,
.receipt,
.result-card,
.settings-tab,
.document-card {
    transition:
        transform .25s cubic-bezier(.22, 1, .36, 1),
        box-shadow .25s ease,
        border-color .22s ease,
        background-color .22s ease;
}

.stat-card:hover,
.overview-card:hover,
.quick-card:hover,
.next-card:hover,
.profile-summary:hover,
.document-card:hover { transform: translateY(-3px); box-shadow: 0 14px 32px rgba(16, 24, 40, .085); }

.loan-card:hover,
.method-card:hover,
.loan-type-card:hover,
.settings-tab:hover { transform: translateY(-2px); box-shadow: 0 11px 25px rgba(16, 24, 40, .075); }

.panel:hover,
.form-section:hover,
.detail-item:hover,
.receipt:hover { border-color: #d8dce5; box-shadow: 0 8px 24px rgba(16, 24, 40, .055); }

html.motion-enabled.motion-in .stat-card.motion-reveal:hover,
html.motion-enabled.motion-in .overview-card.motion-reveal:hover,
html.motion-enabled.motion-in .quick-card.motion-reveal:hover,
html.motion-enabled.motion-in .next-card.motion-reveal:hover,
html.motion-enabled.motion-in .profile-summary.motion-reveal:hover,
html.motion-enabled.motion-in .document-card.motion-reveal:hover { transform: translateY(-3px); }

html.motion-enabled.motion-in .loan-card.motion-reveal:hover,
html.motion-enabled.motion-in .method-card.motion-reveal:hover,
html.motion-enabled.motion-in .loan-type-card.motion-reveal:hover,
html.motion-enabled.motion-in .settings-tab.motion-reveal:hover { transform: translateY(-2px); }

.stat-icon,
.overview-icon,
.next-icon,
.quick-icon,
.type-icon,
.tab-icon,
.account-icon,
.proof-upload-icon,
.status-icon {
    transition: transform .3s cubic-bezier(.22, 1, .36, 1), background-color .2s ease, color .2s ease;
}

.stat-card:hover .stat-icon,
.overview-card:hover .overview-icon,
.next-card:hover .next-icon,
.quick-card:hover .quick-icon,
.loan-type-card:hover .type-icon,
.settings-tab:hover .tab-icon { transform: rotate(-5deg) scale(1.08); }

.form-group,
.amount-control,
.account-select,
.input-group,
.field,
.proof-upload { transition: transform .2s ease, filter .2s ease; }

.form-group.has-focus,
.amount-control.has-focus,
.account-select.has-focus,
.input-group.has-focus,
.field.has-focus,
.proof-upload.has-focus { transform: translateY(-1px); }

input,
select,
textarea,
.form-control,
.account-select-control {
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease, transform .18s ease;
}

input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
select:focus,
textarea:focus { transform: translateY(-1px); }

.progress-bar,
.status-fill,
.balance-progress-fill,
.completion-fill {
    will-change: width;
    transition: width 1.05s cubic-bezier(.22, 1, .36, 1);
}

.nav-link::after {
    position: absolute;
    bottom: 7px;
    left: 12px;
    width: 0;
    height: 2px;
    background: currentColor;
    border-radius: 999px;
    content: '';
    opacity: .4;
    transition: width .24s cubic-bezier(.22, 1, .36, 1);
}

.nav-link:hover::after,
.nav-link.active::after,
.nav-link[aria-current="page"]::after { width: 18px; }

.badge,
.status-pill,
.due-pill,
.attention-pill,
.today-pill { transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease; }

.badge:hover,
.status-pill:hover,
.due-pill:hover,
.attention-pill:hover,
.today-pill:hover { transform: translateY(-1px); }

.attention-pill::before,
.status-pill .status-dot,
.due-pill.soon::before { animation: status-pulse 2.2s ease-in-out infinite; }

.alert,
.verification-banner { animation: alert-arrive .48s cubic-bezier(.22, 1, .36, 1) both; }

.motion-dynamic-in { animation: dynamic-arrive .38s cubic-bezier(.22, 1, .36, 1) both; }
.motion-selected { animation: selected-pulse .38s cubic-bezier(.22, 1, .36, 1); }

dialog[open] { animation: motion-dialog-backdrop .24s ease-out both; }
dialog[open]::backdrop { animation: motion-backdrop-in .24s ease-out both; }
dialog[open] > * { animation: motion-dialog-in .34s cubic-bezier(.22, 1, .36, 1) both; }

.modal.show .modal-dialog { animation: motion-dialog-in .34s cubic-bezier(.22, 1, .36, 1) both; }
.modal-backdrop { --bs-backdrop-bg: #344054; --bs-backdrop-opacity: .32; backdrop-filter: blur(2px); }
.modal-backdrop.show { animation: bootstrap-backdrop-in .24s ease-out both; }

/* A transformed body becomes the containing block for fixed overlays. Keep
   confirmation dialogs attached to the viewport, including during page motion. */
body.modal-open,
body:has(dialog[open]) {
    animation: none;
    filter: none !important;
    transform: none !important;
}
.dropdown-menu.show { animation: dropdown-in .2s cubic-bezier(.22, 1, .36, 1) both; transform-origin: top; }

html.motion-enabled.motion-in .status-icon.motion-reveal,
html.motion-enabled.motion-in .message-panel .status-icon { animation: success-pop .62s .22s cubic-bezier(.22, 1, .36, 1) both; }

tbody tr:not([hidden]):hover { box-shadow: inset 3px 0 0 rgba(99, 102, 241, .45); }

/* The loan review screens are dense, so they use a stronger, deliberate
   opening sequence in addition to the universal page transition. */
.admin-loans-page .topbar {
    animation: admin-loan-enter .72s cubic-bezier(.22, 1, .36, 1) 90ms backwards;
}

.admin-loans-page .stats-grid > .stat-card {
    animation: admin-loan-enter .68s cubic-bezier(.22, 1, .36, 1) backwards;
}

.admin-loans-page .stats-grid > .stat-card:nth-child(1) { animation-delay: 210ms; }
.admin-loans-page .stats-grid > .stat-card:nth-child(2) { animation-delay: 280ms; }
.admin-loans-page .stats-grid > .stat-card:nth-child(3) { animation-delay: 350ms; }
.admin-loans-page .stats-grid > .stat-card:nth-child(4) { animation-delay: 420ms; }

.admin-loan-queue-page .queue-panel,
.admin-loan-detail-page .detail-layout > * {
    animation: admin-loan-panel-enter .78s cubic-bezier(.22, 1, .36, 1) backwards;
}

.admin-loan-queue-page .queue-panel { animation-delay: 510ms; }
.admin-loan-detail-page .detail-layout > :first-child { animation-delay: 500ms; }
.admin-loan-detail-page .detail-layout > :last-child { animation-delay: 590ms; }

.admin-loan-queue-page .status-tabs > *,
.admin-loan-queue-page .queue-toolbar > * {
    animation: admin-loan-control-enter .5s cubic-bezier(.22, 1, .36, 1) backwards;
}

.admin-loan-queue-page .status-tabs > :nth-child(1) { animation-delay: 650ms; }
.admin-loan-queue-page .status-tabs > :nth-child(2) { animation-delay: 690ms; }
.admin-loan-queue-page .status-tabs > :nth-child(3) { animation-delay: 730ms; }
.admin-loan-queue-page .status-tabs > :nth-child(4) { animation-delay: 770ms; }
.admin-loan-queue-page .status-tabs > :nth-child(5) { animation-delay: 810ms; }
.admin-loan-queue-page .queue-toolbar > * { animation-delay: 780ms; }

.admin-loan-queue-page .loan-table tbody tr {
    animation: admin-loan-row-enter .48s cubic-bezier(.22, 1, .36, 1) backwards;
    animation-delay: calc(840ms + var(--motion-row-order, 0) * 34ms);
}

@keyframes click-ripple { to { opacity: 0; transform: scale(1); } }
@keyframes motion-first-paint { from { opacity: 0; filter: blur(3px); transform: translateY(11px) scale(.994); } to { opacity: 1; filter: blur(0); transform: translateY(0) scale(1); } }
@keyframes admin-loan-enter { from { opacity: 0; filter: blur(2px); transform: translateY(18px) scale(.985); } to { opacity: 1; filter: blur(0); transform: translateY(0) scale(1); } }
@keyframes admin-loan-panel-enter { from { opacity: 0; filter: blur(2px); transform: translateY(24px); } to { opacity: 1; filter: blur(0); transform: translateY(0); } }
@keyframes admin-loan-control-enter { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes admin-loan-row-enter { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
@keyframes loading-spin { to { transform: rotate(360deg); } }
@keyframes brand-breathe { 0%, 100% { box-shadow: 0 8px 20px rgba(99, 102, 241, .25); } 50% { box-shadow: 0 10px 30px rgba(129, 140, 248, .48); } }
@keyframes status-pulse { 0%, 100% { box-shadow: 0 0 0 0 currentColor; } 50% { box-shadow: 0 0 0 5px transparent; } }
@keyframes alert-arrive { from { opacity: 0; transform: translateY(-9px); } to { opacity: 1; transform: translateY(0); } }
@keyframes dynamic-arrive { from { opacity: 0; transform: translateY(8px) scale(.99); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes selected-pulse { 0% { transform: scale(1); } 45% { transform: scale(.985); } 100% { transform: scale(1); } }
@keyframes page-transition-progress { 0% { transform: scaleX(0); } 65% { transform: scaleX(.76); } 100% { transform: scaleX(.94); } }
@keyframes motion-backdrop-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes bootstrap-backdrop-in { from { opacity: 0; } to { opacity: var(--bs-backdrop-opacity, .46); } }
@keyframes motion-dialog-backdrop { from { opacity: 0; } to { opacity: 1; } }
@keyframes motion-dialog-in { from { opacity: 0; transform: translateY(18px) scale(.975); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes dropdown-in { from { opacity: 0; transform: translateY(-5px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes success-pop { 0% { opacity: 0; transform: scale(.72) rotate(-8deg); } 65% { opacity: 1; transform: scale(1.08) rotate(2deg); } 100% { opacity: 1; transform: scale(1) rotate(0); } }

@media (max-width: 760px) {
    html.motion-enabled .motion-reveal { transform: translateY(12px) scale(.995); }
    html.motion-enabled .motion-reveal.motion-from-left { transform: translateY(12px); }
    html.motion-enabled .motion-scroll { transform: translateY(16px); }
}

@media (hover: none) {
    .stat-card:hover,
    .overview-card:hover,
    .quick-card:hover,
    .next-card:hover,
    .profile-summary:hover,
    .document-card:hover,
    .loan-card:hover,
    .method-card:hover,
    .loan-type-card:hover,
    .settings-tab:hover { transform: none; box-shadow: inherit; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        scroll-behavior: auto !important;
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
        transition-delay: 0ms !important;
    }

    html.motion-enabled body,
    html.motion-enabled .motion-reveal,
    html.motion-enabled .motion-row,
    html.motion-enabled .motion-scroll {
        opacity: 1;
        filter: none;
        transform: none;
    }

    .page-transition-bar,
    .motion-scroll-progress { display: none; }

    .form-group.has-focus,
    .amount-control.has-focus,
    .account-select.has-focus,
    .input-group.has-focus,
    .field.has-focus,
    .proof-upload.has-focus { transform: none; }
}
