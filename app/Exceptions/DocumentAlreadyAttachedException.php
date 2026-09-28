<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * task #73 phase 3: thrown by TaskDocumentLinker::attach() instead of
 * letting a repeat attach silently no-op (the old syncWithoutDetaching()
 * behavior) or crash with a raw duplicate-key 500. Callers that route
 * user input through the linker (the picker's attach endpoint) catch this
 * and turn it into a 422; callers attaching a document they just created
 * themselves (chip reconciliation, inline upload/add-link, smart-link
 * auto-attach) can never actually trigger it, since a brand-new document
 * can't already be attached to anything.
 */
class DocumentAlreadyAttachedException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Already attached.');
    }
}
