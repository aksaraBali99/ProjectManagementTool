<?php

/**
 * task #4 follow-up: cleaner resize handle styling for embedded images and
 * video. Pest has no JS runner and can't render CSS visually — see
 * EmojiSupportTest's own shared-component tests for the same established
 * pattern this follows: pin the actual source rules down so a regression
 * can't silently reintroduce the old look, with the real "does this look
 * right" check done in a real browser (see the PR description).
 *
 * Deliberately ONE set of assertions, not an image-specific and a video-
 * specific one: resizable-image.js and resizable-video.js both build on
 * the exact same @tiptap/core ResizableNodeView, attaching the identical
 * [data-resize-container]/[data-resize-wrapper]/[data-resize-handle]
 * structure either way — the CSS selectors here are generic attribute
 * selectors with nothing image- or video-specific about them, so the same
 * rules already apply to both consistently. That consistency is itself
 * the thing worth asserting, alongside the actual style values.
 */
test('the old thick, solid, full-perimeter bright-green resize handle styling is gone', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    // The specific combination that produced the old look: a "bottom"/
    // "right" edge handle spanning the full width/height (ResizableNodeView's
    // own default), filled solid with the saturated brand color and no
    // fixed small size of its own.
    expect($css)->not->toContain('background: var(--color-brand-600);
    border: 1.5px solid #fff;');
});

test('resize handles are small 8x8px squares, not full-width/height bars, on both image and video embeds', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    // One generic [data-resize-handle] rule sizes every handle at 8x8 —
    // applies identically whether the selected node is an image or a
    // video, since neither resizable-image.js nor resizable-video.js
    // attaches any node-type-specific class here.
    expect($css)->toContain('[data-resize-handle] {')
        ->and($css)->toContain('width: 8px;')
        ->and($css)->toContain('height: 8px;');

    // The "bottom"/"right" edge handles no longer span the full edge —
    // ResizableNodeView sets left:0;right:0 (or top:0;bottom:0) inline by
    // default to do that; overriding it to center a small square on the
    // edge's midpoint instead requires beating that inline style.
    expect($css)->toContain('[data-resize-handle="bottom"] {')
        ->and($css)->toContain('left: 50% !important;')
        ->and($css)->toContain('[data-resize-handle="right"] {')
        ->and($css)->toContain('top: 50% !important;');
});

test('the resize handle color is muted (a thin brand-colored border on a white fill), not a solid saturated fill', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    preg_match('/\[data-resize-handle\]\s*\{([^}]*)\}/', $css, $match);
    expect($match)->not->toBeEmpty();
    $rule = $match[1];

    expect($rule)->toContain('background: #fff;')
        ->and($rule)->toContain('border: 1px solid var(--color-brand-600);');
});

test('a subtle hairline outline (not a bold border) surrounds the whole selected image/video, using outline rather than border so it never shifts layout', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    preg_match('/\.ProseMirror-selectednode\[data-resize-container\]\s*\[data-resize-wrapper\]\s*\{([^}]*)\}/', $css, $match);
    expect($match)->not->toBeEmpty();
    $rule = $match[1];

    expect($rule)->toContain('outline: 1px solid');
    expect($rule)->not->toContain('border:');
});

test('resizable-image.js and resizable-video.js both build on the same ResizableNodeView, which is what makes one set of handle styles apply to both consistently', function () {
    $imageSource = file_get_contents(resource_path('js/resizable-image.js'));
    $videoSource = file_get_contents(resource_path('js/resizable-video.js'));

    expect($imageSource)->toContain("from '@tiptap/core'")->toContain('ResizableNodeView');
    expect($videoSource)->toContain("from '@tiptap/core'")->toContain('ResizableNodeView');
});
