<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * task #73 phase 4: thrown by TaskDocumentLinker::attachFolder() instead of
 * letting a repeat attach silently no-op or crash with a raw duplicate-key
 * 500 — same reasoning as DocumentAlreadyAttachedException, its file-attach
 * sibling.
 */
class FolderAlreadyLinkedException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Already attached.');
    }
}
