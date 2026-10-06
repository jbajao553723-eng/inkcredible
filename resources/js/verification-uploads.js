const MAX_IMAGE_BYTES = 900 * 1024;
const MAX_PDF_BYTES = 1024 * 1024;
const MAX_REQUEST_BYTES = 3_750_000;
const MAX_IMAGE_DIMENSION = 1600;

const formatMegabytes = (bytes) => `${(bytes / 1024 / 1024).toFixed(1)} MB`;

const compressImage = async (file) => {
    if (!file.type.startsWith('image/')) return file;

    const bitmap = await createImageBitmap(file);
    const scale = Math.min(1, MAX_IMAGE_DIMENSION / Math.max(bitmap.width, bitmap.height));

    if (file.size <= MAX_IMAGE_BYTES && scale === 1) {
        bitmap.close();

        return file;
    }

    const canvas = document.createElement('canvas');
    canvas.width = Math.max(1, Math.round(bitmap.width * scale));
    canvas.height = Math.max(1, Math.round(bitmap.height * scale));
    const context = canvas.getContext('2d');
    context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    bitmap.close();

    let quality = 0.86;
    let blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));

    while (blob && blob.size > MAX_IMAGE_BYTES && quality > 0.56) {
        quality -= 0.08;
        blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', quality));
    }

    if (!blob) return file;

    const name = file.name.replace(/\.[^.]+$/, '') || 'upload';

    return new File([blob], `${name}.jpg`, {
        type: 'image/jpeg',
        lastModified: file.lastModified,
    });
};

const replaceInputFile = (input, file) => {
    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;
};

const initializeVerificationUploads = (root = document) => {
    const forms = root.matches?.('[data-verification-upload-form]')
        ? [root]
        : [...root.querySelectorAll?.('[data-verification-upload-form]') ?? []];

    forms.forEach((form) => {
        if (form.dataset.uploadLimitsInitialized === 'true') return;
        form.dataset.uploadLimitsInitialized = 'true';

        const inputs = [...form.querySelectorAll('input[type="file"]')];

        const validateCombinedSize = () => {
            inputs.forEach((input) => input.setCustomValidity(''));
            const files = inputs.flatMap((input) => [...(input.files ?? [])]);
            const oversizedPdf = files.find((file) => file.type === 'application/pdf' && file.size > MAX_PDF_BYTES);

            if (oversizedPdf) {
                const input = inputs.find((candidate) => [...(candidate.files ?? [])].includes(oversizedPdf));
                input?.setCustomValidity(`PDF files must be ${formatMegabytes(MAX_PDF_BYTES)} or smaller.`);

                return;
            }

            const total = files.reduce((sum, file) => sum + file.size, 0);

            if (total > MAX_REQUEST_BYTES) {
                inputs.find((input) => input.files?.length)?.setCustomValidity(
                    `Selected files total ${formatMegabytes(total)}. Keep the combined upload under ${formatMegabytes(MAX_REQUEST_BYTES)}.`,
                );
            }
        };

        inputs.forEach((input) => {
            input.addEventListener('change', async () => {
                const file = input.files?.[0];
                if (!file) {
                    validateCombinedSize();

                    return;
                }

                input.setCustomValidity('Preparing this file for secure upload…');

                try {
                    const compressed = await compressImage(file);
                    if (compressed !== file) replaceInputFile(input, compressed);
                } catch {
                    // Keep the original file and let the size validation below explain any issue.
                }

                validateCombinedSize();
                input.dispatchEvent(new CustomEvent('verification-upload:prepared', { bubbles: true }));
            });
        });
    });
};

initializeVerificationUploads();
document.addEventListener('partial-navigation:loaded', (event) => {
    initializeVerificationUploads(event.detail?.root ?? document);
});
