import '../css/responsive-workspace.css';

const textSizes = { small: .875, normal: 1, large: 1.125, 'extra-large': 1.25 };
const textLabels = { small: 'Smaller', normal: 'Default', large: 'Larger', 'extra-large': 'Largest' };
const mobileViewport = window.matchMedia('(max-width: 1023px)');
let closeNavigation = () => {};

function prepareTables() {
    document.querySelectorAll('.main .table-wrap table').forEach((table) => {
        const headers = [...table.querySelectorAll('thead th')];
        if (!headers.length || table.querySelector('thead [colspan], thead [rowspan]')) return;
        table.querySelectorAll('tbody tr').forEach((row) => {
            if (row.cells.length !== headers.length || row.querySelector('[colspan], [rowspan]')) return;
            row.classList.add('mobile-record-row');
            [...row.cells].forEach((cell, index) => {
                if (!cell.dataset.label) cell.dataset.label = headers[index].textContent.trim() || 'Actions';
            });
        });
        table.classList.add('mobile-record-table');
    });
}

function initializeWorkspace() {
    document.documentElement.classList.add('responsive-ready');
    const sidebar = document.getElementById('workspace-navigation');
    const opener = document.querySelector('[data-mobile-menu]');
    const backdrop = document.querySelector('.mobile-menu-backdrop');
    if (sidebar && opener && backdrop) {
        const setOpen = (open, restoreFocus = true) => {
            const isOpen = mobileViewport.matches && open;
            document.body.classList.toggle('mobile-navigation-open', isOpen);
            opener.setAttribute('aria-expanded', String(isOpen));
            opener.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
            backdrop.hidden = !isOpen;
            sidebar.inert = mobileViewport.matches && !isOpen;
            document.querySelector('main')?.toggleAttribute('inert', isOpen);
            document.querySelector('.mobile-topbar')?.toggleAttribute('inert', isOpen);
            if (isOpen) {
                sidebar.setAttribute('role', 'dialog');
                sidebar.setAttribute('aria-modal', 'true');
                // Move focus after the menu button's click has finished.
                window.setTimeout(() => {
                    if (document.body.classList.contains('mobile-navigation-open')) sidebar.querySelector('[data-mobile-menu-close]')?.focus({ preventScroll: true });
                }, 0);
            } else {
                sidebar.removeAttribute('role');
                sidebar.removeAttribute('aria-modal');
                if (restoreFocus) opener.focus({ preventScroll: true });
            }
        };
        closeNavigation = () => setOpen(false, false);
        opener.addEventListener('click', () => setOpen(opener.getAttribute('aria-expanded') !== 'true'));
        document.querySelectorAll('[data-mobile-menu-close]').forEach((control) => control.addEventListener('click', () => setOpen(false)));
        sidebar.addEventListener('click', (event) => {
            if (event.target.closest('a[href]')) setOpen(false, false);
        });
        document.addEventListener('keydown', (event) => {
            if (!document.body.classList.contains('mobile-navigation-open')) return;
            if (event.key === 'Escape') { event.preventDefault(); setOpen(false); }
            if (event.key !== 'Tab') return;
            const controls = [...sidebar.querySelectorAll('a[href], button, input, [tabindex="0"]')].filter((item) => item.getClientRects().length && !item.disabled);
            const first = controls[0];
            const last = controls.at(-1);
            if (!sidebar.contains(document.activeElement)) { event.preventDefault(); first?.focus(); return; }
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last?.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first?.focus(); }
        });
        mobileViewport.addEventListener('change', () => setOpen(false, false));
        setOpen(false, false);
    }
    prepareTables();
}

document.addEventListener('change', (event) => {
    const control = event.target;
    if (!(control instanceof HTMLInputElement) || !control.matches('[data-text-size-setting] input[name="text_size"]') || !textSizes[control.value]) return;
    document.documentElement.style.setProperty('--app-text-scale', String(textSizes[control.value]));
    document.querySelectorAll('[data-text-size-status]').forEach((status) => { status.textContent = `Preview size: ${textLabels[control.value]}. Save to keep this preference.`; });
});

document.addEventListener('partial-navigation:loaded', () => {
    // The freshly loaded server stylesheet contains the persisted preference.
    document.documentElement.style.removeProperty('--app-text-scale');
    closeNavigation();
    prepareTables();
});
document.addEventListener('async-filter:updated', prepareTables);
window.addEventListener('pageshow', () => closeNavigation());
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeWorkspace, { once: true });
else initializeWorkspace();
