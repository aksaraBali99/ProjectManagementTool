<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Organization;
use Illuminate\Support\Collection;

/**
 * Every company-scoped page resolves its "current" company the same way —
 * fall back to the first org in the caller's already-visibility/active-
 * filtered collection if none was requested, or the requested one isn't in
 * that set (deactivated, or simply not visible to this user on this page).
 * The organizations query itself and the empty-state view payload
 * genuinely differ per controller, so only this shared resolution step is
 * extracted rather than the whole block.
 */
trait ResolvesCurrentOrganization
{
    protected function resolveCurrentOrganization(Collection $organizations, ?Organization $organization): Organization
    {
        if (! $organization || ! $organizations->contains('id', $organization->id)) {
            return $organizations->first();
        }

        return $organization;
    }
}
