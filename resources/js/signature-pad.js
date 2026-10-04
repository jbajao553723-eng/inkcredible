document.querySelectorAll('[data-signature-pad]').forEach((pad) => {
    const canvas = pad.querySelector('[data-signature-canvas]');
    const input = pad.querySelector('[data-signature-input]');
    const clear = pad.querySelector('[data-signature-clear]');
    const error = pad.querySelector('[data-signature-error]');

    if (!(canvas instanceof HTMLCanvasElement) || !(input instanceof HTMLInputElement)) return;

    const context = canvas.getContext('2d');
    let drawing = false;
    let hasInk = false;

    const reset = (hideError = true) => {
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.strokeStyle = '#172033';
        context.lineWidth = 4;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        input.value = '';
        hasInk = false;
        if (hideError && error) error.hidden = true;
    };
    const point = (event) => {
        const bounds = canvas.getBoundingClientRect();

        return {
            x: (event.clientX - bounds.left) * (canvas.width / bounds.width),
            y: (event.clientY - bounds.top) * (canvas.height / bounds.height),
        };
    };

    canvas.addEventListener('pointerdown', (event) => {
        drawing = true;
        hasInk = true;
        if (error) error.hidden = true;
        canvas.setPointerCapture(event.pointerId);
        const position = point(event);
        context.beginPath();
        context.moveTo(position.x, position.y);
    });
    canvas.addEventListener('pointermove', (event) => {
        if (!drawing) return;
        const position = point(event);
        context.lineTo(position.x, position.y);
        context.stroke();
    });
    const stopDrawing = () => { drawing = false; };
    canvas.addEventListener('pointerup', stopDrawing);
    canvas.addEventListener('pointercancel', stopDrawing);
    clear?.addEventListener('click', () => reset());
    reset(false);

    const form = pad.closest('form');
    form?.addEventListener('submit', (event) => {
        if (hasInk) input.value = canvas.toDataURL('image/png');

        if (pad.dataset.signatureRequired === 'true' && !hasInk) {
            event.preventDefault();
            if (error) error.hidden = false;
            pad.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return;
        }

        const submit = form.querySelector('[data-signature-submit]');
        if (!form.matches('[data-confirm]') && submit instanceof HTMLButtonElement) {
            submit.disabled = true;
            submit.textContent = submit.dataset.busyLabel ?? 'Saving securely...';
        }
    });
});
