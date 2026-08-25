<?php

namespace App\Services\Import;

use App\Models\ImportBatch;

/**
 * Per-sheet created/updated/unchanged/synced counts plus one-time temp
 * passwords for the post-commit summary screen — the temp passwords are
 * never persisted anywhere after this object is discarded at the end of
 * the request.
 */
class ImportCommitSummary
{
    /** @var array<string, array<string, int>> sheet name => kind => count */
    public array $countsBySheet = [];

    /** @var array<string, string> username => plaintext temp password */
    public array $temporaryPasswords = [];

    public function __construct(public readonly ImportBatch $batch) {}

    public function increment(string $sheetName, string $kind): void
    {
        $this->countsBySheet[$sheetName][$kind] ??= 0;
        $this->countsBySheet[$sheetName][$kind]++;
    }
}
