<?php

namespace App\Models;

use App\Models\Concerns\FlushesAccessProfiles;
use App\Support\CompanyCode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

#[Fillable(['name', 'slug', 'code', 'accent_color', 'is_active', 'completed_window_days'])]
class Organization extends Model
{
    // task #72 phase 1: is_active decides visibleOrganizationIds(), which
    // the access profile now caches, so activating or deactivating a
    // company has to drop the cached profiles.
    use FlushesAccessProfiles;

    /** task #72 phase 7: the choices for completed_window_days. */
    public const COMPLETED_WINDOW_OPTIONS = [7, 14, 30];

    public const DEFAULT_COMPLETED_WINDOW_DAYS = 14;

    /**
     * Mirrors the column default, so a company just created through
     * Eloquent already knows its window without a refresh.
     */
    protected $attributes = [
        'completed_window_days' => self::DEFAULT_COMPLETED_WINDOW_DAYS,
        'next_task_number' => 1,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'completed_window_days' => 'integer',
            'next_task_number' => 'integer',
        ];
    }

    /**
     * task #118: a company created without a code gets
     * CompanyCode::suggest()'s code (from its name, next free if taken).
     * The Add Company form requires the code (phase 2); this covers the
     * Excel import and any other code path that creates a company
     * without one.
     */
    protected static function booted(): void
    {
        static::creating(function (Organization $organization) {
            if (blank($organization->code)) {
                $organization->code = CompanyCode::suggest(
                    (string) $organization->name,
                    static::query()->whereNotNull('code')->pluck('code'),
                );
            }
        });
    }

    /**
     * task #118 phase 2: has this company ever given out a task number?
     * Then its code is locked: those codes (BGS-0012) live on in emails
     * and the audit trail. Read from the counter, which only ever goes
     * up, rather than a task count, so a company whose tasks were all
     * deactivated or moved to another company stays locked.
     */
    public function hasIssuedTaskNumbers(): bool
    {
        return $this->next_task_number > 1;
    }

    /**
     * task #118: takes this company's next task number and moves the
     * counter on, so two tasks can never get the same one. Numbers are
     * never handed back, even if the task that took one is never saved
     * or is later deactivated.
     *
     * The counter is incremented first and read after, in one
     * transaction. The UPDATE is what makes this safe: it takes the
     * company row's exclusive lock on MySQL (as SELECT ... FOR UPDATE
     * would) and the write lock on SQLite straight away, held until the
     * transaction ends, so a concurrent create waits instead of reading
     * the same value. The read after it needs no lock of its own.
     * Locking with a read first would deadlock on SQLite (two readers
     * can't both upgrade). The unique (organization_id, task_number)
     * index is the backstop.
     */
    public static function takeNextTaskNumber(int $organizationId): int
    {
        return DB::transaction(function () use ($organizationId) {
            $updated = DB::table('organizations')->where('id', $organizationId)->increment('next_task_number');
            if ($updated === 0) {
                throw new RuntimeException("Company {$organizationId} doesn't exist, so a task in it can't be numbered.");
            }

            return (int) DB::table('organizations')->where('id', $organizationId)->value('next_task_number') - 1;
        });
    }

    /**
     * task #72 phase 7: completed tasks finished before this moment are
     * hidden by default on the working pages — now minus the company's
     * window, in the app timezone (UTC). See Task::scopeOpenOrCompletedWithin().
     */
    public function completedWindowStart(): Carbon
    {
        return now()->subDays($this->completed_window_days ?? self::DEFAULT_COMPLETED_WINDOW_DAYS);
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
