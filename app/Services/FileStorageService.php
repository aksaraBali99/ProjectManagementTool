<?php

namespace App\Services;

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Generic wrapper around Laravel's filesystem abstraction for rich-media
 * uploads (images, audio, video) and documents — a thin layer over
 * Storage::disk(), not a reimplementation of an S3 client, since R2 is
 * S3-compatible and Laravel's s3 driver already speaks that protocol
 * (see config/filesystems.php's 'r2' disk).
 *
 * What this class adds on top of the bare Storage facade: per-category
 * size/type validation (config/filestorage.php), a consistent
 * tasks/{task_id}/{category}/ key layout so the single R2 bucket stays
 * organized, and FileStorageException wrapping so a bad credential or an
 * unreachable R2 endpoint fails with a specific, catchable error instead
 * of a bare Flysystem exception or a silently-false return.
 *
 * Every method re-resolves Storage::disk($this->disk) rather than caching
 * the resolved Filesystem instance, so Storage::fake($disk) in tests
 * intercepts correctly regardless of when the fake is set up relative to
 * this service's construction.
 */
class FileStorageService
{
    private readonly string $disk;

    public function __construct(?string $disk = null)
    {
        $this->disk = $disk ?? config('filestorage.disk');
    }

    /**
     * Validates against the category's configured size/type limits, then
     * stores under tasks/{taskId}/{category-prefix}/{uuid}.{extension} —
     * a UUID filename so two uploads with the same original name (e.g.
     * two people both attaching "screenshot.png" to the same task) never
     * collide.
     *
     * @throws FileStorageException file fails validation, or the disk write itself fails
     */
    public function upload(UploadedFile $file, FileCategory $category, int $taskId): StoredFile
    {
        return $this->store($file, $category, (string) $taskId);
    }

    /**
     * The Add Task page's rich-text editor can't key uploads off a real task
     * id — the task doesn't exist yet while it's still being drafted. Those
     * uploads land under tasks/pending/{pendingId}/{category-prefix}/... — a
     * random id the page generates on load (see TaskManagementController's
     * `pendingMediaId` view data), not a database row of any kind — instead
     * of tasks/{taskId}/..., and reconcilePendingFiles() moves them to
     * their real task-scoped path once the task is actually saved. One
     * pendingId is shared across every category the Add Task page's editor
     * uploads (image, audio, ...) — the folder segment right after it
     * (`{category-prefix}/`) is what tells them apart, so a single call to
     * reconcilePendingFiles() moves everything a description/comment
     * references regardless of category.
     *
     * A file that's never reconciled (the Add Task page was abandoned) is
     * swept up by the media:cleanup-stale-pending scheduled command, the
     * same pattern as the Import feature's own stale-batch cleanup.
     *
     * @throws FileStorageException file fails validation, $pendingId isn't a
     *                              well-formed UUID, or the disk write fails
     */
    public function uploadPending(UploadedFile $file, FileCategory $category, string $pendingId): StoredFile
    {
        if (! Str::isUuid($pendingId)) {
            throw FileStorageException::invalidPendingId($pendingId);
        }

        return $this->store($file, $category, "pending/{$pendingId}");
    }

    /**
     * Moves every tasks/pending/{pendingId}/... file this HTML references
     * to its permanent tasks/{taskId}/... path, and rewrites the matching
     * src values to the new URL — called once, right after a new Task row
     * is created, on whatever the submitted description contains. A real
     * R2/S3 move (copy-then-delete server-side), not a re-upload — the
     * browser already has the file, it never sends the bytes again.
     *
     * Generic across every category by construction: the regex matches a
     * pending URL shape (tasks/pending/{id}/{category-prefix}/...), not a
     * specific HTML tag or attribute, so it moves an <img src>, an
     * <audio src>, or any future category's src the exact same way in one
     * pass over the HTML — task #4 phase 4 confirmed this rather than
     * assuming it, see FileStorageServiceTest.
     *
     * Deliberately tolerant of a reference that can't be moved (the pending
     * file was already cleaned up, or the URL was tampered with): that one
     * tag is left pointing at its original URL — which 24h+ later
     * increasingly means a broken embed — rather than failing the whole
     * task save over one bad reference.
     */
    public function reconcilePendingFiles(string $html, int $taskId): string
    {
        if (! str_contains($html, '/tasks/pending/')) {
            return $html;
        }

        // Not read from config directly: url() prepends whatever the disk's
        // *actual* base turns out to be, and in tests that's Storage::fake()'s
        // own "/storage/..." — never the configured R2/MinIO URL — so asking
        // the disk itself (the same call every URL here was built from) is
        // what keeps this matching real URLs in every environment, fake or not.
        $base = rtrim(Storage::disk($this->disk)->url(''), '/');

        $pattern = '#'.preg_quote($base, '#').'/(tasks/pending/([^/"\'<>]+)/([^/"\'<>]+)/[^"\'<>]+)#';

        return preg_replace_callback($pattern, function (array $match) use ($taskId): string {
            [$url, $oldPath, , $prefix] = $match;
            $newPath = "tasks/{$taskId}/{$prefix}/".basename($oldPath);

            try {
                return $this->move($oldPath, $newPath)->url;
            } catch (FileStorageException) {
                return $url;
            }
        }, $html);
    }

    /**
     * @throws FileStorageException the source doesn't exist, or the move itself fails
     */
    public function move(string $fromPath, string $toPath): StoredFile
    {
        try {
            $moved = Storage::disk($this->disk)->move($fromPath, $toPath);
        } catch (Throwable $e) {
            throw FileStorageException::moveFailed($fromPath, $toPath, $e);
        }

        if (! $moved) {
            throw FileStorageException::moveFailed($fromPath, $toPath, new RuntimeException('Storage::move() returned false.'));
        }

        return new StoredFile(path: $toPath, url: $this->url($toPath));
    }

    private function store(UploadedFile $file, FileCategory $category, string $keySegment): StoredFile
    {
        $this->validate($file, $category);

        $path = $this->keyFor($keySegment, $category, strtolower($file->getClientOriginalExtension()));

        try {
            $written = Storage::disk($this->disk)->put($path, fopen($file->getRealPath(), 'r'));
        } catch (Throwable $e) {
            throw FileStorageException::uploadFailed($category, $e);
        }

        if (! $written) {
            throw FileStorageException::uploadFailed($category, new RuntimeException('Storage::put() returned false.'));
        }

        return new StoredFile(path: $path, url: $this->url($path));
    }

    /**
     * @throws FileStorageException no file exists at $path, or URL generation itself fails
     */
    public function url(string $path): string
    {
        $disk = Storage::disk($this->disk);

        if (! $disk->exists($path)) {
            throw FileStorageException::notFound($path);
        }

        try {
            return $disk->url($path);
        } catch (Throwable $e) {
            throw FileStorageException::urlGenerationFailed($path, $e);
        }
    }

    /**
     * A previously-issued url() value is what every caller actually has on
     * hand (a file-chip's saved href, a Document's own `link` column) —
     * not the disk key — so this takes that URL and works backwards to the
     * key, rather than asking callers to also track/pass the raw path.
     *
     * 'inline' disposition (Storage::response(), not ::download()) rather
     * than always forcing a save-as: a browser that can render the type
     * itself (a PDF, an image someone linked as a "document") still shows
     * it in the tab exactly as before, and a type it can't render (docx,
     * xlsx, ...) falls back to its own normal "save this" flow — either
     * way, the filename in the Content-Disposition header this sets is
     * the real original name, not the tasks/{id}/documents/{uuid}.ext key,
     * which is what a plain link to the raw storage URL was always
     * saving the download as.
     *
     * Returns null (not an exception) for a $url that isn't one of this
     * disk's own — an external Smart Link/Document link, e.g. a Google
     * Docs URL — since there's nothing here to stream; the caller falls
     * back to a plain redirect for that case instead.
     */
    public function download(string $url, string $filename): ?StreamedResponse
    {
        $disk = Storage::disk($this->disk);
        $base = rtrim($disk->url(''), '/');

        if (! str_starts_with($url, $base.'/')) {
            return null;
        }

        $path = substr($url, strlen($base) + 1);

        if (! $disk->exists($path)) {
            return null;
        }

        return $disk->response($path, $filename);
    }

    /**
     * @throws FileStorageException the disk delete itself fails
     */
    public function delete(string $path): void
    {
        try {
            Storage::disk($this->disk)->delete($path);
        } catch (Throwable $e) {
            throw FileStorageException::deleteFailed($path, $e);
        }
    }

    /**
     * $keySegment is either a real task id (upload()) or "pending/{uuid}"
     * (uploadPending()) — either way, tasks/{segment}/{category-prefix}/...
     * is the one layout every consumer (real uploads, pending uploads,
     * reconciliation's move target) agrees on.
     */
    private function keyFor(string $keySegment, FileCategory $category, string $extension): string
    {
        return sprintf('tasks/%s/%s/%s.%s', $keySegment, $category->prefix(), (string) Str::uuid(), $extension);
    }

    /**
     * @throws FileStorageException size or type outside the category's configured allow-list
     */
    private function validate(UploadedFile $file, FileCategory $category): void
    {
        $config = $category->config();

        if ($file->getSize() > $config['max_size']) {
            throw FileStorageException::fileTooLarge($category, $config['max_size']);
        }

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($mimeType, $config['mime_types'], true) || ! in_array($extension, $config['extensions'], true)) {
            throw FileStorageException::disallowedType($category, $mimeType ?? $extension);
        }
    }
}
