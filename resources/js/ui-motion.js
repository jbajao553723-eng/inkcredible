const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const transitionDuration = 220;
const prefersReducedMotion = () => reducedMotion.matches
    || getComputedStyle(document.documentElement).getPropertyValue('--app-reduce-motion').trim() === '1';

document.documentElement.classList.add('motion-enabled');

const interactiveSelector = [
    '.button',
    '.btn',
    '.submit-button',
    '.save-button',
    '.logout-button',
    '.quick-card',
    '.quick-amount',
    '.full-balance-button',
    '.method-card',
    '.loan-card',
    '.loan-type-card',
    '.settings-tab',
    '.nav-link',
    '.panel-link',
    '.verification-banner a',
    '.password-toggle',
    '.photo-upload',
    '[role="button"]',
].join(', ');

const onReady = (callback) => {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
};

const uniqueMatches = (selectors) => [...new Set(
    selectors.flatMap((selector) => [...document.querySelectorAll(selector)])
)];

const collectUniversalRevealElements = () => {
    const rootSelectors = [
        '.page-shell',
        '.form-shell',
        '.legal-card',
        '.result-layout',
        '.hero-grid',
        'body > .min-h-screen',
    ];
    const staggerContainers = [
        'form',
        '.form-row',
        '.form-grid',
        '.stats-grid',
        '.overview-stats',
        '.overview-grid',
        '.metric-layout',
        '.section-stack',
        '.quick-grid',
        '.detail-layout',
        '.application-grid',
        '.workspace-grid',
        '.profile-layout',
        '.security-grid',
        '.settings-tabs',
        '.hero-grid',
        '.result-layout',
    ].join(', ');
    const ignored = 'script, style, template, input[type="hidden"], [hidden]';

    const expand = (element, depth = 0) => {
        if (!(element instanceof HTMLElement) || element.matches(ignored)) return [];
        if (depth < 2 && element.matches(staggerContainers)) {
            return [...element.children].flatMap((child) => expand(child, depth + 1));
        }

        return [element];
    };

    return uniqueMatches(rootSelectors)
        .flatMap((root) => [...root.children].flatMap((child) => expand(child)));
};

const revealPage = () => {
    const authSelectors = [
        '.brand-panel .brand',
        '.brand-eyebrow',
        '.brand-title',
        '.brand-description',
        '.feature-list li',
        '.brand-footer',
        '.form-shell > :not(form)',
        '.form-shell form > :not(.form-row)',
        '.form-shell form > .form-row > *',
        'body > .min-h-screen > *',
        'body > .min-h-screen > * > *',
    ];

    const pageSelectors = [
        'body > .min-h-screen > *',
        '.site-header > *',
        '.sidebar',
        '.sidebar-brand',
        '.sidebar .brand',
        '.sidebar .nav-label',
        '.sidebar .nav-section',
        '.sidebar nav > *',
        '.sidebar-account',
        '.topbar',
        '.topbar > *',
        '.main .page-shell > *',
        '.alert',
        '.verification-banner',
        '.stats-grid > *',
        '.overview-stats > *',
        '.overview-grid > *',
        '.metric-layout > *',
        '.section-stack > *',
        '.quick-grid > *',
        '.quick-card',
        '.detail-layout > *',
        '.application-grid > *',
        '.workspace-grid > *',
        '.profile-layout > *',
        '.security-grid > *',
        '.settings-tabs > *',
        '.verification-section',
        '.readiness-panel',
        '.readiness-factors > *',
        '.motion-setting',
        '.page-shell > .panel',
        '.page-shell > .history-panel',
        '.page-shell > .detail-panel',
        '.result',
        '.result > *',
        '.result-card',
        '.message-panel > *',
        '.receipt',
        '.nav-inner > *',
        '.hero-grid > *',
        '.legal-header .header-inner > *',
        '.legal-card',
        '.footer-inner > *',
    ];

    const elements = [...new Set([
        ...uniqueMatches(document.querySelector('.auth-page') ? authSelectors : pageSelectors),
        ...collectUniversalRevealElements(),
    ])];

    elements.forEach((element, index) => {
        element.classList.add('motion-reveal');
        element.style.setProperty('--motion-order', Math.min(index, 12));

        if (element.matches('.sidebar, .sidebar-brand')) element.classList.add('motion-from-left');
        if (element.matches('.stat-card, .overview-card, .quick-card, .receipt, .result-card')) element.classList.add('motion-scale');
    });

    document.querySelectorAll('tbody tr').forEach((row, index) => {
        row.classList.add('motion-row');
        row.style.setProperty('--motion-row-order', index % 10);
    });

    requestAnimationFrame(() => requestAnimationFrame(() => {
        document.documentElement.classList.add('motion-in');
    }));
};

const revealOnScroll = () => {
    const selectors = [
        '.payment-section',
        '.profile-form-section',
        '.verification-section',
        '.form-section',
        '.verification-notice',
        '.document-grid > *',
        '.readiness-factor',
        '.settings-tab',
        '.loan-list > *',
        '.method-option',
        '.loan-option',
        '.panel-header > *',
        '.panel-body > *',
        '.history-header > *',
        '.detail-item',
        '.profile-fact',
        '.features > *',
        '.steps > *',
        '.section-head > *',
        '.cta > *',
        '.legal-card > *',
        '.legal-card .section',
        '.step',
        '.toolbar > *',
        '.form-grid > *',
        '.account-preview > *',
        '.receipt .detail-row',
        '.modal-body > *',
        'main > section > *',
        'main > article > *',
    ];
    const elements = uniqueMatches(selectors)
        .filter((element) => !element.classList.contains('motion-reveal'));

    elements.forEach((element) => {
        const siblings = [...element.parentElement.children].filter((sibling) => elements.includes(sibling));
        element.classList.add('motion-scroll');
        element.style.setProperty('--motion-child-order', Math.min(siblings.indexOf(element), 6));
    });

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('motion-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('motion-visible');
            observer.unobserve(entry.target);
        });
    }, {
        rootMargin: '0px 0px -5% 0px',
        threshold: 0.06,
    });

    elements.forEach((element) => observer.observe(element));
};

const animateNumber = (element) => {
    if (prefersReducedMotion() || element.dataset.motionCounted === 'true') return;

    const original = element.textContent.trim();
    const match = original.match(/-?[\d,]+(?:\.\d+)?/);

    if (!match) return;

    const target = Number(match[0].replaceAll(',', ''));
    if (!Number.isFinite(target) || target === 0) return;

    element.dataset.motionCounted = 'true';
    const decimals = match[0].includes('.') ? match[0].split('.')[1].length : 0;
    const prefix = original.slice(0, match.index);
    const suffix = original.slice(match.index + match[0].length);
    const formatter = new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
    const duration = 900;
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
    const progressBars = document.querySelectorAll([
        '.progress-bar',
        '.status-fill',
        '.balance-progress-fill',
        '.completion-fill',
    ].join(', '));

    progressBars.forEach((bar) => {
        const targetWidth = bar.style.width || getComputedStyle(bar).width;
        if (!targetWidth || targetWidth === '0px') return;
        bar.style.setProperty('--target-width', targetWidth);
        bar.style.width = '0';
    });

    window.setTimeout(() => {
        progressBars.forEach((bar) => {
            bar.style.width = bar.style.getPropertyValue('--target-width');
        });
        document.querySelectorAll([
            '.stat-value',
            '.balance-value',
            '.next-amount',
            '.amount-value',
            '.metric-value',
            '.summary-value',
            '.loan-balance',
            '[data-motion-count]',
        ].join(', ')).forEach(animateNumber);

        document.querySelectorAll('[data-readiness-ring]').forEach((ring) => {
            if (prefersReducedMotion() || ring.dataset.motionRing === 'true') return;

            const target = Number.parseFloat(ring.style.getPropertyValue('--score'));
            if (!Number.isFinite(target)) return;

            ring.dataset.motionRing = 'true';
            ring.style.setProperty('--score', '0');
            let startTime;

            const update = (time) => {
                startTime ??= time;
                const progress = Math.min((time - startTime) / 900, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                ring.style.setProperty('--score', String(target * eased));

                if (progress < 1) requestAnimationFrame(update);
                else ring.style.setProperty('--score', String(target));
            };

            requestAnimationFrame(update);
        });
    }, prefersReducedMotion() ? 0 : 360);
};

const addRipple = (event) => {
    if (prefersReducedMotion() || !(event.target instanceof Element)) return;

    const target = event.target.closest(interactiveSelector);
    if (!target || target.matches(':disabled, [aria-disabled="true"]')) return;

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

const createTransitionBar = () => {
    const bar = document.createElement('div');
    bar.className = 'page-transition-bar';
    bar.setAttribute('aria-hidden', 'true');
    document.body.append(bar);
    return bar;
};

const enablePageTransitions = () => {
    const transitionBar = createTransitionBar();

    const beginPageExit = () => {
        transitionBar.classList.add('is-active');
        document.documentElement.classList.remove('motion-in');
        document.documentElement.classList.add('motion-out');
    };

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) return;
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-transition')) return;

        let destination;
        try {
            destination = new URL(link.href, window.location.href);
        } catch {
            return;
        }

        if (!['http:', 'https:'].includes(destination.protocol)) return;
        if (destination.origin !== window.location.origin || destination.href === window.location.href || destination.hash) return;
        if (prefersReducedMotion()) return;

        event.preventDefault();
        beginPageExit();
        window.setTimeout(() => window.location.assign(destination.href), transitionDuration);
    });

    const transitioningForms = new WeakSet();

    window.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)
            || event.defaultPrevented
            || prefersReducedMotion()
            || transitioningForms.has(form)
            || form.hasAttribute('data-no-transition')
            || (form.target && form.target !== '_self')
            || form.method.toLowerCase() === 'dialog') {
            return;
        }

        event.preventDefault();
        transitioningForms.add(form);

        const submit = event.submitter ?? form.querySelector('[type="submit"]');

        if (submit) {
            submit.classList.add('is-loading');
            submit.setAttribute('aria-busy', 'true');
            submit.style.pointerEvents = 'none';

            if (event.submitter?.name) {
                const submitterValue = document.createElement('input');
                submitterValue.type = 'hidden';
                submitterValue.name = event.submitter.name;
                submitterValue.value = event.submitter.value;
                form.append(submitterValue);
            }
        }

        beginPageExit();
        window.setTimeout(() => HTMLFormElement.prototype.submit.call(form), transitionDuration);
    });

    window.addEventListener('pageshow', (event) => {
        transitionBar.classList.remove('is-active');
        document.documentElement.classList.remove('motion-out');

        if (event.persisted && !prefersReducedMotion()) {
            document.documentElement.classList.remove('motion-in');
            document.querySelectorAll('.motion-scroll').forEach((element) => element.classList.remove('motion-visible'));
            requestAnimationFrame(() => requestAnimationFrame(() => {
                document.documentElement.classList.add('motion-in');
                document.querySelectorAll('.motion-scroll').forEach((element) => element.classList.add('motion-visible'));
            }));
            return;
        }

        requestAnimationFrame(() => document.documentElement.classList.add('motion-in'));
    });
};

const enableFormFeedback = () => {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            queueMicrotask(() => {
                if (event.defaultPrevented) return;
                const submit = event.submitter ?? form.querySelector('[type="submit"]');
                if (!submit) return;
                submit.classList.add('is-loading');
                submit.setAttribute('aria-busy', 'true');
                submit.style.pointerEvents = 'none';
            });
        });
    });
};

const fieldContainerSelector = [
    '.form-group',
    '.amount-control',
    '.account-select',
    '.input-group',
    '.field',
    '.proof-upload',
].join(', ');

const enableFieldFeedback = () => {
    document.addEventListener('focusin', (event) => {
        if (!(event.target instanceof Element)) return;
        event.target.closest(fieldContainerSelector)?.classList.add('has-focus');
    });

    document.addEventListener('focusout', (event) => {
        if (!(event.target instanceof Element)) return;
        event.target.closest(fieldContainerSelector)?.classList.remove('has-focus');
    });

    document.addEventListener('change', (event) => {
        if (!(event.target instanceof HTMLInputElement)) return;
        if (!['checkbox', 'radio', 'file'].includes(event.target.type)) return;

        const visual = event.target.closest('label, .form-group, .method-option, .loan-option, .proof-upload')
            ?? event.target.nextElementSibling;
        if (!(visual instanceof Element)) return;

        visual.classList.remove('motion-selected');
        requestAnimationFrame(() => visual.classList.add('motion-selected'));
        visual.addEventListener('animationend', () => visual.classList.remove('motion-selected'), { once: true });
    });
};

const enableBrandParallax = () => {
    const panel = document.querySelector('.brand-panel');
    if (!panel || prefersReducedMotion()) return;

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

const enableScrollProgress = () => {
    if (prefersReducedMotion()) return;

    const indicator = document.createElement('div');
    indicator.className = 'motion-scroll-progress';
    indicator.setAttribute('aria-hidden', 'true');
    document.body.append(indicator);

    let scheduled = false;
    const update = () => {
        const available = document.documentElement.scrollHeight - window.innerHeight;
        const progress = available > 120 ? Math.min(window.scrollY / available, 1) : 0;
        indicator.style.transform = `scaleX(${progress})`;
        indicator.classList.toggle('is-visible', available > 120 && window.scrollY > 6);
        scheduled = false;
    };

    window.addEventListener('scroll', () => {
        if (scheduled) return;
        scheduled = true;
        requestAnimationFrame(update);
    }, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();
};

const enableDynamicMotion = () => {
    if (!('MutationObserver' in window)) return;

    const animate = (element) => {
        if (!(element instanceof Element) || prefersReducedMotion()) return;
        if (!element.matches('.alert, .toast, .dropdown-menu, .empty-state, tbody tr, [role="status"]')) return;
        if (element.hidden) return;

        element.classList.remove('motion-dynamic-in');
        requestAnimationFrame(() => element.classList.add('motion-dynamic-in'));
    };

    const observer = new MutationObserver((records) => {
        records.forEach((record) => {
            if (record.type === 'attributes') {
                animate(record.target);
                return;
            }

            record.addedNodes.forEach((node) => {
                animate(node);
                if (node instanceof Element) {
                    node.querySelectorAll('.alert, .toast, .dropdown-menu, .empty-state, tbody tr, [role="status"]')
                        .forEach(animate);
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['hidden', 'aria-hidden'] });
};

onReady(() => {
    revealPage();
    revealOnScroll();
    animateDashboardData();
    enablePageTransitions();
    enableFormFeedback();
    enableFieldFeedback();
    enableBrandParallax();
    enableScrollProgress();
    enableDynamicMotion();
    document.addEventListener('pointerdown', addRipple);
});

document.addEventListener('partial-navigation:loaded', () => {
    revealOnScroll();
    animateDashboardData();
});
