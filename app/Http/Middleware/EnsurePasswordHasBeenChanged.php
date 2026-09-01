<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces `users.must_change_password` — set by
 * `solva:bootstrap --reset-owner` (a CLI recovery reset, run outside any
 * web session) so the fresh password it sets is only ever a bridge to a
 * password the affected user actually chose themselves. Reuses the
 * existing Change Password popup on the user's own edit page rather than
 * a dedicated screen — everything else is redirected there until the
 * flag clears.
 */
class EnsurePasswordHasBeenChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->must_change_password) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName === 'logout') {
            return $next($request);
        }

        $routeUser = $request->route('user');
        $targetsSelf = $routeUser instanceof User && $routeUser->is($user);

        if ($targetsSelf && in_array($routeName, ['users.edit', 'users.password.update'], true)) {
            return $next($request);
        }

        return redirect()->route('users.edit', $user)
            ->with('status', 'Your password was reset — set a new one before continuing.');
    }
}
