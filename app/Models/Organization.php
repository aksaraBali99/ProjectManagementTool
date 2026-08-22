<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'accent_color', 'is_active'])]
class Organization extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Internal, code-facing identifier — never user-editable. Derived from
     * the name at creation time and never regenerated afterward, even if
     * the name changes later, since the slug may already be referenced
     * elsewhere (URLs, cached views) and silently changing it on a rename
     * could break existing links.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'org_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function orgMembers(): HasMany
    {
        return $this->hasMany(OrgMember::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function accessPermissions(): HasMany
    {
        return $this->hasMany(AccessPermission::class);
    }

    /**
     * Full pill treatment (background/text) per the UIX spec's Company
     * colour table. The 3 named companies get the spec's exact hand-picked
     * pair; any other accent_color (a future 4th+ company, or any of these
     * 3 repainted via the colour picker on the Organizations edit form) is
     * derived algorithmically from the accent itself, so picking a new
     * colour is reflected immediately everywhere a company pill renders —
     * not just for the 3 hardcoded pairs. Comparison is case-insensitive
     * since <input type="color"> always submits lowercase hex.
     */
    public function badgeBackground(): string
    {
        if (! $this->accent_color) {
            return '#F3F4F6';
        }

        return match (strtoupper($this->accent_color)) {
            '#1D9E75' => '#E1F5EE',
            '#534AB7' => '#EEEDFE',
            '#BA7517' => '#FAEEDA',
            default => self::mixWithWhite($this->accent_color, 0.85),
        };
    }

    public function badgeText(): string
    {
        if (! $this->accent_color) {
            return '#374151';
        }

        return match (strtoupper($this->accent_color)) {
            '#1D9E75' => '#0F6E56',
            '#534AB7' => '#3C3489',
            '#BA7517' => '#854F0B',
            default => self::mixWithBlack($this->accent_color, 0.35),
        };
    }

    private static function mixWithWhite(string $hex, float $amount): string
    {
        [$r, $g, $b] = self::hexToRgb($hex);

        return self::rgbToHex(
            $r + (255 - $r) * $amount,
            $g + (255 - $g) * $amount,
            $b + (255 - $b) * $amount,
        );
    }

    private static function mixWithBlack(string $hex, float $amount): string
    {
        [$r, $g, $b] = self::hexToRgb($hex);

        return self::rgbToHex($r * (1 - $amount), $g * (1 - $amount), $b * (1 - $amount));
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private static function rgbToHex(float $r, float $g, float $b): string
    {
        $clamp = fn (float $value) => max(0, min(255, (int) round($value)));

        return sprintf('#%02X%02X%02X', $clamp($r), $clamp($g), $clamp($b));
    }
}
