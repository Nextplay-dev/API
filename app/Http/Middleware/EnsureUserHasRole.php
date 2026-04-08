<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()?->hasAnyRole(...$roles)) {
            abort(403, "You don't have permission to perform this action.");
        }

        return $next($request);
    }
}
