<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * A file under tasks/pending/{id}/... is an image uploaded from the Add
 * Task page's Description editor before the task existed. It's either
 * already gone — reconcilePendingImages() moves it to tasks/{id}/... the
 * moment the task is actually saved — or, if the page was abandoned
 * without saving, it just sits there with nothing that will ever claim it.
 *
 * Anything still under tasks/pending/ after 24h therefore has no
 * corresponding saved task by construction (a save would have moved it
 * out already), so this is a plain age check — no need to cross-reference
 * task rows. Same reasoning and cadence as imports:abandon-stale.
 */
class CleanupStalePendingImages extends Command
{
    protected $signature = 'images:cleanup-stale-pending';

    protected $description = 'Deletes files under tasks/pending/ older than 24h — abandoned Add Task uploads that were never saved.';

    public function handle(): int
    {
        $disk = Storage::disk(config('filestorage.disk'));
        $cutoff = now()->subDay()->timestamp;

        $deleted = 0;

        foreach ($disk->allFiles('tasks/pending') as $path) {
            if ($disk->lastModified($path) < $cutoff) {
                $disk->delete($path);
                $deleted++;
            }
        }

        $this->info("{$deleted} stale pending image(s) deleted.");

        return self::SUCCESS;
    }
}
