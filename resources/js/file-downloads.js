const filenameFromDisposition = (disposition) => {
    if (!disposition) return null;

    const encoded = disposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1];
    if (encoded) {
        try {
            return decodeURIComponent(encoded.replace(/["']/g, ''));
        } catch {
            return encoded;
        }
    }

    return disposition.match(/filename="?([^";]+)"?/i)?.[1] ?? null;
};

document.addEventListener('click', async (event) => {
    if (!(event.target instanceof Element)
        || event.button !== 0
        || event.ctrlKey
        || event.metaKey
        || event.shiftKey
        || event.altKey) return;

    const link = event.target.closest('a[download][href]');
    if (!(link instanceof HTMLAnchorElement) || link.hasAttribute('data-download-bypass')) return;

    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) return;

    event.preventDefault();
    event.stopImmediatePropagation();

    const originalText = link.textContent;
    link.setAttribute('aria-busy', 'true');
    link.classList.add('is-loading');

    try {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/pdf,image/png,application/octet-stream',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) throw new Error(`Download failed with status ${response.status}`);

        const blobUrl = URL.createObjectURL(await response.blob());
        const download = document.createElement('a');
        download.href = blobUrl;
        download.download = filenameFromDisposition(response.headers.get('Content-Disposition'))
            || link.getAttribute('download')
            || 'download';
        download.hidden = true;
        download.setAttribute('data-download-bypass', '');
        document.body.append(download);
        download.click();
        download.remove();
        window.setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
    } catch (error) {
        window.alert('The file could not be downloaded. Please try again.');
        console.error(error);
    } finally {
        link.removeAttribute('aria-busy');
        link.classList.remove('is-loading');
        if (originalText !== null) link.textContent = originalText;
    }
}, true);
