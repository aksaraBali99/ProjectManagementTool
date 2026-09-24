<?php

namespace App\Exceptions;

use App\Enums\FileCategory;
use RuntimeException;
use Throwable;

/**
 * Every failure mode FileStorageService can hit, each with a message
 * specific enough to act on — a generic "storage failed" is useless both
 * in a validation-error UI (Phase 2+) and when diagnosing a bad R2
 * credential locally before real credentials exist.
 */
class FileStorageException extends RuntimeException
{
    public static function fileTooLarge(FileCategory $category, int $maxBytes): self
    {
        return new self(sprintf(
            '%s files must be %s or smaller.',
            $category->label(),
            self::formatBytes($maxBytes)
        ));
    }

    public static function disallowedType(FileCategory $category, string $mimeType): self
    {
        return new self(sprintf(
            '"%s" is not an allowed file type for %s uploads.',
            $mimeType,
            $category->label()
        ));
    }

    public static function notFound(string $path): self
    {
        return new self("No file exists at \"{$path}\".");
    }

    public static function uploadFailed(FileCategory $category, Throwable $previous): self
    {
        return new self("Failed to upload {$category->label()} file to storage.", previous: $previous);
    }

    public static function deleteFailed(string $path, Throwable $previous): self
    {
        return new self("Failed to delete file at \"{$path}\" from storage.", previous: $previous);
    }

    public static function moveFailed(string $fromPath, string $toPath, Throwable $previous): self
    {
        return new self("Failed to move file from \"{$fromPath}\" to \"{$toPath}\" in storage.", previous: $previous);
    }

    public static function invalidPendingId(string $pendingId): self
    {
        return new self("\"{$pendingId}\" is not a valid pending-upload id.");
    }

    public static function urlGenerationFailed(string $path, Throwable $previous): self
    {
        return new self("Failed to generate a URL for file at \"{$path}\".", previous: $previous);
    }

    public static function nameCollision(string $path): self
    {
        return new self("A file already exists at \"{$path}\" — try again.");
    }

    private static function formatBytes(int $bytes): string
    {
        return round($bytes / 1_048_576, 1).'MB';
    }
}
