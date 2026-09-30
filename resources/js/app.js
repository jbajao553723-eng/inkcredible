import './bootstrap';
import './ui-motion';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const confirmationDialog = document.getElementById('client-confirmation');

if (confirmationDialog instanceof HTMLDialogElement) {
    const message = confirmationDialog.querySelector('[data-confirm-message]');
    const proceed = confirmationDialog.querySelector('[data-confirm-proceed]');
    const approvedForms = new WeakSet();
    let pendingForm = null;
    let pendingSubmitter = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('[data-confirm]') || approvedForms.has(form)) return;

        event.preventDefault();
        if (!form.reportValidity()) return;

        pendingForm = form;
        pendingSubmitter = event.submitter;
        if (message) message.textContent = form.dataset.confirm ?? 'Continue with this action?';
        confirmationDialog.showModal();
    });

    confirmationDialog.querySelectorAll('[data-confirm-cancel]').forEach((button) => {
        button.addEventListener('click', () => confirmationDialog.close());
    });

    proceed?.addEventListener('click', () => {
        if (!pendingForm) return;
        const form = pendingForm;
        const submitter = pendingSubmitter;
        approvedForms.add(form);
        confirmationDialog.close();
        form.requestSubmit(submitter ?? undefined);
    });

    confirmationDialog.addEventListener('close', () => {
        pendingForm = null;
        pendingSubmitter = null;
    });
}
