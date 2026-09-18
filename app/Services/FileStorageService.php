<?php

namespace App\Services;

use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
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
        $this->validate($file, $category);

        $path = $this->keyFor($taskId, $category, strtolower($file->getClientOriginalExtension()));

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

    private function keyFor(int $taskId, FileCategory $category, string $extension): string
    {
        return sprintf('tasks/%d/%s/%s.%s', $taskId, $category->prefix(), (string) Str::uuid(), $extension);
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
