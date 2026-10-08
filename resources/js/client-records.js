const initializeRecords = (root = document) => {
    root.querySelectorAll('[data-record-list]').forEach((list) => {
        if (list.dataset.recordsReady) return;
        list.dataset.recordsReady = 'true';
        const search = list.querySelector('[data-record-search]');
        const status = list.querySelector('[data-record-status]');
        const rows = [...list.querySelectorAll('[data-record-row]')];
        const count = list.querySelector('[data-record-count]');
        const empty = list.querySelector('[data-record-empty]');
        const update = () => {
            const query = (search?.value || '').trim().toLocaleLowerCase();
            const selectedStatus = status?.value || '';
            let visible = 0;
            rows.forEach((row) => {
                const matches = (!query || row.textContent.toLocaleLowerCase().includes(query))
                    && (!selectedStatus || row.dataset.recordStatusValue === selectedStatus);
                row.hidden = !matches;
                if (matches) visible++;
            });
            if (count) count.textContent = `Showing ${visible} of ${rows.length} records`;
            if (empty) empty.hidden = visible !== 0;
        };
        search?.addEventListener('input', update);
        status?.addEventListener('change', update);
        update();
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeRecords());
} else {
    initializeRecords();
}
document.addEventListener('partial-navigation:loaded', () => initializeRecords());
