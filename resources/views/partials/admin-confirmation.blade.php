<div class="modal fade confirmation-modal" id="admin-confirmation" tabindex="-1" aria-labelledby="admin-confirmation-title" aria-describedby="admin-confirmation-message" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content confirmation-card">
            <button type="button" class="btn-close confirmation-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="confirmation-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.6 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0Z"/></svg>
            </div>
            <div class="confirmation-copy">
                <div class="confirmation-eyebrow">Please confirm</div>
                <h2 class="modal-title" id="admin-confirmation-title">Are you sure?</h2>
                <p id="admin-confirmation-message" data-confirm-message></p>
            </div>
            <div class="confirmation-actions">
                <button type="button" class="btn confirmation-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmation-proceed" data-confirm-proceed>Yes, continue</button>
            </div>
        </div>
    </div>
</div>
