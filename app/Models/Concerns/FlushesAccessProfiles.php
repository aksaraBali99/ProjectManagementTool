<?php

namespace App\Models\Concerns;

use App\Support\Access\AccessProfileCache;

/**
 * task #72 phase 1: any model whose rows feed an AccessProfile flushes
 * the per-request cache when it changes.
 *
 * Covers Eloquent writes only — saved and deleted fire for create(),
 * update(), updateOrCreate(), firstOrCreate() and $model->delete(), which
 * is how OrgMember and AccessPermission are usually written.
 *
 * It explicitly does NOT cover:
 *   - pivot writes (roles()->sync/attach/detach, permissions()->sync,
 *     clients()->sync) — attaching to a pivot fires no model event
 *   - query-builder mass writes (Model::where(...)->delete()/->update()),
 *     which never load a model and so never fire one either
 *
 * Both kinds call AccessProfileCache::flushAll() explicitly at the call
 * site. See the PR description for the full list; the ones that bite are
 * CompanyRoleSyncer's "none" branch and UserManagementController's
 * department-access replace, since both are mass deletes that would
 * otherwise leave a revoked role or grant live for the rest of the
 * request.
 */
trait FlushesAccessProfiles
{
    public static function bootFlushesAccessProfiles(): void
    {
        static::saved(fn () => AccessProfileCache::flushAll());
        static::deleted(fn () => AccessProfileCache::flushAll());
    }
}
