import 'bootstrap/dist/css/bootstrap.min.css';
import Modal from 'bootstrap/js/dist/modal';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

document.querySelectorAll('[data-admin-table]').forEach((panel) => {
    const rows = [...panel.querySelectorAll('tbody tr[data-search]')];
    const toolbar = panel.querySelector('.toolbar');
    const search = toolbar?.querySelector('input[type="search"]');
    const filters = [...(toolbar?.querySelectorAll('select') ?? [])];
    const empty = panel.querySelector('.empty-state[hidden]');

    if (!toolbar || !search || !rows.length) return;

    const count = document.createElement('span');
    count.className = 'filter-count';
    count.setAttribute('role', 'status');
    count.setAttribute('aria-live', 'polite');

    const reset = document.createElement('button');
    reset.type = 'button';
    reset.className = 'btn btn-sm btn-outline-secondary';
    reset.textContent = 'Clear filters';
    reset.hidden = true;
    toolbar.append(count, reset);

    const update = () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = 0;

        rows.forEach((row) => {
            const matchesText = (row.dataset.search ?? '').toLocaleLowerCase().includes(query);
            const matchesFilters = filters.every((filter) => !filter.value || row.dataset[filter.id.split('-').at(-1)] === filter.value);
            row.hidden = !(matchesText && matchesFilters);
            if (!row.hidden) visible++;
        });

        count.textContent = `Showing ${visible} of ${rows.length}`;
        reset.hidden = !query && filters.every((filter) => !filter.value);
        if (empty) empty.hidden = visible !== 0;
    };

    search.addEventListener('input', update);
    filters.forEach((filter) => filter.addEventListener('change', update));
    reset.addEventListener('click', () => {
        search.value = '';
        filters.forEach((filter) => { filter.value = ''; });
        update();
        search.focus();
    });
    update();
});

document.addEventListener('keydown', (event) => {
    if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) return;
    if (event.target.closest('input, textarea, select, [contenteditable]')) return;
    const search = document.querySelector('[data-admin-table] input[type="search"]');
    if (!search) return;
    event.preventDefault();
    search.focus();
});

const confirmationElement = document.getElementById('admin-confirmation');
if (confirmationElement) {
    const confirmation = new Modal(confirmationElement);
    const message = confirmationElement.querySelector('[data-confirm-message]');
    const proceed = confirmationElement.querySelector('[data-confirm-proceed]');
    const approvedForms = new WeakSet();
    let pendingForm = null;
    let pendingSubmitter = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!form.matches('form[data-confirm]') || approvedForms.has(form)) return;
        event.preventDefault();
        if (!form.reportValidity()) return;
        pendingForm = form;
        pendingSubmitter = event.submitter;
        message.textContent = form.dataset.confirm;
        proceed.classList.toggle('btn-danger', (pendingSubmitter?.textContent ?? '').toLowerCase().includes('reject'));
        proceed.classList.toggle('btn-primary', !proceed.classList.contains('btn-danger'));
        confirmation.show();
    });

    proceed.addEventListener('click', () => {
        if (!pendingForm) return;
        approvedForms.add(pendingForm);
        confirmation.hide();
        pendingForm.requestSubmit(pendingSubmitter ?? undefined);
    });

    confirmationElement.addEventListener('hidden.bs.modal', () => {
        pendingForm = null;
        pendingSubmitter = null;
    });
}

if (!reducedMotion.matches && 'IntersectionObserver' in window) {
    const cards = document.querySelectorAll('.stats-grid .stat-card, .quick-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12 });

    cards.forEach((card, index) => {
        card.classList.add('admin-reveal');
        card.style.setProperty('--reveal-order', index % 4);
        observer.observe(card);
    });
}
