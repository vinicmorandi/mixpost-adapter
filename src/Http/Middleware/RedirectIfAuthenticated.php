<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    use UsesAuth;

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [self::getAuthGuardName()] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(route('mixpost.home'));
            }
        }

        return $next($request);
    }
}
