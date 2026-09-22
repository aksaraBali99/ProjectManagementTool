<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * A file under tasks/pending/{id}/... is a rich-text upload — image, audio,
 * or any future category (task #4 phase 4 confirmed reconcilePendingFiles()
 * and this sweep are both already generic across categories, not
 * image-specific, so this file didn't need to change when audio was added
 * — only its name did, from CleanupStalePendingImages) — from the Add Task
 * page's Description editor, uploaded before the task existed. It's either
 * already gone — FileStorageService::reconcilePendingFiles() moves it to
 * tasks/{id}/... the moment the task is actually saved — or, if the page
 * was abandoned without saving, it just sits there with nothing that will
 * ever claim it.
 *
 * Anything still under tasks/pending/ after 24h therefore has no
 * corresponding saved task by construction (a save would have moved it
 * out already), so this is a plain age check — no need to cross-reference
 * task rows, or to know which category a given file belongs to. Same
 * reasoning and cadence as imports:abandon-stale.
 */
class CleanupStalePendingMedia extends Command
{
    protected $signature = 'media:cleanup-stale-pending';

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

        $this->info("{$deleted} stale pending file(s) deleted.");

        return self::SUCCESS;
    }
}
