import 'bootstrap/dist/css/bootstrap.min.css';
import Modal from 'bootstrap/js/dist/modal';
import './ui-motion';
import './file-downloads';

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
    const search = document.querySelector('[data-admin-table]:not([hidden]) input[type="search"]')
        ?? document.querySelector('[data-admin-table] input[type="search"]');
    if (!search) return;
    event.preventDefault();
    search.focus();
});

const asyncFilterRequest = async (url, selector, pushHistory = true) => {
    const currentRegion = document.querySelector(selector);
    if (!currentRegion) return;

    currentRegion.classList.add('is-loading');
    currentRegion.setAttribute('aria-busy', 'true');

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) throw new Error(`Filter request failed with status ${response.status}`);

        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextRegion = page.querySelector(selector);

        if (!nextRegion) throw new Error(`Filter response did not include ${selector}`);

        const currentForm = document.querySelector(`[data-async-filter][data-async-filter-target="${selector}"]`);
        const nextForm = page.querySelector(`[data-async-filter][data-async-filter-target="${selector}"]`);

        if (currentForm && nextForm && !currentRegion.contains(currentForm)) {
            [...currentForm.elements].forEach((field) => {
                if (!field.name) return;
                const nextField = nextForm.elements.namedItem(field.name);
                if (!nextField || nextField instanceof RadioNodeList) return;
                if ('value' in nextField) field.value = nextField.value;
            });
        }

        currentRegion.replaceWith(document.importNode(nextRegion, true));
        document.title = page.title || document.title;

        if (pushHistory) {
            window.history.pushState({ asyncFilterTarget: selector }, '', url);
        }

        document.dispatchEvent(new CustomEvent('async-filter:updated', {
            detail: { selector, url },
        }));
    } catch (error) {
        currentRegion.classList.remove('is-loading');
        currentRegion.removeAttribute('aria-busy');
        window.location.assign(url);
    }
};

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.matches('[data-async-filter]')) return;

    event.preventDefault();

    const dateFrom = form.querySelector('[name="date_from"]');
    const dateTo = form.querySelector('[name="date_to"]');
    if (dateTo instanceof HTMLInputElement) {
        dateTo.setCustomValidity(
            dateFrom instanceof HTMLInputElement && dateFrom.value && dateTo.value && dateTo.value < dateFrom.value
                ? 'The end date must be on or after the start date.'
                : '',
        );
    }

    if (!form.reportValidity()) return;

    // A control named "action" shadows HTMLFormElement.action (for example,
    // the Audit Logs action selector). Read the attribute instead so the URL
    // can never become "/[object HTMLSelectElement]".
    const formAction = form.getAttribute('action') || window.location.href;
    const url = new URL(formAction, window.location.href);
    const parameters = new URLSearchParams();

    new FormData(form).forEach((value, key) => {
        if (typeof value === 'string' && value.trim() !== '') parameters.append(key, value);
    });

    url.search = parameters.toString();
    asyncFilterRequest(url.toString(), form.dataset.asyncFilterTarget);
});

document.addEventListener('input', (event) => {
    if (event.target instanceof HTMLInputElement
        && event.target.matches('[data-async-filter] [name="date_from"], [data-async-filter] [name="date_to"]')) {
        event.target.form?.querySelector('[name="date_to"]')?.setCustomValidity('');
    }
});

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)
        || event.button !== 0
        || event.ctrlKey
        || event.metaKey
        || event.shiftKey
        || event.altKey) return;

    const link = event.target.closest('a[href]');
    if (!link) return;

    const region = link.closest('[data-async-filter-region]');
    const isFilterLink = link.matches('[data-async-filter-link]');
    const isPaginationLink = region && (link.closest('.pagination') || link.closest('.pagination-bar'));

    if (!isFilterLink && !isPaginationLink) return;

    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin || url.hash || link.classList.contains('disabled')) return;

    const selector = link.dataset.asyncFilterTarget
        || region?.dataset.asyncFilterTarget
        || (region?.id ? `#${region.id}` : null);
    if (!selector) return;

    event.preventDefault();
    asyncFilterRequest(url.toString(), selector);
}, true);

window.addEventListener('popstate', (event) => {
    const selector = event.state?.asyncFilterTarget;
    if (selector) asyncFilterRequest(window.location.href, selector, false);
});

document.querySelectorAll('[data-async-filter-region]').forEach((region) => {
    const selector = region.dataset.asyncFilterTarget || (region.id ? `#${region.id}` : null);
    if (selector && !window.history.state?.asyncFilterTarget) {
        window.history.replaceState({ asyncFilterTarget: selector }, '', window.location.href);
    }
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
        const isDanger = ['reject', 'delete', 'remove', 'clear'].some((word) =>
            `${pendingSubmitter?.textContent ?? ''} ${form.dataset.confirm ?? ''}`.toLowerCase().includes(word),
        );
        confirmationElement.classList.toggle('is-danger', isDanger);
        proceed.textContent = isDanger ? 'Yes, proceed' : 'Yes, continue';
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
        confirmationElement.classList.remove('is-danger');
    });
}

const reportGuideElement = document.getElementById('report-guide-modal');
if (reportGuideElement) {
    const reportGuide = Modal.getOrCreateInstance(reportGuideElement);
    document.querySelectorAll('[data-report-guide]').forEach((trigger) => {
        trigger.addEventListener('click', () => reportGuide.show());
    });
}

const createAdminElement = document.getElementById('create-admin-modal');
if (createAdminElement) {
    const createAdminModal = Modal.getOrCreateInstance(createAdminElement);
    document.querySelectorAll('[data-create-admin]').forEach((trigger) => {
        trigger.addEventListener('click', () => createAdminModal.show());
    });
    if (createAdminElement.dataset.autoOpen === 'true') createAdminModal.show();
}

const editAdminElement = document.getElementById('edit-admin-modal');
if (editAdminElement) {
    const editAdminModal = Modal.getOrCreateInstance(editAdminElement);
    const editAdminForm = editAdminElement.querySelector('[data-edit-admin-form]');
    const fields = {
        id: editAdminElement.querySelector('[data-edit-admin-id]'),
        firstName: editAdminElement.querySelector('[data-edit-first-name]'),
        lastName: editAdminElement.querySelector('[data-edit-last-name]'),
        email: editAdminElement.querySelector('[data-edit-email]'),
        contact: editAdminElement.querySelector('[data-edit-contact]'),
        password: editAdminElement.querySelector('[data-edit-password]'),
        passwordConfirmation: editAdminElement.querySelector('[data-edit-password-confirmation]'),
    };

    document.querySelectorAll('[data-edit-admin]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            editAdminForm.action = trigger.dataset.adminUpdateUrl;
            fields.id.value = trigger.dataset.adminId;
            fields.firstName.value = trigger.dataset.adminFirstName;
            fields.lastName.value = trigger.dataset.adminLastName;
            fields.email.value = trigger.dataset.adminEmail;
            fields.contact.value = trigger.dataset.adminContact;
            fields.password.value = '';
            fields.passwordConfirmation.value = '';
            editAdminModal.show();
        });
    });

    if (editAdminElement.dataset.autoOpen === 'true') {
        editAdminForm.action = editAdminElement.dataset.editAction;
        editAdminModal.show();
    }
}

const analyticsTabs = [...document.querySelectorAll('[data-analytics-tab]')];
const analyticsPanels = [...document.querySelectorAll('[data-analytics-panel]')];
if (analyticsTabs.length && analyticsPanels.length) {
    const activateAnalyticsTab = (tab, moveFocus = false) => {
        analyticsTabs.forEach((candidate) => {
            const isActive = candidate === tab;
            candidate.setAttribute('aria-selected', String(isActive));
            candidate.tabIndex = isActive ? 0 : -1;
        });
        analyticsPanels.forEach((panel) => {
            panel.hidden = panel.dataset.analyticsPanel !== tab.dataset.analyticsTab;
        });
        if (moveFocus) tab.focus();
    };

    analyticsTabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateAnalyticsTab(tab));
        tab.addEventListener('keydown', (event) => {
            const lastIndex = analyticsTabs.length - 1;
            const nextIndex = event.key === 'ArrowRight'
                ? (index + 1) % analyticsTabs.length
                : event.key === 'ArrowLeft'
                    ? (index - 1 + analyticsTabs.length) % analyticsTabs.length
                    : event.key === 'Home'
                        ? 0
                        : event.key === 'End'
                            ? lastIndex
                            : null;
            if (nextIndex === null) return;
            event.preventDefault();
            activateAnalyticsTab(analyticsTabs[nextIndex], true);
        });
    });
}

const reportActionMenu = document.querySelector('.report-action-menu');
if (reportActionMenu) {
    document.addEventListener('click', (event) => {
        if (!reportActionMenu.contains(event.target)) reportActionMenu.removeAttribute('open');
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') reportActionMenu.removeAttribute('open');
    });
}
