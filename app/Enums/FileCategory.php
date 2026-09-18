<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum FileCategory: string
{
    use HasEnumValues;

    case Image = 'image';
    case Audio = 'audio';
    case Video = 'video';
    case Document = 'document';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Audio => 'Audio',
            self::Video => 'Video',
            self::Document => 'Document',
        };
    }

    /**
     * The config/filestorage.php entry for this category — max_size (bytes),
     * mime_types, extensions, and the key-prefix folder segment.
     *
     * @return array{prefix: string, max_size: int, mime_types: array<int, string>, extensions: array<int, string>}
     */
    public function config(): array
    {
        return config("filestorage.categories.{$this->value}");
    }

    /**
     * The folder segment under tasks/{task_id}/{prefix}/ — see
     * FileStorageService::keyFor().
     */
    public function prefix(): string
    {
        return $this->config()['prefix'];
    }
}
