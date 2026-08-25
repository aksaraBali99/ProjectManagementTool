<?php

namespace App\Console\Commands;

use App\Enums\ImportBatchStatus;
use App\Models\ImportBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * A batch stuck at pending_review for more than 24h almost certainly
 * means the admin who uploaded it never came back to review/commit it —
 * cleans up its (potentially large) import_rows and the stored .xlsx
 * file, but keeps the batch record itself as a lightweight trace that an
 * upload happened and was abandoned, rather than deleting it outright.
 */
class AbandonStaleImportBatches extends Command
{
    protected $signature = 'imports:abandon-stale';

    protected $description = 'Marks pending_review import batches older than 24h as abandoned and deletes their rows/files.';

    public function handle(): int
    {
        $staleBatches = ImportBatch::where('status', ImportBatchStatus::PendingReview)
            ->where('created_at', '<', now()->subDay())
            ->get();

        foreach ($staleBatches as $batch) {
            $batch->importRows()->delete();

            if ($batch->stored_path) {
                Storage::disk('local')->delete($batch->stored_path);
            }

            $batch->update(['status' => ImportBatchStatus::Abandoned]);
        }

        $this->info(count($staleBatches).' stale import batch(es) abandoned.');

        return self::SUCCESS;
    }
}
