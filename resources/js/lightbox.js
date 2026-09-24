// Reusable click-to-enlarge overlay — not coupled to rich-text images.
// Anything the app wants to open full-size just calls
// openLightbox({type, src, alt}); app.js is what currently decides WHEN to
// call it (delegated clicks on images/video thumbnails inside rendered
// rich text — see initLightboxDelegation there).
//
// type: 'image' (default, backward compatible with every existing caller
// that never passed one) or 'video' (task #4, video resize + lightbox) —
// extended in place rather than duplicated into a second overlay
// implementation, per that task's explicit instruction: the close button,
// backdrop click, Escape handling and focus management below are already
// completely generic (never touched an <img> specifically), so only the
// content element itself needed a branch.
let active = null;

export function closeLightbox() {
    if (active) active.close();
}

export function openLightbox({ type, src, alt }) {
    closeLightbox();

    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', alt || (type === 'video' ? 'Video preview' : 'Image preview'));

    // Always a fresh, full-size <video controls> — regardless of how small
    // the inline thumbnail that was clicked had been resized to (task #4:
    // the resized dimensions only ever apply to the inline preview, never
    // to this expanded view, which is always sized by .lightbox-video's
    // own CSS cap). autoplay is a direct result of the click that opened
    // this, not unsolicited, so it's exempt from the general
    // don't-autoplay-media caution elsewhere in this app.
    const content = type === 'video'
        ? Object.assign(document.createElement('video'), {
            className: 'lightbox-video',
            src: src,
            controls: true,
            autoplay: true,
            playsInline: true,
        })
        : Object.assign(document.createElement('img'), {
            className: 'lightbox-image',
            src: src,
            alt: alt || '',
        });

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'lightbox-close';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><line x1="3" y1="3" x2="13" y2="13"/><line x1="13" y1="3" x2="3" y2="13"/></svg>';

    overlay.append(content, closeBtn);
    document.body.appendChild(overlay);
    document.body.classList.add('lightbox-open');

    const previouslyFocused = document.activeElement;
    closeBtn.focus();

    function onKeydown(event) {
        if (event.key === 'Escape') close();
    }

    // Backdrop click closes; a click that lands on the content or the close
    // button itself is handled by their own listener / left alone.
    function onOverlayClick(event) {
        if (event.target === overlay) close();
    }

    function close() {
        document.removeEventListener('keydown', onKeydown);
        overlay.removeEventListener('click', onOverlayClick);
        // A <video> kept playing detached from the document would keep
        // decoding/emitting audio in some browsers until GC'd — an <img>
        // has no such lifecycle, so this is a no-op for the image case.
        if (typeof content.pause === 'function') content.pause();
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
