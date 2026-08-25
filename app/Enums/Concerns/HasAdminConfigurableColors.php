<?php

namespace App\Enums\Concerns;

use Illuminate\Support\Facades\Cache;

/**
 * DB-cached badge-color lookup, shared by every enum with an
 * admin-configurable color table (the Status & Priority Colors settings
 * page) — TaskStatus and Priority each had this ~40-line implementation
 * independently before extraction. The implementing enum supplies
 * colorModel()/colorCacheKey()/colorColumn() (statics) and fallbackColors()
 * (an instance match, for the unlikely case a case has no DB row at all).
 *
 * Cached as a plain array, deliberately not the Eloquent models/Collection
 * themselves — the 'database' cache driver serializes whatever it's given,
 * and unserializing a cached model instance can come back as
 * __PHP_Incomplete_Class if the model class isn't already resolvable at
 * that exact moment (autoloader/classmap state at request time — cheap to
 * hit in practice, e.g. right after a deploy). A plain array of strings has
 * no class to resolve, so this can't happen.
 */
trait HasAdminConfigurableColors
{
    public function badgeBackground(): string
    {
        return self::colorRow($this)['background_color'] ?? $this->fallbackColors()['background_color'];
    }

    public function badgeText(): string
    {
        return self::colorRow($this)['text_color'] ?? $this->fallbackColors()['text_color'];
    }

    /**
     * @return array{background_color: string, text_color: string}|null
     */
    private static function colorRow(self $case): ?array
    {
        return self::allColors()[$case->value] ?? null;
    }

    /**
     * @return array<string, array{background_color: string, text_color: string}>
     */
    private static function allColors(): array
    {
        $modelClass = self::colorModel();
        $column = self::colorColumn();

        return Cache::rememberForever(
            self::colorCacheKey(),
            fn () => $modelClass::query()
                ->get([$column, 'background_color', 'text_color'])
                ->keyBy($column)
                ->map(fn ($color) => [
                    'background_color' => $color->background_color,
                    'text_color' => $color->text_color,
                ])
                ->all()
        );
    }

    /**
     * Called from the Status & Priority Colors settings page after saving
     * — badge colors are cached forever (not TTL-based), so nothing else
     * ever clears this; every screen that renders a badge would otherwise
     * keep serving stale colors until the cache store itself expires or is
     * flushed.
     */
    public static function forgetColorCache(): void
    {
        Cache::forget(self::colorCacheKey());
    }
}
