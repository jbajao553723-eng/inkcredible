<dialog class="confirmation-dialog" id="client-confirmation" aria-labelledby="client-confirmation-title" aria-describedby="client-confirmation-message">
    <div class="confirmation-dialog-card">
        <button class="confirmation-dialog-close" type="button" data-confirm-cancel aria-label="Close">&times;</button>
        <div class="confirmation-dialog-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.6 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
        </div>
        <div class="confirmation-dialog-eyebrow">Final check</div>
        <h2 id="client-confirmation-title">Are you sure?</h2>
        <p id="client-confirmation-message" data-confirm-message></p>
        <div class="confirmation-dialog-actions">
            <button class="button button-secondary" type="button" data-confirm-cancel>Go back</button>
            <button class="button button-primary" type="button" data-confirm-proceed>Yes, send it</button>
        </div>
    </div>
</dialog>
