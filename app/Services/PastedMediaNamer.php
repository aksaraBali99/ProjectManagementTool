<?php

namespace App\Services;

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\PastedMedia;
use App\Models\Task;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Generates the auto-filename for a clipboard-pasted image/video (task
 * #4, clipboard paste) — {task-title-slug}-{image|video}-{n} — and, for
 * an EXISTING task, atomically resolves `n` against what's actually
 * already been pasted for that task+category (see the pasted_media
 * migration/model). This only ever runs for a paste with no original
 * filename to fall back on; a picker-selected file keeps its real name
 * (or, for image/video today, no name at all beyond its storage key)
 * exactly as before — RichTextImageController/RichTextVideoController
 * never call into this except when the client explicitly flags the
 * upload as a paste.
 *
 * The Add Task page (no task_id yet) never reaches this class at all:
 * with nothing yet persisted to count against and no concurrent-paste
 * risk for a single person drafting a single task, the client computes
 * its own session-scoped n instead (slugifyTaskTitle() in
 * rich-text-editor.js) — this only starts mattering once the task, and
 * so a real row to record pasted_media against, actually exists.
 */
class PastedMediaNamer
{
    /**
     * Locks the task row for the duration of the transaction so two
     * concurrent pastes (two tabs, two people) for the SAME task can
     * never both count the other's paste as "not there yet" and race to
     * the same n — the second request's transaction simply waits for the
     * first one's (count + upload + insert) to commit, then counts again
     * and sees it. A plain "count, then create" with no lock has exactly
     * that race window between the two steps; this closes it at the
     * database level rather than trying to do so in PHP.
     *
     * $upload runs INSIDE this same transaction, between computing the
     * name and recording the pasted_media row — deliberately, not
     * "compute the name, then separately try the upload": the whole
     * point of only ever writing a row on a successful store (see the
     * migration's own docblock) is that a failed upload — an oversized
     * or disallowed-type file, discovered only once
     * FileStorageService::validate() actually runs — must NOT burn a
     * number. Resolving the name first and calling the real upload
     * (which can throw FileStorageException) second, all under one lock,
     * is what makes that guarantee hold; a caller that computed the name
     * up front and uploaded afterward, outside this method, would leak
     * exactly the gap this is built to avoid.
     *
     * @throws FileStorageException whatever $upload itself throws — propagated after
     *                              rolling back the transaction, so no row is left behind
     */
    public function withNextFilename(Task $task, FileCategory $category, ?string $titleAtPasteTime, Closure $upload): StoredFile
    {
        return DB::transaction(function () use ($task, $category, $titleAtPasteTime, $upload) {
            Task::whereKey($task->id)->lockForUpdate()->first();

            $n = PastedMedia::where('task_id', $task->id)
                ->where('file_type', $category->value)
                ->count() + 1;

            $filename = self::buildName($titleAtPasteTime, $category, $n);

            $stored = $upload($filename);

            PastedMedia::create([
                'organization_id' => $task->organization_id,
                'task_id' => $task->id,
                'file_type' => $category->value,
                'filename' => $filename,
            ]);

            return $stored;
        });
    }

    public static function buildName(?string $title, FileCategory $category, int $n): string
    {
        return self::slugifyTitle($title ?? '').'-'.$category->value.'-'.$n;
    }

    /**
     * Mirrored exactly — spaces become hyphens first, then everything
     * else non-alphanumeric is stripped outright (not turned into a
     * hyphen), runs of hyphens collapse to one, and the result is capped
     * at 40 characters — by slugifyTaskTitle() in rich-text-editor.js for
     * the Add Task page's own client-side naming. The two must produce
     * identical output for the same title: which one runs is purely a
     * matter of whether a task_id exists yet, never a user-visible
     * distinction.
     */
    public static function slugifyTitle(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug = mb_substr($slug, 0, 40);
        $slug = trim($slug, '-');

        return $slug === '' ? 'untitled-task' : $slug;
    }
}
