<?php

namespace App\Services;

/**
 * What FileStorageService::upload() hands back — the disk key (needed
 * later to delete() or re-derive a URL) alongside the publicly
 * accessible URL a Phase 2+ caller embeds directly.
 */
final class StoredFile
{
    public function __construct(
        public readonly string $path,
        public readonly string $url,
    ) {}
}
