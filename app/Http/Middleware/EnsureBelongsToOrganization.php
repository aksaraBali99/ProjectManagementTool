<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route-level guard for any route bound to an {organization} parameter.
 * Aborts with 403 unless the current user's visibleOrganizationIds()
 * includes that organization — the same rule BelongsToOrganization
 * enforces at the query level, applied before the controller runs.
 */
class EnsureBelongsToOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $organization = $request->route('organization');

        if ($organization instanceof Organization) {
            $organizationId = $organization->id;
        } else {
            $organizationId = (int) $organization;
        }

        if (! in_array($organizationId, $user->visibleOrganizationIds(), true)) {
            abort(403);
        }

        return $next($request);
    }
}
