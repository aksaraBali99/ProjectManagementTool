// Reusable click-to-enlarge overlay — not coupled to rich-text images.
// Anything the app wants to open full-size just calls openLightbox({src,
// alt}); app.js is what currently decides WHEN to call it (delegated clicks
// on images inside rendered rich text — see initLightboxDelegation there).
let active = null;

export function closeLightbox() {
    if (active) active.close();
}

export function openLightbox({ src, alt }) {
    closeLightbox();

    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', alt || 'Image preview');

    const img = document.createElement('img');
    img.className = 'lightbox-image';
    img.src = src;
    img.alt = alt || '';

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'lightbox-close';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="3" x2="13" y2="13"/><line x1="13" y1="3" x2="3" y2="13"/></svg>';

    overlay.append(img, closeBtn);
    document.body.appendChild(overlay);
    document.body.classList.add('lightbox-open');

    const previouslyFocused = document.activeElement;
    closeBtn.focus();

    function onKeydown(event) {
        if (event.key === 'Escape') close();
    }

    // Backdrop click closes; a click that lands on the image or the close
    // button itself is handled by their own listener / left alone.
    function onOverlayClick(event) {
        if (event.target === overlay) close();
    }

    function close() {
        document.removeEventListener('keydown', onKeydown);
        overlay.removeEventListener('click', onOverlayClick);
        overlay.remove();
        document.body.classList.remove('lightbox-open');
        if (previouslyFocused && typeof previouslyFocused.focus === 'function') previouslyFocused.focus();
        if (active === api) active = null;
    }

    document.addEventListener('keydown', onKeydown);
    overlay.addEventListener('click', onOverlayClick);
    closeBtn.addEventListener('click', close);

    const api = { close: close };
    active = api;

    return api;
}
