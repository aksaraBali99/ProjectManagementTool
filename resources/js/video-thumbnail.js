// Turns a plain, server-rendered <video src ... [width height]> inside
// read-only rich text (task drilldown, comments, Description's view mode —
// anywhere carrying [data-rich-text-content]) into the same click-to-expand
// thumbnail the live editor already shows (resizable-video.js) — task #4,
// video resize + lightbox. Dynamically imported and only run when there's
// actually a <video> to enhance, same lazy-load shape as code-highlight.js
// for syntax highlighting.
//
// Deliberately doesn't decide what a click DOES — app.js's
// initLightboxDelegation is the single place that opens the lightbox, for
// both images and video thumbnails, exactly as it already owned that
// decision for images before this feature existed. This module only ever
// builds the DOM; [data-video-enhanced] marks a <video> already wrapped, so
// re-running this over content that hasn't changed (the 5-10s comment poll
// re-invokes it on every row, changed or not) is a no-op past the first
// pass, the same guard code-highlight.js uses for [data-highlighted].
export function enhanceEmbeddedVideos(scope) {
    const target = scope || document;

    target.querySelectorAll('[data-rich-text-content] video:not([data-video-enhanced])').forEach(function (video) {
        video.setAttribute('data-video-enhanced', '');
        video.removeAttribute('controls');
        video.muted = true;
        video.preload = 'metadata';
        video.tabIndex = -1;

        const width = video.getAttribute('width');
        const height = video.getAttribute('height');

        const thumb = document.createElement('div');
        thumb.className = 'video-thumb';
        if (width && height) {
            thumb.setAttribute('data-has-size', '');
            thumb.style.width = width + 'px';
            thumb.style.height = height + 'px';
        }

        const playIcon = document.createElement('div');
        playIcon.className = 'video-thumb-play';
        playIcon.setAttribute('aria-hidden', 'true');
        playIcon.innerHTML = '<span class="video-thumb-play-icon"><svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.5 3.2v9.6a.6.6 0 0 0 .92.5l7.4-4.8a.6.6 0 0 0 0-1l-7.4-4.8a.6.6 0 0 0-.92.5z"/></svg></span>';

        // The browser's own first frame, not a generated poster — same
        // small nudge-forward trick as resizable-video.js's editor
        // NodeView, kept identical so the two paths look the same.
        video.addEventListener('loadeddata', function onLoadedData() {
            video.removeEventListener('loadeddata', onLoadedData);
            if (video.currentTime === 0 && video.duration > 0.1) {
                try {
                    video.currentTime = Math.min(0.1, video.duration / 2);
                } catch {
                    // Not yet seekable in some browser/codec combination —
                    // the thumbnail just stays on frame 0.
                }
            }
        });

        video.parentNode.insertBefore(thumb, video);
        thumb.append(video, playIcon);
    });
}
