<?php

namespace App\Http\Middleware;

use App\Support\Access\AccessProfileCache;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * task #72 phase 1: starts every request with an empty access-profile
 * cache.
 *
 * The binding is scoped, so in normal serving this is belt-and-braces.
 * It is load-bearing in the test suite and under any long-lived worker
 * (Octane, queue workers), where one application instance serves many
 * requests and roles routinely change in between — without this, request
 * 2 could answer from request 1's profile and a test that grants a role
 * then re-requests would silently see the old answer.
 *
 * Registered first in the global stack so it runs before anything that
 * might ask a permission question.
 */
class FlushAccessProfileCache
{
    public function __construct(private readonly AccessProfileCache $cache) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->cache->flush();

        return $next($request);
    }
}
