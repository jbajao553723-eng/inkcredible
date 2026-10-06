const navigationSelector = '[data-partial-navigation] a[href]';
let activeRequest;

const executePageScripts = (page) => {
    page.querySelectorAll('script[data-partial-page-script]').forEach((script) => {
        const executable = document.createElement('script');
        executable.textContent = `(function () {\n${script.textContent}\n})();`;
        document.body.append(executable);
        executable.remove();
    });
};

const syncSettingsTabs = (currentMain, nextMain) => {
    const nextTabs = [...nextMain.querySelectorAll('[data-settings-tab]')];
    const activeSection = nextTabs.find((tab) => tab.getAttribute('aria-selected') === 'true')?.dataset.settingsTab;

    currentMain.querySelectorAll('[data-settings-tab]').forEach((tab) => {
        const isActive = tab.dataset.settingsTab === activeSection;
        tab.classList.toggle('active', isActive);
        tab.setAttribute('aria-selected', String(isActive));
        tab.tabIndex = isActive ? 0 : -1;
    });

    return activeSection;
};

const loadPartialPage = async (url, { pushHistory = true, moveFocus = false } = {}) => {
    const currentMain = document.querySelector('main[data-partial-page]');
    const currentStyle = document.querySelector('style[data-partial-page-style]');

    if (!currentMain || !currentStyle) {
        window.location.assign(url);
        return;
    }

    activeRequest?.abort();
    activeRequest = new AbortController();
    const currentContent = currentMain.querySelector('[data-partial-content]');
    const loadingRegion = currentContent ?? currentMain;
    loadingRegion.classList.add('partial-page-loading');
    loadingRegion.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: activeRequest.signal,
        });

        if (!response.ok) throw new Error(`Partial navigation failed with status ${response.status}`);

        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextMain = page.querySelector('main[data-partial-page]');
        const nextStyle = page.querySelector('style[data-partial-page-style]');
        const nextContent = nextMain?.querySelector('[data-partial-content]');

        if (!nextMain || !nextStyle) throw new Error('The destination is not a partial-navigation page.');

        currentStyle.textContent = nextStyle.textContent;
        let loadedRoot;
        if (currentContent && nextContent) {
            loadedRoot = document.importNode(nextContent, true);
            currentContent.replaceWith(loadedRoot);
            const activeSection = syncSettingsTabs(currentMain, nextMain);
            if (moveFocus) currentMain.querySelector(`[data-settings-tab="${activeSection}"]`)?.focus({ preventScroll: true });
        } else {
            loadedRoot = document.importNode(nextMain, true);
            currentMain.replaceWith(loadedRoot);
        }
        document.title = page.title || document.title;

        if (pushHistory) {
            window.history.pushState({ partialPage: true }, '', url);
        }

        executePageScripts(page);

        if (!moveFocus) loadedRoot?.focus({ preventScroll: true });
        document.dispatchEvent(new CustomEvent('partial-navigation:loaded', {
            detail: { root: loadedRoot, url },
        }));
    } catch (error) {
        if (error.name === 'AbortError') return;
        window.location.assign(url);
    }
};

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)
        || event.defaultPrevented
        || event.button !== 0
        || event.ctrlKey
        || event.metaKey
        || event.shiftKey
        || event.altKey) return;

    const link = event.target.closest(navigationSelector);
    if (!link || link.target === '_blank' || link.hasAttribute('download')) return;

    const destination = new URL(link.href, window.location.href);
    if (destination.origin !== window.location.origin || destination.href === window.location.href) return;

    const dirtyForm = document.querySelector('main[data-partial-page] form[data-partial-dirty="true"]');
    if (dirtyForm && !window.confirm('You have unsaved changes. Leave this settings section?')) return;

    event.preventDefault();
    loadPartialPage(destination.href);
}, true);

document.addEventListener('input', (event) => {
    const form = event.target instanceof Element ? event.target.closest('main[data-partial-page] form') : null;
    if (form) form.dataset.partialDirty = 'true';
});

document.addEventListener('keydown', (event) => {
    const tab = event.target instanceof Element ? event.target.closest('[data-settings-tab]') : null;
    if (!tab || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;

    const tabs = [...tab.closest('[role="tablist"]')?.querySelectorAll('[data-settings-tab]') ?? []];
    const currentIndex = tabs.indexOf(tab);
    if (currentIndex < 0) return;

    event.preventDefault();
    let nextIndex = currentIndex;
    if (event.key === 'ArrowLeft') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
    if (event.key === 'ArrowRight') nextIndex = (currentIndex + 1) % tabs.length;
    if (event.key === 'Home') nextIndex = 0;
    if (event.key === 'End') nextIndex = tabs.length - 1;
    loadPartialPage(tabs[nextIndex].href, { moveFocus: true });
});

document.addEventListener('change', (event) => {
    const form = event.target instanceof Element ? event.target.closest('main[data-partial-page] form') : null;
    if (form) form.dataset.partialDirty = 'true';
});

document.addEventListener('submit', (event) => {
    if (event.target instanceof HTMLFormElement) delete event.target.dataset.partialDirty;
});

window.addEventListener('popstate', (event) => {
    if (event.state?.partialPage) {
        loadPartialPage(window.location.href, { pushHistory: false });
        return;
    }

    if (document.querySelector('main[data-partial-page]')) window.location.reload();
});

if (document.querySelector('main[data-partial-page]')) {
    window.history.replaceState({ ...window.history.state, partialPage: true }, '', window.location.href);
}
