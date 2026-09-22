const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

document.documentElement.classList.add('motion-enabled');

const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
};

const revealPage = () => {
    const selectors = document.querySelector('.auth-page')
        ? [
            '.brand-panel .brand',
            '.brand-eyebrow',
            '.brand-title',
            '.brand-description',
            '.feature-list li',
            '.brand-footer',
            '.form-shell > *',
        ]
        : [
            '.sidebar',
            '.topbar',
            '.alert',
            '.verification-banner',
            '.stat-card',
            '.balance-card',
            '.next-card',
            '.metric-layout > *',
            '.section-stack > *',
            '.quick-card',
        ];

    const elements = [...new Set(selectors.flatMap((selector) => [...document.querySelectorAll(selector)]))];

    elements.forEach((element, index) => {
        element.classList.add('motion-reveal');
        element.style.setProperty('--motion-order', Math.min(index, 12));
    });

    requestAnimationFrame(() => requestAnimationFrame(() => {
        document.documentElement.classList.add('motion-in');
    }));
};

const animateNumber = (element) => {
    if (reducedMotion.matches) return;

    const original = element.textContent.trim();
    const match = original.match(/-?[\d,]+(?:\.\d+)?/);

    if (!match) return;

    const target = Number(match[0].replaceAll(',', ''));
    if (!Number.isFinite(target) || target === 0) return;

    const decimals = match[0].includes('.') ? match[0].split('.')[1].length : 0;
    const prefix = original.slice(0, match.index);
    const suffix = original.slice(match.index + match[0].length);
    const formatter = new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
    const duration = 850;
    let startTime;

    const update = (time) => {
        startTime ??= time;
        const progress = Math.min((time - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        element.textContent = `${prefix}${formatter.format(target * eased)}${suffix}`;

        if (progress < 1) requestAnimationFrame(update);
        else element.textContent = original;
    };

    requestAnimationFrame(update);
};

const animateDashboardData = () => {
    const progressBars = document.querySelectorAll('.progress-bar, .status-fill');

    progressBars.forEach((bar) => {
        const targetWidth = bar.style.width;
        if (!targetWidth) return;
        bar.style.setProperty('--target-width', targetWidth);
        bar.style.width = '0';
    });

    window.setTimeout(() => {
        progressBars.forEach((bar) => {
            bar.style.width = bar.style.getPropertyValue('--target-width');
        });
        document.querySelectorAll('.stat-value, .balance-value').forEach(animateNumber);
    }, reducedMotion.matches ? 0 : 280);
};

const addRipple = (event) => {
    if (reducedMotion.matches) return;

    const target = event.target.closest('.button, .submit-button, .quick-card, .nav-link, .panel-link, .verification-banner a, .password-toggle');
    if (!target || target.matches(':disabled')) return;

    const rect = target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height) * 1.8;
    const ripple = document.createElement('span');

    ripple.className = 'click-ripple';
    ripple.style.width = ripple.style.height = `${size}px`;
    ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
    ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
    target.append(ripple);
    ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
};

const enablePageTransitions = () => {
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin || destination.href === window.location.href || destination.hash) return;
        if (reducedMotion.matches) return;

        event.preventDefault();
        document.documentElement.classList.add('motion-out');
        window.setTimeout(() => window.location.assign(destination.href), 150);
    });
};

const enableFormFeedback = () => {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const submit = form.querySelector('[type="submit"]');
            if (!submit) return;
            submit.classList.add('is-loading');
            submit.setAttribute('aria-busy', 'true');
        });
    });
};

const enableBrandParallax = () => {
    const panel = document.querySelector('.brand-panel');
    if (!panel || reducedMotion.matches) return;

    panel.addEventListener('pointermove', (event) => {
        const rect = panel.getBoundingClientRect();
        panel.style.setProperty('--pointer-x', `${((event.clientX - rect.left) / rect.width - 0.5) * 18}px`);
        panel.style.setProperty('--pointer-y', `${((event.clientY - rect.top) / rect.height - 0.5) * 18}px`);
    });

    panel.addEventListener('pointerleave', () => {
        panel.style.setProperty('--pointer-x', '0px');
        panel.style.setProperty('--pointer-y', '0px');
    });
};

onReady(() => {
    revealPage();
    animateDashboardData();
    enablePageTransitions();
    enableFormFeedback();
    enableBrandParallax();
    document.addEventListener('pointerdown', addRipple);
});
