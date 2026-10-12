<?php

namespace App\Support\Access;

use App\Models\User;

/**
 * task #72 phase 1: holds one AccessProfile per user id for the life of a
 * request, so every role/permission/membership/department question costs
 * at most one load per user.
 *
 * Keyed by user id rather than kept on the User instance deliberately:
 * a single request routinely has several User objects for the same person
 * (auth()->user(), $task->assignee, $comment->user), and they must share
 * one profile or the saving largely disappears.
 *
 * Registered as a scoped binding, flushed at the start of every HTTP
 * request by FlushAccessProfileCache and between queued jobs, so a
 * profile can never outlive the request that built it. That matters most
 * in the test suite, where many requests share one application instance
 * and roles change between them.
 */
class AccessProfileCache
{
    /** @var array<int, AccessProfile> */
    private array $profiles = [];

    public static function enabled(): bool
    {
        return (bool) config('solava.access_cache', true);
    }

    public function for(User $user): AccessProfile
    {
        $id = (int) $user->getKey();

        return $this->profiles[$id] ??= AccessProfile::load($id);
    }

    /**
     * Drops one user's profile, or everything when no user is given.
     *
     * Called whenever anything the profiles are built from changes. It is
     * deliberately blunt: flushing too often only costs a reload, while
     * missing a flush would serve a stale permission — so every write
     * path that cannot be covered by a model event calls flush() with no
     * argument rather than trying to work out whose profile is affected.
     */
    public function flush(?int $userId = null): void
    {
        if ($userId === null) {
            $this->profiles = [];

            return;
        }

        unset($this->profiles[$userId]);
    }

    /**
     * Convenience for call sites that are not already resolving the cache
     * — pivot writes, query-builder mass deletes, seeders and commands.
     */
    public static function flushAll(): void
    {
        if (app()->resolved(self::class)) {
            app(self::class)->flush();
        }
    }
}
