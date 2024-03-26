<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;

class Admin
{
    use UsesAuth;

    public function handle(Request $request, Closure $next)
    {
        if (!self::getAuthGuard()->user()->isAdmin()) {
            return redirect()->route('mixpost.home');
        }

        return $next($request);
    }
}
