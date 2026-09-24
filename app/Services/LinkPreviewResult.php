<?php

namespace App\Services;

/**
 * What LinkPreviewService::resolve() hands back on success — everything
 * the compact link-preview chip needs to render (icon + title + domain,
 * no thumbnail — see link-preview-extension.js), baked in as a snapshot
 * at the moment of resolution (task #4, Smart Links: cache at paste-time,
 * not per-viewer — see the link_previews migration's own docblock).
 */
final class LinkPreviewResult
{
    public function __construct(
        public readonly string $url,
        public readonly string $title,
        public readonly string $domain,
    ) {}
}
